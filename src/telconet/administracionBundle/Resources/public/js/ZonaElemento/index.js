var storeElementos;

Ext.onReady(function() { 

    Ext.tip.QuickTipManager.init();

    var boolZonificarElementos   = true;

    //Zonificar Elementos
    var permisoEliminar = $("#ROLE_411-5821");
    var boolPermiso = (typeof permisoEliminar === 'undefined') ? false : (permisoEliminar.val() == 1 ? true : false);

    if (!boolPermiso)
    {
        boolZonificarElementos = true;
    }
    else
    {
        boolZonificarElementos = false;
    }

    storeElementos = new Ext.data.Store({ 
        pageSize: 30,
        total: 'total',
        proxy: {
            type: 'ajax',
			timeout: 9600000,
            url : url_gridElementos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
		[
                    {name:'id_elemento', mapping:'id_elemento'},
                    {name:'nombre_elemento', mapping:'nombre_elemento'},
                    {name:'nombre_zona', mapping:'nombre_zona'},
                    {name:'estado', mapping:'estado'},
                    {name:'nombre_tipo', mapping:'nombre_tipo'},
                    {name:'nombre_modelo', mapping:'nombre_modelo'}
		],
        autoLoad: false
    });

    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });

    grid = Ext.create('Ext.grid.Panel', {
        id : 'grid',
        width: 800,
        height: 500,
        store: storeElementos,
        selModel: sm,
        viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
        iconCls: 'icon-grid',
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
                        iconCls: 'icon_zonificar',
                        text: 'Zonificar Elementos',
                        itemId: 'zonificarAjax',
                        scope: this,
                        hidden: boolZonificarElementos,
                        handler: function() {
                             zonificarElementos();
                        }
                    }
                ]
            }
        ],
        columns:
            [
                {
                  id: 'nombre_tipo',
                  header: 'Tipo',
                  dataIndex: 'nombre_tipo',
                  width: 100,
                  sortable: true
                }, 
                {
                  id: 'nombre_modelo',
                  header: 'Modelo',
                  dataIndex: 'nombre_modelo',
                  width: 150,
                  sortable: true
                },    
                {
                  id: 'nombre_elemento',
                  header: 'Nombre Elemento',
                  dataIndex: 'nombre_elemento',
                  width: 250,
                  sortable: true
                },                
                {
                  id: 'nombre_zona',
                  header: 'Nombre Zona',
                  dataIndex: 'nombre_zona',
                  width: 150,
                  sortable: true
                },                
                {
                  id: 'estado',
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: 100,
                  sortable: true
                }              
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: storeElementos,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
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
            }
        },
        renderTo: 'grid'
    });

    storeTipoElementos = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_TiposElementos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
        [
            { name:'NombreTipoElemento' , mapping:'NombreTipoElemento'  },
            { name:'idTipoElemento'     , mapping:'idTipoElemento' }
        ]
    });

    storeModelosElementos = new Ext.data.Store({
        total: 'total',
        pageSize: 200,
        proxy: {
            type: 'ajax',
            method: 'post',
            url: url_ModelosElementos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
        [
            { name:'idModeloElemento' , mapping:'idModeloElemento'  },
            { name:'nombreModeloElemento' , mapping:'nombreModeloElemento' }
        ]
    });    

    comboTipoElementos = Ext.create('Ext.form.ComboBox', {
        id: 'cmbTipoElemento',
        store: storeTipoElementos,
        displayField: 'NombreTipoElemento',
        valueField: 'idTipoElemento',
        fieldLabel: 'Tipo',
        height: 30,
        width: 220,
        queryMode: "remote",
        minChars: 3,
        emptyText: '',
        labelWidth: 50,
        disabled: false,
        listeners:
        {
            select: function(combo)
            {
                Ext.getCmp('cmbModelosElementos').value = null;
                Ext.getCmp('cmbModelosElementos').setRawValue(null);
                storeModelosElementos.proxy.extraParams = {tipoElementoId: combo.getValue()};
                storeModelosElementos.load();
            }
        }
    });

    comboModelosElementos = Ext.create('Ext.form.ComboBox', {
        id: 'cmbModelosElementos',
        store: storeModelosElementos,
        displayField: 'nombreModeloElemento',
        valueField: 'idModeloElemento',
        fieldLabel: 'Modelo',
        height: 30,
        width: 220,
        queryMode: "remote",
        minChars: 3,
        emptyText: '',
        labelWidth: 50,
        disabled: false
    });   
    
    comboEstado = Ext.create('Ext.form.ComboBox', {
        id: 'cmbEstado',
        store:
            [
                ['Todos','Todos'],
                ['Activo','Activo'],
                ['Eliminado','Eliminado']
            ],
        displayField : 'nombreEstado',
        valueField   : 'idEstado',
        fieldLabel   : 'Estado',
        height       : 30,
        value        :'Todos',
        width        : 200,
        labelWidth   : 50,
        emptyText    : '',
        disabled     : false
    });    

    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border:false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'left'            
        },
        bodyStyle: {
                    background: '#fff'
                },

        collapsible : true,
        collapsed: true,
        width: 800,
        title: 'Criterios de busqueda',
            buttons: [
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
                        {
                            xtype      : 'textfield',
                            id         : 'txtNombreElemento',
                            fieldLabel : 'Nombre',
                            value      : '',
                            labelWidth : 50,
                            width      : 220,
                        },
                        {html: "&nbsp;", border: false, width: 200},
                        comboEstado,
                        {html: "&nbsp;", border: false, width: 250},
                        {html: "&nbsp;", border: false, width: 200},
                        comboTipoElementos,
                        {html: "&nbsp;", border: false, width: 200},
                        presentarComboZonasFilter(),
                        {html: "&nbsp;", border: false, width: 250},
                        {html: "&nbsp;", border: false, width: 200},
                        comboModelosElementos
                    ],
        renderTo: 'filtro'
    });
    
});

