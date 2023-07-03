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
var store = '';
var estado_id = '';
var area_id = '';
var login_id = '';
var tipo_asignacion = '';
var pto_sucursal = '';
var idClienteSucursalSesion;

Ext.onReady(function() {

    Ext.override(Ext.data.proxy.Ajax, {timeout: 900000});
    
    //Modelo para el listado de Errores
    Ext.define('ErroresModel',
        {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'comentario_error', type: 'string'},
                {name: 'login', type: 'string'},
                {name: 'origen_documento', type: 'string'},
            ]
        });

    //Store para el listado de errores
    var store_errores = Ext.create('Ext.data.JsonStore',
        {
            model: 'ErroresModel',
            proxy: {
                type: 'memory',
                reader: {
                    type: 'json',
                    root: 'listado_errores'
                }
            }
        });
        
    var objRequest = Ext.Ajax.request({
            url: url_store_errores,
            method: 'post',
            timeout: 99999,
            async: false,         
            success: function(response) {
                                
                Ext.each(Ext.JSON.decode(response.responseText), function(json){                   
                   Ext.each(json.listado_errores, function(array){                       
                        store_errores.add(array);
                   });
                   
                });
               
            },
            scope: store_errores
        });       

    
    //Listado de errores
    var listErrores = Ext.create('Ext.grid.Panel', {
        width: 1300,
        height: 250,
        collapsible: true,
        title: 'Listado',
        store: store_errores,
        viewConfig: {
            emptyText: 'No existen errores a presentar'
        },
        columns:
            [
                {
                    text: 'Error',
                    flex: 500,
                    dataIndex: 'comentario_error'
                }, {
                    text: 'Login Pago',
                    flex: 100,
                    dataIndex: 'login'
                }, {
                    text: 'Origen',
                    flex: 35,
                    dataIndex: 'origen_documento'
                }
            ]
    });

    //Errores
    var myPanel = new Ext.Panel({
        title: 'Errores...',
        items: [listErrores],
    });    

    //CREA CAMPOS PARA USARLOS EN LA VENTANA DE BUSQUEDA		
    DTFechaDesde = new Ext.form.DateField({
        id: 'fechaDesde',
        fieldLabel: 'F.Emisión Desde',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 200,
        //anchor : '65%',
        //layout: 'anchor'
    });
    DTFechaHasta = new Ext.form.DateField({
        id: 'fechaHasta',
        fieldLabel: 'F.Emisión Hasta',
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 200,
        //anchor : '65%',
        //layout: 'anchor'
    });

    //CREAMOS DATA STORE PARA EMPLEADOS
    Ext.define('modelCliente', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idcliente', type: 'string'},
            {name: 'descripcion', type: 'string'}
        ]
    });

    var estado_clientes = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelCliente",
        proxy: {
            type: 'ajax',
            url: url_store_clientes,
            reader: {
                type: 'json',
                root: 'clientes'
            }
        }
    });

    clientes_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: estado_clientes,
        labelAlign: 'left',
        id: 'idcliente',
        name: 'idcliente',
        valueField: 'idcliente',
        displayField: 'descripcion',
        fieldLabel: 'Clientes',
        width: 300,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank:false,
        listeners: {
            select:
                function(e) {
                    idcliente = Ext.getCmp('idcliente').getValue();
                },
            click: {
                element: 'el', //bind to the underlying el property on the panel
                fn: function() {
                    estado_clientes.removeAll();
                    estado_clientes.load();
                }
            }
        }
    }); 


    Ext.define('ListadoPtosClientes', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id_pto_cliente', type: 'int'},
            {name: 'descripcion_pto', type: 'string'}
        ]
    });

    listado_ptos_clientes = Ext.create('Ext.data.Store', {
        model: 'ListadoPtosClientes',
        proxy: {
            type: 'ajax',
            url: url_store_pto_clientes,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'listado'
            },
            extraParams: {idcliente: ''},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store) {
                listado_ptos_clientes.getProxy().extraParams.idcliente = Ext.getCmp('idcliente').getValue();
            },
            load: function(store) {
                listado_ptos_clientes.each(function(record) {
                    //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                });
            }
        }

    });


    clientes_ptos_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: listado_ptos_clientes,
        labelAlign: 'left',
        id: 'id_pto_cliente',
        name: 'id_pto_cliente',
        valueField: 'id_pto_cliente',
        displayField: 'descripcion_pto',
        fieldLabel: 'Ptos Clientes',
        width: 300,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank: false,
        /*	
         listeners: {
         select:
         function(e) {
         idptocliente = Ext.getCmp('id_pto_cliente').getValue();
         },
         click: {
         element: 'el', //bind to the underlying el property on the panel
         fn: function(){ 
         listado_ptos_clientes.removeAll();
         listado_ptos_clientes.load();
         }
         }			
         }*/
    });


    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'documento', type: 'string'},
            {name: 'punto', type: 'string'},
            {name: 'valor_ingreso', type: 'string'},
            {name: 'valor_egreso', type: 'string'},
            {name: 'acumulado', type: 'string'},
            {name: 'Fecreacion', type: 'string'},
            {name: 'strFeEmision', type: 'string'},
            {name: 'strFeAutorizacion', type: 'string'},
            {name: 'tipoDocumento', type: 'string'},
            {name: 'oficina', type: 'string'},
            {name: 'referencia', type: 'string'},
            {name: 'formaPago', type: 'string'},
            {name: 'numero', type: 'string'},
            {name: 'observacion', type: 'string'},
            {name: 'boolSumatoriaValorTotal', type: 'string'},
        ]
    });


    store = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            async: false,
            url: url_store_grid,
            reader: {
                type: 'json',
                root: 'documentos',
                totalProperty: 'total'
            },
            extraParams: {fechaDesde: '', fechaHasta: '', idcliente: ''},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function(store) {
                store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').getValue();
                store.getProxy().extraParams.idcliente = Ext.getCmp('idcliente').getValue();
                store.getProxy().extraParams.id_pto_cliente = Ext.getCmp('id_pto_cliente').getValue();
            },
            load: function(store) {
                linea_tabla = "<table  width='50%' id='table-3'>";
                linea_tabla += "<thead>";
                linea_tabla += "<tr>";
                linea_tabla += "<th>Fecha creacion</th>";
                linea_tabla += "<th>Oficina</th>";
                linea_tabla += "<th>Tipo documento</th>";
                linea_tabla += "<th>Documento</th>";
                linea_tabla += "<th>Referencia</th>";
                linea_tabla += "<th>Punto</th>";
                linea_tabla += "<th>Ingreso</th>";
                linea_tabla += "<th>Egreso</th>";
                linea_tabla += "<th>Sumatoria</th>";
                linea_tabla += "</tr>";
                linea_tabla += "</thead>";
                linea_tabla += "<tbody>";
                store.each(function(record) {
                    linea_tabla += "<tr><td>" + record.data.Fecreacion + "</td>";
                    linea_tabla += "<td>" + record.data.oficina + "</td>";
                    linea_tabla += "<td>" + record.data.tipoDocumento + "</td>";
                    linea_tabla += "<td>" + record.data.documento + "</td>";
                    linea_tabla += "<td>" + record.data.referencia + "</td>";
                    linea_tabla += "<td>" + record.data.punto + "</td>";
                    linea_tabla += "<td>" + record.data.valor_ingreso + "</td>";
                    linea_tabla += "<td>" + record.data.valor_egreso + "</td>";
                    linea_tabla += "<td>" + record.data.acumulado + "</td></tr>";
                });
                linea_tabla += "</tbody>";
                linea_tabla += "</table>";
                //console.log(linea_tabla);
                $('#estado_cuenta').html(linea_tabla);
                /*store.each(function(record) {
                 //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                 });*/
            }
        }
    });

    store.load();



    var sm = new Ext.selection.CheckboxModel({
        listeners: {
            selectionchange: function(selectionModel, selected, options) {
                arregloSeleccionados = new Array();
                Ext.each(selected, function(record) {
                    //arregloSeleccionados.push(record.data.idOsDet);
                });
                //console.log(arregloSeleccionados);

            }
        }
    });


    var listView = Ext.create('Ext.grid.Panel', {
        width: 1300,
        height: 1300,
        collapsible: false,
        title: 'Estado de Cuenta Por Punto',
        dockedItems: [{
                dock: 'top',
                xtype: 'toolbar',
                items: [
                    {xtype: 'tbfill'},
                    {
                        xtype: 'button',
                        itemId: 'grid-excel-button',
                        iconCls: 'x-btn-icon icon_exportar',
                        //hidden : true,
                        text: 'Exportar',
                        handler: function() {
                            var vExportContent = listView.getExcelXml();
                            document.location = 'data:application/vnd.ms-excel;base64,' + Base64.encode(vExportContent);
                        }

                    }]}],
        //renderTo: Ext.get('lista_prospectos'),
        store: store,
        multiSelect: false,
        viewConfig: {
            stripeRows: true,
            enableTextSelection: true,
            emptyText: 'No hay datos para mostrar',
            getRowClass: function(record, index) {
                var cls = '';
                if (record.data.documento == 'MOVIMIENTOS' ||
                    record.data.documento == 'Anticipos no aplicados' ||
                    record.data.documento == 'Anticipos asignados' ||
                    record.data.documento == 'SALDO:' ||
                    record.data.documento == 'RESUMEN PTO CLIENTE:' ||
                    record.data.documento == 'Historial Anticipos asignados')
                {
                    cls = 'estado_cta';
                    record.data.valor_ingreso = '';
                    record.data.valor_egreso = '';
                    record.data.acumulado = '';
                }

                if (record.data.documento == 'Total:')
                {
                    cls = 'total_estado_cta';
                }
                
                if (record.data.observacion != '' && record.data.observacion != null)
                {
                    cls = 'multilineColumn';
                }
                
                if (record.data.oficina != '' && record.data.oficina != null)
                {
                    cls = 'multilineColumn';
                }
                //Se marca en otro color en el estado de cuenta para el caso de ANTC que no sumarizan el saldo en el estado de cuenta.
                if(record.data.boolSumatoriaValorTotal=='false')
                {
                    cls = 'antc_estado_cta'; 
                }
                return cls;
            }
        },
        listeners: {
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
        columns: [{
                text: 'F. Creacion',
                dataIndex: 'Fecreacion',
                align: 'right',
                width: 70,
                tdCls: 'x-change-cell'
            }, {
                text: 'F. Emision',
                dataIndex: 'strFeEmision',
                align: 'right',
                width: 70,
                tdCls: 'x-change-cell'
             }, {
                text: 'F. Autorizacion',
                dataIndex: 'strFeAutorizacion',
                align: 'right',
                width: 90,
                tdCls: 'x-change-cell'    
            }, {
                text: 'Oficina',
                width: 150,
                dataIndex: 'oficina',
                tdCls: 'x-change-cell  x-grid-cell-inner'
            }, {
                text: 'No. documento',
                width: 200,
                dataIndex: 'documento',
                tdCls: 'x-change-cell'
            }, {
                text: 'Doc.',
                width: 50,
                dataIndex: 'tipoDocumento',
                tdCls: 'x-change-cell'
            }, {
                text: 'F. Pago',
                width: 50,
                dataIndex: 'formaPago',
                tdCls: 'x-change-cell'
            }, {
                text: '',
                width: 100,
                dataIndex: 'numero',
                tdCls: 'x-change-cell'
            }, {
                text: 'Observacion',
                dataIndex: 'observacion',
                tdCls: 'x-change-cell x-grid-cell-inner',
                flex: 1
            }, {
                text: 'Ingreso',
                width: 130,
                align: 'right',
                dataIndex: 'valor_ingreso',
                tdCls: 'x-change-cell'
            }, {
                text: 'Egreso',
                width: 130,
                align: 'right',
                dataIndex: 'valor_egreso',
                tdCls: 'x-change-cell'
            }, {
                text: 'Saldo',
                dataIndex: 'acumulado',
                align: 'right',
                tdCls: 'x-change-cell',
                width: 130
            }]
    });

    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders
        border: false,
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'left',
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 1300,
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
                handler: function() {
                    limpiar();
                }
            }
        ],
        items: [
            DTFechaDesde,
             {html:"&nbsp;",border:false,width:10},
            DTFechaHasta,             
            clientes_cmb,
            clientes_ptos_cmb,
        ],
        renderTo: 'filtro_prospectos'
    });

    verificarCmbSesion();

    var myTabPanel = new Ext.TabPanel({
        activeItem: 0,
        items: [listView, myPanel],
        renderTo: 'filtro_prospectos'
    });

});

function Buscar()
{
    store.load({params: {start: 0, limit: 10}});
}

function limpiar() {
    Ext.getCmp('fechaDesde').setRawValue("");
    Ext.getCmp('fechaHasta').setRawValue("");
    Ext.getCmp('idcliente').setRawValue("");

}

function verificarCmbSesion()
{
    if (cliente == "S")
    {
        clientes_cmb.setVisible(false);
    }
    else
    {
        clientes_cmb.setVisible(true);
    }

    if (ptocliente == "S")
    {
        clientes_ptos_cmb.setVisible(false);
    }
    else
    {
        clientes_ptos_cmb.setVisible(true);
    }

}

