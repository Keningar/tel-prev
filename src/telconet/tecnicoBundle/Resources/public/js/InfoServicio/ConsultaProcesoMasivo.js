/**
 * Script que presenta la pantalla de consulta de procesos masivos
 * 
 * @author John Vera         <javera@telconet.ec>
 * @version 1.0 04-12-2014
 * @author modificado John Vera         <javera@telconet.ec>
 * @version 1.1 27-11-2015
 * 
 */
Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();


    //store que obtiene los elementos
    var storeElementos = new Ext.data.Store({
        pageSize: 100,
        listeners: {
            load: function() {

            }
        },
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url: getElementosPorTipo,
            extraParams: {
                idServicio: '',
                tipoElemento: 'OLT'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'},
                {name: 'ipElemento', mapping: 'ip'}
            ]
    });



    store = new Ext.data.Store
        ({
            pageSize: 1000,
            total: 'total',
            proxy:
                {
                    timeout: 400000,
                    type: 'ajax',
                    url: 'getConsulta',
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                    extraParams:
                        {
                            login: '',
                            estado: '',
                            fechaDesde: '',
                            fechaHasta: '',
                            ultimaMilla: '',
                            tipo: '',
                            idProcesoMasivo: '',
                            idElemento:''

                        }
                },
            fields:
                [
                    {name: 'procesoMasivo', mapping: 'procesoMasivo'},
                    {name: 'procesoMasivoDet', mapping: 'procesoMasivoDet'},
                    {name: 'login', mapping: 'login'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'},
                    {name: 'tipoProceso', mapping: 'tipoProceso'},
                    {name: 'ultimaMilla', mapping: 'ultimaMilla'},
                    {name: 'fechaProceso', mapping: 'fechaProceso'},
                    {name: 'usuarioCrea', mapping: 'usuarioCrea'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'observacion', mapping: 'observacion'}
                ]
        });


    storeTipoProceso = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data : arrayListaTipoProceso
    });
    
    storeEstadosPmDet = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data : arrayListaEstadosPmDet
    });

    //ultima milla 
    var storeUltimaMilla = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: getUltimaMilla,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
            [
                {name: 'nombreUltimaMilla', mapping: 'nombreUltimaMilla'},
                {name: 'codigoUltimaMilla', mapping: 'codigoUltimaMilla'}
            ],
        autoLoad: true
    });

    var pluginExpanded = true;

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })

    grid = Ext.create('Ext.grid.Panel',
        {
            width: '98%',
            height: 450,
            store: store,
            loadMask: true,
            frame: false,
            selModel: sm,
            viewConfig: {enableTextSelection: true},
            dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    align: '->',
                    items: [
                        {xtype: 'tbfill'},
                        {
                            iconCls: 'icon_add',
                            text: 'Ejecutar',
                            itemId: 'ejecutaAjax',
                            scope: this,
                            handler: function() {
                                
                             var permiso = $("#ROLE_333-3477");
                             var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);

                             if (!boolPermiso) {
                                 Ext.Msg.alert('Error ', 'USTED NO TIENE PRIVILEGIOS PARA PRESIONAR ESTE BOTON.');
                             }
                             else {
                                 ejecutarAlgunos();
                             }

                            }
                        }
                    ]}
            ],
            iconCls: 'icon-grid',
            columns:
                [
                    {
                        header: 'No. Proceso',
                        dataIndex: 'procesoMasivo',
                        width: '6%',
                        sortable: true
                    },
                    {
                        header: 'Login',
                        dataIndex: 'login',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Elemento',
                        dataIndex: 'nombreElemento',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Tipo Proceso',
                        dataIndex: 'tipoProceso',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Ultima Milla',
                        dataIndex: 'ultimaMilla',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Fecha Proceso',
                        dataIndex: 'fechaProceso',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Usuario Crea',
                        dataIndex: 'usuarioCrea',
                        width: '10%',
                        sortable: true
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: '5%',
                        sortable: true
                    },
                    {
                        header: 'Observacion',
                        dataIndex: 'observacion',
                        width: '28%',
                        sortable: true
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

    var filterPanel = Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout: {
                type: 'table',
                columns: 5,
                align: 'stretch'
            },
            bodyStyle:
                {
                    background: '#fff'
                },
            collapsible: true,
            collapsed: false,
            width: '98%',
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
                    {width: '10%', border: false},
                    {
                        xtype: 'textfield',
                        id: 'idProcesoMasivo',
                        fieldLabel: 'No. Proceso Masivo',
                        value: '',
                        maskRe: /[0-9.]/,
                        width: '30%'
                    },
                    {width: '20%', border: false},
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Tipo Proceso',
                        id: 'tipo',
                        value: '',
                        store: storeTipoProceso,
                        valueField: 'id',
                        displayField: 'name',
                        width: '30%',
                        listeners: {
                            change: function (field,newValue, oldValue ) {
                                if( newValue =='CambioPlanMasivo') {
                                    buscar();
                                }                                                                              
                            }
                        }
                    },
                    {width: '10%', border: false},
                    {width: '10%', border: false},
                    {
                        xtype: 'textfield',
                        id: 'login',
                        fieldLabel: 'Login',
                        value: '',
                        width: '30%'
                    },
                    {width: '20%', border: false},
                    
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Estado',
                        id: 'estado',
                        value: '',
                        store: storeEstadosPmDet,
                        valueField: 'id',
                        displayField: 'name',
                        width: '30%',
                    },
                    {width: '10%', border: false},
                    //inicio del siguiente bloque
                    {width: '10%', border: false},
                    {
                        xtype: 'datefield',
                        format: 'd/m/Y',
                        id: 'fechaDesde',
                        name: 'fechaDesde',
                        fieldLabel: "Fecha inicio ",
                        labelStyle: 'text-align:left;',
                        displayField: "",
                        value: "",
                        maxValue: new Date()
                    },
                    {width: '20%', border: false},
                    {
                        xtype: 'datefield',
                        format: 'd/m/Y',
                        id: 'fechaHasta',
                        name: 'fechaHasta',
                        fieldLabel: "Fecha fin ",
                        labelStyle: 'text-align:left;',
                        displayField: "",
                        value: "",
                        //allowBlank : false,
                        maxValue: new Date()
                    },
                    {width: '10%', border: false},
                    //nueva fila
                    {width: '20%', border: false},
                    {
                        xtype: 'combo',
                        fieldLabel: 'Ultima Milla',
                        id: 'ultimaMilla',
                        name: 'ultimaMilla',
                        displayField: 'nombreUltimaMilla',
                        valueField: 'codigoUltimaMilla',
                        emptyText: 'Seleccione...',
                        labelStyle: 'text-align:left;',
                        multiSelect: false,
                        queryMode: 'local',
                        store: storeUltimaMilla,
                    },
                    {width: '20%', border: false},
                    {
                        xtype: 'combobox',
                        id: 'sltElemento',
                        fieldLabel: 'Elemento:',
                        store: storeElementos,
                        displayField: 'nombreElemento',
                        valueField: 'idElemento',
                        loadingText: 'Buscando ...',
                        listClass: 'x-combo-list-small',
                        queryMode: "remote",
                        lazyRender: true,
                        forceSelection: true,
                        emptyText: 'Ingrese nombre Elemento..',
                        minChars: 3,
                        typeAhead: true,
                        selectOnTab: true,
                        width: '30%'
                    },
                    {width: '10%', border: false}
                    
                    
                ],
            renderTo: 'filtro'
        });

    store.load();


});