function zonificarElementos()
{
    if (sm.getSelection().length > 0) {
        presentarVentanaZonificacion();
    } else {
        Ext.Msg.alert('Error ', 'Seleccione por lo menos un registro de la lista..!!');
    }
}

function presentarComboZonasFilter()
{
    var storeZonasFilter = new Ext.data.Store({
        total    : 'total',
        pageSize : 200,
        proxy: {
            type   : 'ajax',
            method : 'post',
            url    : url_zonas,
            reader: {
                type          : 'json',
                totalProperty : 'total',
                root          : 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
        [
            { name:'idZona'     , mapping:'idZona'  },
            { name:'nombreZona' , mapping:'nombreZona' }
        ]
    });

    var comboZonasFilter = Ext.create('Ext.form.ComboBox', {
        id           : 'cmbZonaFilter',
        store        : storeZonasFilter,
        fieldLabel   : 'Zona',
        displayField : 'nombreZona',
        valueField   : 'idZona',
        width        : 270,
        height       : 30,
        queryMode    : "remote",
        minChars     : 3,
        emptyText    : '',
        labelWidth   : 50,
        disabled     : false
    });

    return comboZonasFilter;
}

function presentarComboZonas()
{
    var storeZonas = new Ext.data.Store({
        total    : 'total',
        pageSize : 200,
        proxy: {
            type   : 'ajax',
            method : 'post',
            url    : url_zonas,
            reader: {
                type          : 'json',
                totalProperty : 'total',
                root          : 'encontrados'
            },
            extraParams: {
                estado: 'Activo'
            }
        },
        fields:
        [
            { name:'idZona'     , mapping:'idZona'  },
            { name:'nombreZona' , mapping:'nombreZona' }
        ]
    });

    var comboZonas = Ext.create('Ext.form.ComboBox', {
        id           : 'cmbZona',
        store        : storeZonas,
        fieldLabel   : 'Zona',
        displayField : 'nombreZona',
        valueField   : 'idZona',
        width        : 270,
        height       : 30,
        queryMode    : "remote",
        minChars     : 3,
        emptyText    : '',
        labelWidth   : 50,
        disabled     : false
    });

    return comboZonas;
}


function presentarVentanaZonificacion()
{
    btnguardar = Ext.create('Ext.Button', {
        text : 'Aceptar',
        cls  : 'x-btn-rigth',
        handler: function() {
            var intZonaElemento = Ext.getCmp('cmbZona').value;
            prosesarZonificacion(intZonaElemento,win);
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
        bodyPadding   : 5,
        waitMsgTarget : true,
        layout        : 'column',
        fieldDefaults: {
            labelAlign : 'left',
            labelWidth : 100,
            msgTarget  :'side'
        },
        items:
        [
            {
                xtype      : 'fieldset',
                title      : '',
                style      : 'border: none;padding:0px',
                autoHeight : true,
                width      : 300,
                items      :
                [
                    presentarComboZonas()
                ]
            }
        ]
    });

    win = Ext.create('Ext.window.Window', {
        title       : "Actualizar Zona",
        closable    : false,
        modal       : true,
        width       : 320,
        height      : 100,
        resizable   : false,
        layout      : 'fit',
        items       : [formPanel],
        buttonAlign : 'center',
        buttons     : [btnguardar,btncancelar]
    }).show();
}

function prosesarZonificacion(intZonaElemento,ventana)
{
    var tramaElementos  = '';
    var numeroElementos = 0;

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

    for (var i = 0; i < sm.getSelection().length; ++i)
    {
        tramaElementos = tramaElementos + sm.getSelection()[i].data.id_elemento;
        if (sm.getSelection()[i].data.estado == "Eliminado")
        {
            numeroElementos = numeroElementos + 1;
        }
        if (i < (sm.getSelection().length - 1))
        {
            tramaElementos = tramaElementos + '|';
        }
    }

    if (numeroElementos == 0)
    {
        Ext.Msg.confirm('Alerta', 'Se actualizara la zona a los elementos seleccionados. Desea continuar?', function(btn) {
            if (btn == 'yes') {
                ventana.close();
                conn.request({
                    url    : url_zonificarElemento,
                    method : 'post',
                    params : {
                        tramaElementos : tramaElementos,
                        zona           : intZonaElemento
                    },
                    success: function(response) {
                        var json = Ext.JSON.decode(response.responseText);
                        if (json.estado == "Ok") {
                            Ext.Msg.alert('Alerta', 'Transaccion Exitosa');
                            storeElementos.load();
                        }
                        else {
                            Ext.Msg.alert('Error ', 'Se produjo un error en la ejecucion.');
                        }
                    },
                    failure: function(result) {
                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                    }
                });
            }
        });
    }
    else
    {
        Ext.Msg.alert('Error ','No se pueden ejecutar registros en estado Eliminado');
    }  
}

function buscar()
{
    if(Ext.getCmp('cmbModelosElementos').value == null
        && Ext.getCmp('txtNombreElemento').value == ""
        && Ext.getCmp('cmbZonaFilter').value == null)
    {
        Ext.Msg.alert("Alerta","Realice la busqueda de un elemento especifico o seleccione un modelo determinado");
    }
    else
    {
        storeElementos.getProxy().extraParams.nombreElemento = Ext.getCmp('txtNombreElemento').value;
        storeElementos.getProxy().extraParams.tipoElemento   = Ext.getCmp('cmbTipoElemento').value;
        storeElementos.getProxy().extraParams.modeloElemento = Ext.getCmp('cmbModelosElementos').value;
        storeElementos.getProxy().extraParams.estado         = Ext.getCmp('cmbEstado').value;
        storeElementos.getProxy().extraParams.zona           = Ext.getCmp('cmbZonaFilter').value;
        storeElementos.load();
    }
}

function limpiar()
{
    Ext.getCmp('txtNombreElemento').value = "";
    Ext.getCmp('txtNombreElemento').setRawValue("");
    Ext.getCmp('cmbTipoElemento').value = null;
    Ext.getCmp('cmbTipoElemento').setRawValue(null);
    Ext.getCmp('cmbModelosElementos').value = null;
    Ext.getCmp('cmbModelosElementos').setRawValue(null);
    Ext.getCmp('cmbZonaFilter').value = null;
    Ext.getCmp('cmbZonaFilter').setRawValue(null);
    storeElementos.removeAll();
    grid.getStore().removeAll();
}
