Ext.require
    ([
        '*',
        'Ext.tip.QuickTipManager',
        'Ext.window.MessageBox'
    ]);

var itemsPerPage = 10;
var totalSuma = 0;
var estado_store;
var store_mes;
var store;
var storeLogins;

Ext.onReady(function ()
{
    //CREAMOS DATA STORE PARA ESTADOS
    Ext.define('modelEstado',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'estado', type: 'string'}
                ]
        });

    estado_store = Ext.create('Ext.data.Store',
        {
            autoLoad: false,
            model: "modelEstado",
            proxy:
                {
                    type: 'ajax',
                    url: strUrlGetEstadosConsumoCloud,
                    timeout: 9000000,
                    reader:
                        {
                            type: 'json',
                            root: 'estados'
                        }
                },
            listeners: {
                load: function ()
                {
                    this.add(Ext.create('modelEstado', {
                        estado: 'Todos'
                    }));
                }
            }
        });

    var estado_cmb = new Ext.form.ComboBox
        ({
            xtype: 'combobox',
            store: estado_store,
            id: 'idestado',
            name: 'idestado',
            valueField: 'estado',
            displayField: 'estado',
            fieldLabel: 'Estado(*)',
            width: 350,
            labelAlign: 'right',
            emptyText: 'Seleccione',
            labelWidth: 110,
            labelPad: 10,
        });


    //VARIABLES PARA PRESENTAR LOS MESES
    Ext.define('modelMes',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'mes', type: 'string'}
                ]
        });

    store_mes = Ext.create('Ext.data.Store',
        {
            autoLoad: false,
            model: "modelMes",
            proxy:
                {
                    type: 'ajax',
                    url: strUrlGetMesesConsumoCloud,
                    timeout: 9000000,
                    reader:
                        {
                            type: 'json',
                            root: 'mes'
                        }
                },
            listeners: {
                load: function ()
                {
                    this.add(Ext.create('modelMes', {
                        mes: 'Todos'
                    }));
                }
            }
        });

    var cmbMeses = new Ext.form.ComboBox
        ({
            xtype: 'combobox',
            store: store_mes,
            id: 'idMes',
            name: 'idMes',
            valueField: 'mes',
            displayField: 'mes',
            fieldLabel: 'Mes Consumo(*)',
            width: 350,
            emptyText: 'Seleccione',
            labelAlign: 'right',
            labelWidth: 110,
            labelPad: 10,
        });

    //MODEL PARA EL GRID
    Ext.define('ListaDetalleModel',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'feConsumo', type: 'string'},
                    {name: 'nombre', type: 'string'},
                    {name: 'login', type: 'string'},
                    {name: 'loginFacturacion', type: 'string'},
                    {name: 'descripcion', type: 'string'},
                    {name: 'valor', type: 'float'},
                    {name: 'estado', type: 'string'},
                    {name: 'observacion', type: 'string'}
                ]
        });


    store = Ext.create('Ext.data.JsonStore',
        {
            model: 'ListaDetalleModel',
            pageSize: itemsPerPage,
            autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    timeout: 9000000,
                    url: strUrlGridConsumoCloud,
                    reader:
                        {
                            type: 'json',
                            root: 'documentos',
                            totalProperty: 'total',
                            sumTotalProperty: 'valorTotal'
                        },
                    simpleSortMode: true
                },
            listeners:
                {                   
                    beforeload: function (store)
                    {
                        store.getProxy().extraParams.mesConsumo = Ext.getCmp('idMes').getValue();
                        store.getProxy().extraParams.puntoId = Ext.getCmp('cmbLogin').getValue();
                        store.getProxy().extraParams.estado = Ext.getCmp('idestado').getValue();
                    }
                }
        });


    Ext.define('ListModelLogin',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'puntoId', type: 'int'},
                    {name: 'login', type: 'string'}
                ]
        });

    storeLogins = Ext.create('Ext.data.Store',
        {
            model: 'ListModelLogin',
            autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    url: strUrlGetLoginsConsumoCloud,
                    timeout: 9000000,
                    reader:
                        {
                            type: 'json',
                            root: 'encontrados'
                        }
                },
            listeners: {
                load: function ()
                {
                    this.add(Ext.create('ListModelLogin', {
                        puntoId: 0,
                        login: 'Todos'
                    }));
                }
            }
        });

    var cmbLogin = new Ext.form.ComboBox
        ({
            xtype: 'combobox',
            store: storeLogins,
            id: 'cmbLogin',
            name: 'cmbLogin',
            valueField: 'puntoId',
            displayField: 'login',
            fieldLabel: 'Login',
            width: 350,
            labelAlign: 'right',
            labelWidth: 110,
            labelPad: 10,
            emptyText: 'Seleccione',
            editable: false
        });


    Ext.create('Ext.grid.Panel',
        {
            width: 1200,
            height: 285,
            collapsible: false,
            title: '',
            renderTo: Ext.get('gridDocumentosFinancieros'),
            features: [{
                    ftype: 'summary'
                }],
            bbar: Ext.create('Ext.PagingToolbar',
                {
                    store: store,
                    displayInfo: true,
                    displayMsg: 'Mostrando consumos {0} - {1} of {2}',
                    emptyMsg: "No hay datos para mostrar"
                }),
            store: store,
            multiSelect: false,
            viewConfig:
                {
                    stripeRows: true,
                    enableTextSelection: true,
                    emptyText: 'No hay datos para mostrar'
                },
            listeners:
                {

                    viewready: function (grid)
                    {
                        var view = grid.view;

                        grid.mon(view,
                            {
                                uievent: function (type, view, cell, recordIndex, cellIndex)
                                {
                                    grid.cellIndex = cellIndex;
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
                                        beforeshow: function (tip)
                                        {
                                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                                            {
                                                var header = grid.headerCt.getGridColumns()[grid.cellIndex];

                                                if (header.dataIndex != null)
                                                {
                                                    var trigger = tip.triggerElement,
                                                        parent = tip.triggerElement.parentElement,                                                        
                                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                                    if (view.getRecord(parent).get(columnDataIndex) != null)
                                                    {
                                                        var columnText = view.getRecord(parent).get(columnDataIndex).toString();

                                                        if (columnText)
                                                        {
                                                            tip.update(columnText);
                                                        } else
                                                        {
                                                            return false;
                                                        }
                                                    } else
                                                    {
                                                        return false;
                                                    }
                                                } else
                                                {
                                                    return false;
                                                }
                                            }
                                        }
                                    }
                            });

                        grid.tip.on('show', function ()
                        {
                            var timeout;

                            grid.tip.getEl().on('mouseout', function ()
                            {
                                timeout = window.setTimeout(function () {
                                    grid.tip.hide();
                                }, 500);
                            });

                            grid.tip.getEl().on('mouseover', function () {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseover', function () {
                                window.clearTimeout(timeout);
                            });

                            Ext.get(view.el).on('mouseout', function ()
                            {
                                timeout = window.setTimeout(function () {
                                    grid.tip.hide();
                                }, 500);
                            });
                        });
                    }
                },
            columns:
                [
                    new Ext.grid.RowNumberer(),
                    {
                        text: 'N°',
                        width: 130,
                        dataIndex: 'id',
                        hidden: true
                    },
                    {
                        text: 'Fe Consumo',
                        width: 75,
                        dataIndex: 'feConsumo'
                    },
                    {
                        text: 'Máquina Virtual',
                        width: 300,
                        dataIndex: 'nombre'
                    },
                    {
                        text: 'Login',
                        width: 200,
                        dataIndex: 'login'
                    },
                    {
                        text: 'Login Fact.',
                        width: 125,
                        dataIndex: 'loginFacturacion'
                    },
                    {
                        text: 'Característica',
                        dataIndex: 'descripcion',
                        width: 135,
                        summaryType: 'count',
                        summaryRenderer: function () {
                            return "<b>Total (" + store.proxy.reader.jsonData.total + "): </b>";
                        }
                    },
                    {
                        text: 'Valor',
                        dataIndex: 'valor',
                        align: 'right',
                        width: 75,
                        summaryType: 'count',
                        summaryRenderer: function () {
                            totalSuma = store.proxy.reader.jsonData.valorTotal;
                            return '<b>$' + totalSuma + '</b>';
                        }
                    },
                    {
                        text: 'Estado',
                        dataIndex: 'estado',
                        width: 70
                    },
                    {
                        text: 'Observación',
                        dataIndex: 'observacion',
                        width: 170
                    },
                ]
        });




    Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            layout:
                {
                    type: 'table',
                    columns: 5,
                    align: 'left',
                },
            bodyStyle:
                {
                    background: '#fff'
                },
            collapsible: true,
            collapsed: true,
            width: 1200,
            title: 'Criterios de busqueda',
            buttons:
                [
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: Buscar,
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function ()
                        {
                            limpiar();
                        }
                    },
                    {
                        text: 'Anular Consumos',
                        iconCls: "icon_delete",
                        handler: function ()
                        {
                            anular();
                        }
                    }
                ],
            items:
                [
                    cmbLogin,
                    {html: "&nbsp;", border: false, width: 50},
                    cmbMeses,
                    {html: "&nbsp;", border: false, width: 50},
                    {html: "&nbsp;", border: false, width: 50},
                    estado_cmb,
                    {html: "&nbsp;", border: false, width: 50},
                    {html: "&nbsp;", border: false, width: 50},
                    {html: "&nbsp;", border: false, width: 50},
                ],
            renderTo: 'filtroDocumentosFinancieros'
        });


});
function Buscar()
{
    store.loadData([], false);
    store.currentPage = 1;
    store.load();
}

