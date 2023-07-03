Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox',
    'Ext.toolbar.Paging',
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

var itemsPerPage = 500;
var tipo_asignacion = '';
var motivo_id = '';
var relacion_sistema_id = '';

Ext.onReady(function () {


    Ext.define('modelMotivo', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idMotivo', type: 'string'},
            {name: 'descripcion', type: 'string'},
            {name: 'idRelacionSistema', type: 'string'}
        ]
    });
    var motivo_store = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelMotivo",
        proxy: {
            type: 'ajax',
            url: url_lista_motivos,
            reader: {
                type: 'json',
                root: 'motivos'
            }
        }
    });
    var motivo_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: motivo_store,
        labelAlign: 'left',
        id: 'idMotivo',
        name: 'idMotivo',
        valueField: 'idMotivo',
        displayField: 'descripcion',
        fieldLabel: 'Motivo Rechazo',
        width: 400,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank: true,
        listeners: {
            select:
                function (e) {
                    //alert(Ext.getCmp('idestado').getValue());
                    motivo_id = Ext.getCmp('idMotivo').getValue();
                    relacion_sistema_id = e.displayTplData[0].idRelacionSistema;
                },
            click: {
                element: 'el', //bind to the underlying el property on the panel
                fn: function () {
                    motivo_id = '';
                    relacion_sistema_id = '';
                    motivo_store.removeAll();
                    motivo_store.load();
                }
            }
        }
    });


    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325,
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 325,
        //anchor : '65%',
        //layout: 'anchor'
    });

    txtMontoInicio = Ext.create('Ext.form.Text',
        {
            id: 'txtMontoInicio',
            name: 'txtMontoInicio',
            fieldLabel: 'Monto Inicial',
            labelAlign: 'left',
            minValue: 1,
            maxValue: 100,
            width: 200,
            maskRe: /[0-9.]/,
            regex: /^[0-9]+(?:\.[0-9]+)?$/,
            regexText: 'Solo numeros'
        });
    txtMontoFin = Ext.create('Ext.form.Text',
        {
            id: 'txtMontoFin',
            name: 'txtMontoFin',
            fieldLabel: 'Monto Fin',
            labelAlign: 'left',
            minValue: 1,
            maxValue: 100,
            width: 200,
            maskRe: /[0-9.]/,
            regex: /^[0-9]+(?:\.[0-9]+)?$/,
            regexText: 'Solo numeros'
        });

    txtLogin = Ext.create('Ext.form.Text',
        {
            id: 'txtLogin',
            name: 'txtLogin',
            fieldLabel: 'Login',
            labelAlign: 'left',
            allowBlank: true,
            width: 325
        });

    txtUsrCreacion = Ext.create('Ext.form.Text',
        {
            id: 'txtUsrCreacion',
            name: 'txtUsrCreacion',
            fieldLabel: 'Usuario Creacion',
            labelAlign: 'left',
            allowBlank: true,
            width: 325
        });

    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'id',                    type: 'string'},
            {name: 'servicio',              type: 'string'},
            {name: 'numero',                type: 'string'},
            {name: 'pto',                   type: 'string'},
            {name: 'valorTotal',            type: 'float'},
            {name: 'estadoImpresionFact',   type: 'string'},
            {name: 'observacion',           type: 'string'},
            {name: 'feCreacion',            type: 'string'},
            {name: 'usrCreacion',           type: 'string'},
            {name: 'motivo',                type: 'string'},
            {name: 'strEsElectronica',      type: 'string'},
            {name: 'linkVer',               type: 'string'},
            {name: 'cliente',               type: 'string'}
        ]
    });


    store = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: url_store,
            timeout: 600000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {fechaDesde: '', fechaHasta: ''},
            simpleSortMode: true
        },
        autoLoad: true,
        listeners: {
            beforeload: function (store) {
                store.getProxy().extraParams.fechaDesde     = Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta     = Ext.getCmp('fechaHasta').getValue();
                store.getProxy().extraParams.intMontoInicio = Ext.getCmp('txtMontoInicio').getValue();
                store.getProxy().extraParams.intMontoFin    = Ext.getCmp('txtMontoFin').getValue();
                store.getProxy().extraParams.strLogin       = Ext.getCmp('txtLogin').getValue();
                store.getProxy().extraParams.strUsrCreacion = Ext.getCmp('txtUsrCreacion').getValue();
            },
            load: function (store) {
                store.each(function (record) {
                    //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                });
            }
        }
    });

    sm = new Ext.selection.CheckboxModel({
        listeners: {
            selectionchange: function (selectionModel, selected, options) {
                arregloSeleccionados = new Array();
                Ext.each(selected, function (record) { });
            }
        }
    });


    var listView = Ext.create('Ext.grid.Panel', {
        width: 1000,
        height: 275,
        collapsible: false,
        title: '',
        selModel: sm,
        store: store,
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    {xtype: 'tbfill'},
                    ,
                        motivo_cmb,
                    {
                        iconCls: 'icon_aprobar',
                        text: 'Rechazar',
                        disabled: false,
                        itemId: 'rechazar',
                        scope: this,
                        handler: function () {
                            rechazarAlgunos()
                        }
                    },
                    {
                        iconCls: 'icon_aprobar',
                        text: 'Aprobar',
                        disabled: false,
                        itemId: 'aprobar',
                        scope: this,
                        handler: function () {
                            aprobarAlgunos()
                        }
                    }]}
        ],
        renderTo: Ext.get('lista'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        listeners: {
            itemdblclick: function (view, record, item, index, eventobj, obj) {
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
        columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Numero',
                width: 110,
                dataIndex: 'numero'
            }, 
            {
                text: 'Cliente',
                width: 150,
                dataIndex: 'cliente'
            }, 
            {
                text: 'Pto. Cliente',
                width: 110,
                dataIndex: 'pto'
            }, {
                text: 'Valor Total',
                width: 110,
                dataIndex: 'valorTotal'
            }, {
                text: 'Elec?',
                width: 50,
                dataIndex: 'strEsElectronica'
            }, {
                text: 'Estado',
                width: 80,
                dataIndex: 'estadoImpresionFact'
            }, {
                text: 'Motivo',
                dataIndex: 'motivo',
                align: 'right',
                width: 160
            }, {
                text: 'Observacion',
                dataIndex: 'observacion',
                align: 'right',
                width: 100
            }, {
                text: 'Fecha Creacion',
                dataIndex: 'feCreacion',
                align: 'right',
                width: 100
            }, {
                text: 'Usuario<br/>Creacion',
                dataIndex: 'usrCreacion',
                align: 'right',
                width: 70
            }]
    });

    function renderAcciones(value, p, record) {
        var iconos = '';
        var estadoIncidencia = true;
        iconos = iconos + '<b><a href="' + record.data.linkVer + '" onClick="" title="Ver" class="button-grid-show"></a></b>';
        return Ext.String.format(
            iconos,
            value,
            '1',
            'nada'
            );
    }

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders
        //bodyBorder: false,
        border: false,
        //border: '1,1,0,1',
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 3,
            align: 'left',
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 1000,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                //xtype: 'button',
                iconCls: "icon_search",
                handler: Buscar,
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function () {
                    limpiar();
                }
            }

        ],
        items: [
            DTFechaDesde,
            {html: "&nbsp;", border: false, width: 50},
            DTFechaHasta,
            txtUsrCreacion,
            {html: "&nbsp;", border: false, width: 50},
            txtLogin,
            txtMontoInicio,
            {html: "&nbsp;", border: false, width: 50},
            txtMontoFin

        ],
        renderTo: 'filtro'
    });

});

