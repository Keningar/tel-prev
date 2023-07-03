var arrayRegistrosSeleccionados             = [];
var intContAgreg                            = 0;
Ext.onReady(function() {
    //array que contiene el titulo de las ventanas de mensajes   
    Ext.define('estados', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'opcion', type: 'string'},
            {name: 'valor', type: 'string'}
        ]
    });
    
    Ext.define('Asignacion', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdElemento', mapping: 'intIdElemento'},
            {name: 'strNombreElemento', mapping: 'strNombreElemento'},
            {name: 'strMarcaNombre', mapping: 'strMarcaNombre'},
            {name: 'strNombrePlan', mapping: 'strNombrePlan'},
            {name: 'strEstado', mapping: 'strEstado'},
            {name: 'strLogin', mapping: 'strLogin'},
            {name: 'intIdServicio', mapping: 'intIdServicio'},
            //{name: 'strValor', mapping: 'strValor'},
            {name: 'strModeloNombre', mapping: 'strModeloNombre'},
            {name: 'intIdSolicitud', mapping: 'intIdSolicitud'}
        ]
    });
    
    var storeOlt = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: urlListadoOlt,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'}
            ]
    });
    
    var storeModelo = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: '../../../administracion/tecnico/admi_modelo_elemento/getEncontrados',
            extraParams: {
                tipoElemento: 'CPE ONT',
                estado: 'Activo'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idModeloElemento', mapping: 'idModeloElemento'},    
                {name: 'nombreModeloElemento', mapping: 'nombreModeloElemento'}
            ]
    });
    
    var storeModeloAsig = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: '../../../administracion/tecnico/admi_modelo_elemento/getEncontrados',
            extraParams: {
                tipoElemento: 'CPE ONT',
                estado: 'Activo'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idModeloElemento', mapping: 'idModeloElemento'},    
                {name: 'nombreModeloElemento', mapping: 'nombreModeloElemento'}
            ]
    });
    
    var storePlan = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: urlListadoPlanes,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombre_plan', mapping: 'nombre_plan'},
                {name: 'id_plan', mapping: 'id_plan'}
            ]
    });
    
    store = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 900000,
            url: urlOltGridSolAgregarEquipo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                modeloElemento: '',
                planNombre: '',
                modeloElementoAsig: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'intIdElemento', mapping: 'intIdElemento'},
                {name: 'strNombreElemento', mapping: 'strNombreElemento'},
                {name: 'strMarcaNombre', mapping: 'strMarcaNombre'},
                {name: 'strNombrePlan', mapping: 'strNombrePlan'},
                {name: 'strEstado', mapping: 'strEstado'},
                {name: 'strLogin', mapping: 'strLogin'},
                {name: 'intIdServicio', mapping: 'intIdServicio'},
                //{name: 'strValor', mapping: 'strValor'},
                {name: 'strModeloNombre', mapping: 'strModeloNombre'},
                {name: 'intIdSolicitud', mapping: 'intIdSolicitud'}
            ]
    });
    
    SelectDataOltAgregarSolEquipo = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 900000,
            url: gridAsignaciones,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root:'asignado'
            },
            extraParams: {
                arrayDataJson: ''
            }
        },
        fields:
            [
                {name: 'intIdElemento', mapping: 'intIdElemento'},
                {name: 'strNombreElemento', mapping: 'strNombreElemento'},
                {name: 'strMarcaNombre', mapping: 'strMarcaNombre'},
                {name: 'strNombrePlan', mapping: 'strNombrePlan'},
                {name: 'strEstado', mapping: 'strEstado'},
                {name: 'strLogin', mapping: 'strLogin'},
                {name: 'intIdServicio', mapping: 'intIdServicio'},
                //{name: 'strValor', mapping: 'strValor'},
                {name: 'strModeloNombre', mapping: 'strModeloNombre'},
                {name: 'intIdSolicitud', mapping: 'intIdSolicitud'}
            ]
    });

    /*****Arreglo de Check de seleccion******/
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });
    
    sm2 = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true,
        listeners: {
            selectionchange: function(sm, selections) {
                gridAsignaciones.down('#removeButton').setDisabled(selections.length == 0);
            }
        }
    });

    grid = Ext.create('Ext.grid.Panel', {
        width: 750,
        height: 350,
        store: store,
        loadMask: true,
        selModel: sm,
        viewConfig: {enableTextSelection: true},
        iconCls: 'icon-grid',
        dockedItems: [ 
                        {
                            xtype: 'toolbar',
                            dock: 'top',
                            align: '->',
                            items: [
                                {
                                    iconCls: 'icon_add',
                                    text: 'Agregar a la lista',
                                    disabled: false,
                                    itemId: 'idagregar',
                                    scope: this,
                                    handler: function(){listaOltAgregarSolEquipo()}
                                }
                            ]
                        }
                    ],
        columns: [
            {
                id: 'idElemento',
                header: 'idElemento',
                dataIndex: 'intIdElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'ipElemento',
                header: 'Olt',
                xtype: 'templatecolumn',
                width: 240,
                tpl: '<span class="box-detalle">{strNombreElemento}</span>\n\ '
            },
            {
                header: 'Modelo CPE',
                dataIndex: 'strModeloNombre',
                width: 120,
                sortable: true
            },
            /*{
                header: 'Valor',
                dataIndex: 'strValor',
                hidden: true,
                hideable: false
            },//*/
            {
                header: 'Plan',
                dataIndex: 'strNombrePlan',
                width: 180,
                sortable: true
            },
            {
                id: 'strLogin',
                header: 'Login',
                dataIndex: 'strLogin',
                width: 160,
                sortable: true
            },
            {
                id: 'intIdServicio',
                header: 'intIdServicio',
                dataIndex: 'intIdServicio',
                hidden: true,
                hideable: false
            },
            {
                id: 'strEstado',
                header: 'strEstado',
                dataIndex: 'strEstado',
                hidden: true,
                hideable: false
            },
            {
                id: 'intIdSolicitud',
                header: 'intIdSolicitud',
                dataIndex: 'intIdSolicitud',
                hidden: true,
                hideable: false
            }
        ],
        bbar:[
                {
                    text: 'Total de Solicitudes:',
                    disabled: false
                },
                {
                    xtype: 'textfield',
                    id: 'contadorOltsTotal',
                    name: 'contadorOltsTotal',
                    value: 0,
                    readOnly: true,
                    width: 40,
                    listeners: {
                        render: function(b) {
                            b.inputEl.setStyle('text-align', 'center');
                        }
                    }
                }
        ],
        renderTo: 'grid'
    });
    
    gridAsignaciones = Ext.create('Ext.grid.Panel', {
        width: 750,
        height: 350,
        store: SelectDataOltAgregarSolEquipo,
        loadMask: true,
        selModel: sm2,
        iconCls: 'icon-grid',
        columns: [
            {
                header: 'idElemento',
                dataIndex: 'intIdElemento',
                hidden: true,
                hideable: false
            },
            {
                header: 'Olt',
                xtype: 'templatecolumn',
                width: 240,
                tpl: '<span class="box-detalle">{strNombreElemento}</span>\n\ '
            },
            {
                header: 'Modelo CPE',
                dataIndex: 'strModeloNombre',
                width: 120,
                sortable: true
            },
            /*{
                header: 'Valor',
                dataIndex: 'strValor',
                hidden: true,
                hideable: false
            },//*/
            {
                header: 'Plan',
                dataIndex: 'strNombrePlan',
                width: 180,
                sortable: true
            },
            {
                header: 'Login',
                dataIndex: 'strLogin',
                width: 160,
                sortable: true
            },
            {
                id: 'Plan',
                header: 'intIdServicio',
                dataIndex: 'intIdServicio',
                hidden: true,
                hideable: false
            },
            {
                id: 'Cliente',
                header: 'strEstado',
                dataIndex: 'strEstado',
                hidden: true,
                hideable: false
            },
            {
                id: 'Valor',
                header: 'intIdSolicitud',
                dataIndex: 'intIdSolicitud',
                hidden: true,
                hideable: false
            }
        ],
        dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                                itemId: 'removeButton',
                                text: 'Eliminar',
                                tooltip: 'Elimina los items seleccionado',
                                iconCls: 'remove',
                                scope: this,
                                disabled: true,
                                handler: function() {
                                    eliminarSeleccion(gridAsignaciones);
                                    }
                                },
                                { xtype: 'tbfill' },
                                {
                                    id: 'sltModeloAsig',
                                    fieldLabel: 'Modelo CPE',
                                    xtype: 'combobox',
                                    store: storeModeloAsig,
                                    displayField: 'nombreModeloElemento',
                                    valueField: 'nombreModeloElemento',
                                    loadingText: 'Buscando ...',
                                    queryMode: 'local',
                                    listClass: 'x-combo-list-small',
                                    width: '30%'
                                }]
                    }],
        title: 'Registros Seleccionados',
        bbar:[
                {
                    text: 'Total :',
                    disabled: false
                },
                {
                    xtype: 'textfield',
                    id: 'contadorOltsAgregados',
                    name: 'contadorOltsAgregados',
                    value: 0,
                    readOnly: true,
                    width: 40,
                    listeners: {
                        render: function(c) {
                            c.inputEl.setStyle('text-align', 'center');
                        }
                    } 
                }
        ],
        renderTo: 'gridAsignaciones'
    });
    
    filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 750,
        title: 'Criterios de busqueda',
        buttons: [
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
        items: [
            {width: '10%', border: false},
            {
                id: 'sltOlt',
                fieldLabel: 'Olt',
                xtype: 'combobox',
                store: storeOlt,
                displayField: 'nombreElemento',
                valueField: 'idElemento',
                loadingText: 'Buscando ...',
                queryMode: 'local',
                listClass: 'x-combo-list-small',
                width: '300px'
            },
            {width: '20%', border: false},
            {
                id: 'sltModelo',
                fieldLabel: 'Modelo CPE',
                xtype: 'combobox',
                store: storeModelo,
                displayField: 'nombreModeloElemento',
                valueField: 'idModeloElemento',
                loadingText: 'Buscando ...',
                queryMode: 'local',
                listClass: 'x-combo-list-small',
                width: '30%'
            },
            {width: '10%', border: false},
            {width: '10%', border: false},
            {
                xtype: 'combobox',
                id: 'sltPlan',
                fieldLabel: 'Plan',
                displayField: 'nombre_plan',
                valueField: 'id_plan',
                loadingText: 'Buscando ...',
                store: storePlan,
                listClass: 'x-combo-list-small',
                queryMode: 'local',
                width: '78%'
            },
            {width: '20%', border: false},
            {width: '10%', border: false}
        ],
        renderTo: 'filtro'
    });

    store.load({
        callback: function() 
        {
            storeOlt.load(
            {
                callback: function() 
                {
                    storeModelo.load(
                    {
                        callback: function() 
                        {
                            storePlan.load(
                            {
                                callback: function() 
                                {
                                    storeModeloAsig.load(
                                    {
                                        callback: function() 
                                        {
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
                    
            });
            Ext.getCmp('contadorOltsTotal').setValue(store.getTotalCount());
            Ext.getCmp('contadorOltsTotal').setRawValue(store.getTotalCount());  
            Ext.getCmp('contadorOltsTotal').show();
        }
       
    });
});

function buscar() {
    store.getProxy().extraParams.nombreElemento     = Ext.getCmp('sltOlt').value;
    store.getProxy().extraParams.modeloElemento     = Ext.getCmp('sltModelo').value;
    store.getProxy().extraParams.planNombre         = Ext.getCmp('sltPlan').value;
    store.load({
        callback: function()
        {
            Ext.getCmp('contadorOltsTotal').setValue(store.getTotalCount());
            Ext.getCmp('contadorOltsTotal').setRawValue(store.getTotalCount());
        }
    });
}

function limpiar() {
    Ext.getCmp('sltOlt').value = "";
    Ext.getCmp('sltOlt').setRawValue("");
    
    Ext.getCmp('sltModelo').value = "";
    Ext.getCmp('sltModelo').setRawValue("");

    Ext.getCmp('sltPlan').value = "";
    Ext.getCmp('sltPlan').setRawValue("");

    store.getProxy().extraParams.nombreElemento = Ext.getCmp('sltOlt').value;
    store.getProxy().extraParams.modeloElemento = Ext.getCmp('sltModelo').value;
    store.getProxy().extraParams.planNombre     = Ext.getCmp('sltPlan').value;
    store.load({
        callback: function()
        {
            Ext.getCmp('contadorOltsTotal').setValue(store.getTotalCount());
            Ext.getCmp('contadorOltsTotal').setRawValue(store.getTotalCount());
        }
    });
}

function listaOltAgregarSolEquipo()
{    
    if (sm.getSelection().length > 0)
    {
        for (var i = 0; i < sm.getSelection().length; ++i)
        {
            if((intContAgreg+1) <= cantidadMaximaOlts)
            {
                var r = Ext.create('Asignacion', {
                    intIdElemento: sm.getSelection()[i].get('intIdElemento'),
                    strNombreElemento: sm.getSelection()[i].get('strNombreElemento'),
                    strNombrePlan: sm.getSelection()[i].get('strNombrePlan'),
                    //strValor:sm.getSelection()[i].get('strValor'),
                    strLogin:sm.getSelection()[i].get('strLogin'),
                    intIdServicio:sm.getSelection()[i].get('intIdServicio'),
                    strEstado: sm.getSelection()[i].get('strEstado'),
                    strModeloNombre: sm.getSelection()[i].get('strModeloNombre'),
                    intIdSolicitud:sm.getSelection()[i].get('intIdSolicitud')
                });
            
                if (!existeAsignacion(r, gridAsignaciones))
                {    
                    SelectDataOltAgregarSolEquipo.insert(0,r);
                    intContAgreg = intContAgreg + 1;
                } 
            }
            else
            {
                Ext.Msg.alert('Alerta', 'No se permite agregar más de ' + cantidadMaximaOlts + ' registros');
            }
            
        }
        Ext.getCmp('contadorOltsAgregados').setValue(intContAgreg);
        Ext.getCmp('contadorOltsAgregados').setRawValue(intContAgreg);  
        Ext.getCmp('contadorOltsAgregados').show();
    }
    else
    {
        Ext.Msg.alert('Alerta', 'Seleccione por lo menos un registro de la lista');
    }
}

function eliminarSeleccion(datosSelect)
{
    Ext.Msg.confirm('Alerta', 'Se eliminarán los registros Seleccionados. ¿Seguro desea continuar?', function(btn)
    {
        if (btn == 'yes')
        {
            var xRowSelMod = datosSelect.getSelectionModel().getSelection();
            for (var i = 0; i < xRowSelMod.length; i++)
            {
                var RowSel = xRowSelMod[i];
                datosSelect.getStore().remove(RowSel);
                intContAgreg = intContAgreg - 1;
            }
            
            Ext.getCmp('contadorOltsAgregados').setValue(intContAgreg);
            Ext.getCmp('contadorOltsAgregados').setRawValue(intContAgreg);
            Ext.getCmp('contadorOltsAgregados').show();  
        }
    });
}

/*
 * Funcion permite guardar los registro en la base datos
 * @returns {Number}
 */
function OltAgregarSolEquipo()
{
    var strModeloElementoAsig = Ext.getCmp('sltModeloAsig').value;
    var arrayDatosOltAgreSolEquipo = new Object();
    var jsonDataOltAgregSolEquipo = '';
    arrayDatosOltAgreSolEquipo['arrayData'] = new Array();
    var arrayData = Array();
    
    var intListaOltAgregarSolEquipo = gridAsignaciones.getStore().getCount();
    
    if( intListaOltAgregarSolEquipo == 0 )
    {
        Ext.Msg.alert('Alerta', 'Lista de Registros Seleccionados vacía');
        return 0;
    }
    
    Ext.Msg.confirm('Alerta', ' Se generaran solicitudes de Agregar Equipo Masivo para los servicios de esta lista. ¿Seguro desea continuar?',
    function(btn)
    {
        if (btn == 'yes')
        {
            Ext.MessageBox.show({
                msg: 'Guardando...',
                title:
                        'Procesando',
                progressText: 'Guardando.',
                progress: true,
                closable: false,
                width: 300,
                wait: true,
                waitConfig: {interval: 200}
            });

            for (var i = 0; i < gridAsignaciones.getStore().getCount(); i++)
            {
                arrayData.push(gridAsignaciones.getStore().getAt(i).data);
            }

            arrayDatosOltAgreSolEquipo['arrayData'] = arrayData;
            jsonDataOltAgregSolEquipo = Ext.JSON.encode(arrayDatosOltAgreSolEquipo);

            Ext.Ajax.request({
                url: urlSolAgregarEquipo,
                method: 'POST',
                timeout: 60000,
                params: {
                    jsonListaAgregarEquipo: jsonDataOltAgregSolEquipo,
                    modeloElementoAsig:strModeloElementoAsig
                },
                success: function(response) {
                    Ext.Msg.alert("Información","Datos procesados con exito");
                    store.load({
                        callback: function()
                        {
                            Ext.getCmp('contadorOltsTotal').setValue(store.getTotalCount());
                            Ext.getCmp('contadorOltsTotal').setRawValue(store.getTotalCount());
                        }
                    });
                    gridAsignaciones.getStore().removeAll(); 
                    Ext.getCmp('contadorOltsAgregados').setValue(0);
                    Ext.getCmp('contadorOltsAgregados').setRawValue(0);  
                    Ext.getCmp('contadorOltsAgregados').show();
                    intContAgreg = 0;
                    Ext.getCmp('store').doLayout();
                },
                failure: function(result) {
                    Ext.Msg.alert("Alert","Error Solicitud de Agregar Equipos Masivo. Notificar a Sistemas");
                    
                }
            });
        }
    });
}

function existeAsignacion(myRecord, grid)
{
    var existe = false;
    var num = grid.getStore().getCount();
    for (var i = 0; i < num; i++)
    {
        var strLog = grid.getStore().getAt(i).get('strLogin');
        if (strLog == myRecord.get('strLogin'))
        {
            existe = true;
            break;
        }
    }
    return existe;
}