function limpiar()
{

    storeLogins.load();
    estado_store.load();
    store_mes.load();
    Ext.getCmp('idMes').setRawValue("");
    Ext.getCmp('cmbLogin').setRawValue("");
    Ext.getCmp('idestado').setRawValue("");

    Buscar();

}

function anular()
{
    if (Ext.getCmp('idestado').getValue() === null || Ext.getCmp('idestado').getValue() === 'Todos') {
        Ext.Msg.alert("Advertencia", "Debe escoger un estado específico para anular los consumos.");
        return false;
    }
    if (Ext.getCmp('idMes').getValue() === null || Ext.getCmp('idMes').getValue() === 'Todos') {
        Ext.Msg.alert("Advertencia", "Debe escoger un mes de consumo específico para anular los consumos.");
        return false;
    }
    Ext.Msg.confirm('Alerta', 'Desea anular los consumos que se encuentran en el filtro seleccionado?', function (btn)
    {
        if (btn === 'yes')
        {
            Ext.MessageBox.wait("Anulando los consumos...");
            Ext.Ajax.request({
                url: strUrlAnulaConsumos,
                method: 'post',
                params:
                    {
                        puntoId: Ext.getCmp('cmbLogin').getValue(),
                        estado: Ext.getCmp('idestado').getValue(),
                        mesConsumo: Ext.getCmp('idMes').getValue()
                    },
                success: function (response) {
                    Ext.MessageBox.close();
                    Ext.Msg.alert("Información", response.responseText);
                    limpiar();
                },
                failure: function ()
                {
                    Ext.MessageBox.close();
                    Ext.Msg.alert('Error ', 'Error: Ocurrió un error durante la comunicación');
                }
            });
        }
    });
}

function guardar()
{
    document.form_consumo.submit();

}
function cancelar()
{
    return false;
}


jQuery(document).ready(function ()
{
    jQuery('#agregar_archivo').click(function ()
    {
        intArchivos++;
        var strNuevoElemento = "<td><input type='file' required='required' accept='.csv' id='file_consumo[" + intArchivos + "]' " +
            "name='file_consumo[" + intArchivos + "]' size='40'></td>"
            + "<td><button class='button-grid-eliminar' id='gridfile_consumo[" +
            +intArchivos + "]' onclick='eliminarElemento(\"file_consumo[" +
            +intArchivos + "]\");'></td>";
        var newLi = jQuery('<li></li>').html(strNuevoElemento);
        newLi.appendTo(jQuery('#listaArchivos'));
        return false;
    });
});
function eliminarElemento(strId)
{
    var objElement = document.getElementById(strId);
    objElement.parentNode.removeChild(objElement);
    objElement = document.getElementById("grid" + strId);
    objElement.parentNode.removeChild(objElement);
    return false;
}