function Buscar() {

    store.load({params: {start: 0, limit: 500}});

}



function rechazarAlgunos() {
    var param = '';
    if (sm.getSelection().length > 0)
    {

        for (var i = 0; i < sm.getSelection().length; ++i)
        {
            param = param + sm.getSelection()[i].data.id;

            if (i < (sm.getSelection().length - 1))
            {
                param = param + '|';
            }
        }
        console.log(Ext.getCmp('idMotivo').getValue());
        if (Ext.getCmp('idMotivo').getValue()) {
            Ext.Msg.confirm('Alerta', 'Se rechazaran los documentos seleccionados. Desea continuar?', function (btn) {
                document.getElementById('mensaje_info').classList.add('campo-oculto');
                document.getElementById('mensaje_error').classList.add('campo-oculto');
                var loadmask = new Ext.LoadMask(Ext.getBody(), {msg: "Procesando..."});
                if (btn == 'yes') {
                    loadmask.show();
                    Ext.Ajax.request({
                        url: url_rechazar,
                        method: 'post',
                        params: {param: param, motivoId: motivo_id},
                        success: function (response) {
                            var text = response.responseText;

                            document.getElementById('mensaje_info').classList.remove('campo-oculto');
                            document.getElementById("mensaje_info").innerHTML = "Documento fue rechazado";

                            store.load();
                            loadmask.hide();
                        },
                        failure: function (result)
                        {
                            document.getElementById('mensaje_error').classList.remove('campo-oculto');
                            document.getElementById("mensaje_info").innerHTML = result.statusText;
                            loadmask.hide();
                        }
                    });
                }
            });
        } else {
            alert('Debe seleccionar un motivo para poder rechazar la(s) nota(s) de credito.');
        }

    }
    else
    {
        alert('Seleccione por lo menos un registro de la lista');
    }
}