/**
 * Funcion que ejecuta la busqueda de los datos
 * 
 * @author John Vera         <javera@telconet.ec>
 * @version 1.0 04-12-2014
 * 
 */
function buscar()
{
    if (Ext.getCmp('fechaDesde').value > Ext.getCmp('fechaHasta').value)
    {
        alert('La fecha final debe ser mayor a la final.');
        return;
    }

    store.getProxy().extraParams.estado = Ext.getCmp('estado').value;
    store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').value;
    store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').value;
    store.getProxy().extraParams.idProcesoMasivo = Ext.getCmp('idProcesoMasivo').value;
    store.getProxy().extraParams.tipo = Ext.getCmp('tipo').value;
    store.getProxy().extraParams.login = Ext.getCmp('login').value;
    store.getProxy().extraParams.idElemento = Ext.getCmp('sltElemento').value;

    store.getProxy().extraParams.ultimaMilla = Ext.getCmp('ultimaMilla').value;

    store.load();
}

/**
 * Funcion que limpia los controles de los 
 * filtros.
 * @author John Vera         <javera@telconet.ec>
 * @version 1.0 04-12-2014
 * 
 */
function limpiar()
{
    Ext.getCmp('estado').value = "";
    Ext.getCmp('estado').setRawValue("");

    Ext.getCmp('fechaDesde').value = "";
    Ext.getCmp('fechaDesde').setRawValue("");

    Ext.getCmp('fechaHasta').value = "";
    Ext.getCmp('fechaHasta').setRawValue("");

    Ext.getCmp('login').value = "";
    Ext.getCmp('login').setRawValue("");
    
    Ext.getCmp('tipo').value = "";
    Ext.getCmp('tipo').setRawValue("");
    
    Ext.getCmp('ultimaMilla').value = "";
    Ext.getCmp('ultimaMilla').setRawValue("");
    
    Ext.getCmp('sltElemento').value = "";
    Ext.getCmp('sltElemento').setRawValue("");
    
}


function ejecutarAlgunos() {
    var param = '';

    if (Ext.getCmp('tipo').value == "CambioPlanMasivo")
    {
        if (sm.getSelection().length > 0)
        {
            var estado = 0;
            for (var i = 0; i < sm.getSelection().length; ++i)
            {
                param = param + sm.getSelection()[i].data.procesoMasivoDet;
                if (sm.getSelection()[i].data.estado != 'PrePendiente')
                {
                    estado = estado + 1;
                }
                if (i < (sm.getSelection().length - 1))
                {
                    param = param + '|';
                }
            }
            if (estado == 0)
            {
                Ext.Msg.confirm('Alerta', 'Se ejecutaran los registros. Desea continuar?', function(btn) {
                    if (btn == 'yes') {
                        Ext.get("grid").mask('Cargando...');
                        Ext.Ajax.request({
                            url: "ejecutaCambioPlanMasivo",
                            method: 'post',
                            params: {param: param},
                            success: function(response) {
                                var text = response.responseText;
                                if (text == "OK") {
                                    Ext.Msg.alert('Alerta', 'Transaccion Exitosa');
                                    Ext.get("grid").unmask();
                                    store.load();
                                }
                                else {
                                    Ext.Msg.alert('Error ', 'Se produjo un error en la ejecucion.');
                                    Ext.get("grid").unmask();
                                }
                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Error ','Solo se pueden ejecutar registros en estado PrePendiente');
            }
        }
        else
        {

            Ext.Msg.alert('Error ', 'Seleccione por lo menos un registro de la lista');

        }

    } else
    {
        Ext.Msg.alert('Error ', 'Debe seleccionar el tipo de proceso Cambio Plan Masivo');
    }
} 