function aprobarAlgunos() {
    var param = '';
    if (sm.getSelection().length > 0)
    {
        for (var i = 0; i < sm.getSelection().length; ++i)
        {
            param = param + sm.getSelection()[i].data.id;

            if (i < (sm.getSelection().length - 1))
            {
                param = param + '|';
            }
        }
        Ext.Msg.confirm('Alerta', 'Se aprobaran los documentos seleccionados. Desea continuar?', function (btn) {
            document.getElementById('mensaje_info').classList.add('campo-oculto');
            document.getElementById('mensaje_error').classList.add('campo-oculto');
            var loadmask = new Ext.LoadMask(Ext.getBody(), {msg: "Procesando..."});
            if (btn == 'yes') {
                loadmask.show();
                Ext.Ajax.request({
                    url: url_aprobar,
                    method: 'post',
                    params: {param: param},
                    success: function (response) {
                        var text = response.responseText;
                        if (text !== "") {
                            
                           document.getElementById('mensaje_error').classList.remove('campo-oculto');
                           
                            if(text==='ExistenNcMismaFact')
                            {
                                text = "Existen notas de crédito por aprobar para una misma factura. Favor revisar.";
                                document.getElementById("mensaje_error").innerHTML = text;
                            }
                            else
                            {
                                document.getElementById("mensaje_error").innerHTML = text;
                            }
                        }
                        else {
                            document.getElementById('mensaje_info').classList.remove('campo-oculto');
                            document.getElementById("mensaje_info").innerHTML = "Documento fue aprobado";
                        }
                        loadmask.hide();
                        store.load();
                    },
                    failure: function (result)
                    {
                        loadmask.hide();
                        if(result.statusText === null || result.statusText ==='')
                        {   
                            document.getElementById('mensaje_error').classList.remove('campo-oculto');
                            document.getElementById("mensaje_info").innerHTML = result.statusText;
                        }
                        else
                        {
                            document.getElementById('mensaje_info').classList.remove('campo-oculto');
                            document.getElementById("mensaje_info").innerHTML = "Documento aprobado, espere hasta que los datos terminen de cargar.";
                        }
                        store.load();
                    }
                });
            }
        });
    }
    else
    {
        alert('Seleccione por lo menos un registro de la lista');
    }
}

function limpiar() {
    Ext.getCmp('fechaDesde').setRawValue("");
    Ext.getCmp('fechaHasta').setRawValue("");
}
