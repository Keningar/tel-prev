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

Ext.onReady(function () {

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

    //CREAMOS DATA STORE PARA EMPLEADOS
    Ext.define('modelEstado', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idestado', type: 'string'},
            {name: 'codigo', type: 'string'},
            {name: 'descripcion', type: 'string'}
        ]
    });
    var estado_store = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelEstado",
        proxy: {
            type: 'ajax',
            url: url_store_estados,
            reader: {
                type: 'json',
                root: 'estados'
            }
        }
    });
    var estado_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        store: estado_store,
        labelAlign: 'left',
        id: 'idestado',
        name: 'idestado',
        valueField: 'descripcion',
        displayField: 'descripcion',
        fieldLabel: 'Estado',
        width: 325,
        triggerAction: 'all',
        selectOnFocus: true,
        lastQuery: '',
        mode: 'local',
        allowBlank: false,
        listeners: {
            select:
                function (e) {
                    estado_id = Ext.getCmp('idestado').getValue();
                },
            click: {
                element: 'el', //bind to the underlying el property on the panel
                fn: function () {
                    estado_store.removeAll();
                    estado_store.load();
                }
            }
        }
    });


    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [{name: 'Numerofacturasri', type: 'string'},
            {name: 'Punto', type: 'string'},
            {name: 'Cliente', type: 'string'},
            {name: 'Esautomatica', type: 'string'},
            {name: 'Fecreacion', type: 'string'},
            {name: 'Feemision', type: 'string'},
            {name: 'Feautorizacion', type: 'string'},
            {name: 'Total', type: 'string'},
            {name: 'Estado', type: 'string'},
            {name: 'linkVer', type: 'string'},
            {name: 'linkEliminar', type: 'string'},
            {name: 'id', type: 'int'}
        ]
    });


    store = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        pageSize: itemsPerPage,
        proxy: {
            type: 'ajax',
            url: url_store_grid,
            reader: {
                type: 'json',
                root: 'documentos',
                totalProperty: 'total'
            },
            extraParams: {fechaDesde: '', fechaHasta: '', estado: ''},
            simpleSortMode: true
        },
        listeners: {
            beforeload: function (store) {
                store.getProxy().extraParams.fechaDesde = Ext.getCmp('fechaDesde').getValue();
                store.getProxy().extraParams.fechaHasta = Ext.getCmp('fechaHasta').getValue();
                store.getProxy().extraParams.estado = Ext.getCmp('idestado').getValue();
            },
            load: function (store) {
                store.each(function (record) {
                    //idClienteSucursalSesion = record.data.idClienteSucursalSesion;
                });
            }
        }
    });

    if(intIdPunto){
        store.load({params: {start: 0, limit: 10}});
    }



    var sm = new Ext.selection.CheckboxModel({
        listeners: {
            selectionchange: function (selectionModel, selected, options) {
                arregloSeleccionados = new Array();
                Ext.each(selected, function (record) {
                    //arregloSeleccionados.push(record.data.idOsDet);
                });
                //console.log(arregloSeleccionados);

            }
        }
    });


    var listView = Ext.create('Ext.grid.Panel', {
        width: 1000,
        height: 275,
        collapsible: false,
        title: '',
        selModel: sm,
        dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                align: '->',
                items: [
                    //tbfill -> alinea los items siguientes a la derecha
                    {xtype: 'tbfill'},
                    {
                        iconCls: 'icon_delete',
                        text: 'Eliminar',
                        disabled: true,
                        itemId: 'delete',
                        scope: this,
                        handler: function () {
                            eliminarAlgunos();
                        }
                    }]}],
        renderTo: Ext.get('lista_prospectos'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando prospectos {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        store: store,
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
            }
        },
        columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Id Factura',
                width: 130,
                dataIndex: 'id',
                hidden: true
            }, {
                text: 'No. factura SRI',
                width: 130,
                dataIndex: 'Numerofacturasri'
            }, {
                text: 'Pto cliente',
                width: 130,
                dataIndex: 'Punto'
            }, {
                text: 'Cliente',
                width: 130,
                dataIndex: 'Cliente'
            }, {
                text: 'Es automatica',
                dataIndex: 'Esautomatica',
                align: 'right',
                width: 80
            }, {
                text: 'Estado',
                dataIndex: 'Estado',
                align: 'right',
                width: 70
            }, {
                text: 'F. Autorizacion',
                dataIndex: 'Feautorizacion',
                align: 'right',
                width: 100
            }, {
                text: 'F. Emision',
                dataIndex: 'Feemision',
                align: 'right',
                width: 100
            }, {
                text: 'F. Creacion',
                dataIndex: 'Fecreacion',
                align: 'right',
                width: 100
            }, {
                text: 'Total',
                dataIndex: 'Total',
                align: 'right',
                width: 100
            }, {
                text: 'Acciones',
                width: 100,
                renderer: renderAcciones,
            }]
    });


    function renderAcciones(value, p, record) {
        var iconos = '';
        var estadoIncidencia = true;
        iconos = iconos + '<b><a href="' + record.data.linkVer + '" onClick="" title="Ver" class="button-grid-show"></a></b>';
        
        //Solo para los records activos
        if(record.data.Estado=='Activo' && puede_anular)
            iconos = iconos + '<b><a href="#" onClick="showProcesar(' + record.data.id + ')" title="Anular" class="button-grid-delete"></a></b>';

        //verifica que el comprobante tenga mensajes para mostrar la pantalla de logs
        if (record.data.boolMensajesCompElectronico) {
            iconos = iconos + '<b><a href="#"  onClick="getMensajesCompElectronico(' + record.data.id + ')" title="Mensaje Comprobante" class="button-grid-logs"></a></b>';
        }
        //VALIDA DESCARGA COMPROBANTES
        var objPermiso = $("#ROLE_67-1837");
        var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
        if (boolPermiso) {
            if (record.data.boolVerificaEnvioNotificacion) {
                if (record.data.boolDocumentoXml) {
                    iconos = iconos + '<b><a href="#"  onClick="downloadComprobante(' + record.data.id + ',\'' + record.data.Numerofacturasri + '\',  \'xml\')" title="Descargar XML" class="button-grid-xml"></a></b>';
                }
                if (record.data.boolDocumentoPdf) {
                    iconos = iconos + '<b><a href="#"  onClick="downloadComprobante(' + record.data.id + ',\'' + record.data.Numerofacturasri + '\',  \'pdf\')" title="Descargar Documento PDF" class="button-grid-pdf"></a></b>';
                }
            }
        }
        //VALIDA ACTUALIZA COMPROBANTE
        var objPermiso = $("#ROLE_67-1778");
        var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
        if (boolPermiso) {
            //verifica que comprobante pueda ser actualizado
            if (record.data.boolVerificaActualiza) {
                iconos = iconos + '<b><a href="#"  onClick="actualizaComprobanteElec(' + record.data.id + ', ' + record.data.intIdTipoDocumento + ')" title="Actualiza Comprobante" class="button-grid-cambioVelocidad"></a></b>';
            }
        }

        //VALIDA ENVIA NOTIFICACION COMPROBANTE
        var objPermiso = $("#ROLE_67-1777");
        var boolPermiso = (typeof objPermiso === 'undefined') ? false : (objPermiso.val() == 1 ? true : false);
        if (boolPermiso) {
            if (record.data.boolVerificaEnvioNotificacion) {
                iconos = iconos + '<b><a href="#"  onClick="envioNotificacionComprobante(' + record.data.id + ')" title="Envio de Notificacion" class="button-grid-mail"></a></b>';
            }
        }

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
            columns: 5,
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
            {html: "&nbsp;", border: false, width: 50},
            DTFechaDesde,
            {html: "&nbsp;", border: false, width: 50},
            DTFechaHasta,
            {html: "&nbsp;", border: false, width: 50},
            {html: "&nbsp;", border: false, width: 50},
            estado_cmb,
            {html: "&nbsp;", border: false, width: 50},
            {html: "&nbsp;", border: false, width: 325},
            {html: "&nbsp;", border: false, width: 50}
        ],
        renderTo: 'filtro_prospectos'
    });

    function eliminarAlgunos() {
        var param = '';
        if (sm.getSelection().length > 0)
        {
            var estado = 0;
            for (var i = 0; i < sm.getSelection().length; ++i)
            {
                param = param + sm.getSelection()[i].data.idOrden;

                if (sm.getSelection()[i].data.estado == 'Eliminado')
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
                Ext.Msg.confirm('Alerta', 'Se eliminaran los registros. Desea continuar?', function (btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            url: "delete_ajax",
                            method: 'post',
                            params: {param: param},
                            success: function (response) {
                                var text = response.responseText;
                                store.load();
                            },
                            failure: function (result)
                            {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                });

            }
            else
            {
                alert('Por lo menos uno de las registro se encuentra en estado ELIMINADO');
            }
        }
        else
        {
            alert('Seleccione por lo menos un registro de la lista');
        }
    }

    Ext.define('modelMotivos', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id', type: 'string'},
            {name: 'descripcion', type: 'string'}
        ]
    });

    store_motivos = Ext.create('Ext.data.Store', {
        autoLoad: false,
        model: "modelMotivos",
        proxy: {
            type: 'ajax',
            url: url_listar_motivos,
            reader: {
                type: 'json',
                root: 'documentos'
            }
        }
    });


});

function Buscar()
{

    /*if  (( Ext.getCmp('fechaDesde').getValue()!=null)&&(Ext.getCmp('fechaHasta').getValue()!=null) )
     {
     if (Ext.getCmp('fechaDesde').getValue() > Ext.getCmp('fechaHasta').getValue())
     {
     Ext.Msg.show({
     title:'Error en Busqueda',
     msg: 'Por Favor para realizar la busqueda Fecha Desde debe ser fecha menor a Fecha Hasta.',
     buttons: Ext.Msg.OK,
     animEl: 'elId',
     icon: Ext.MessageBox.ERROR
     });		 
     
     }
     else
     {*/
    store.load({params: {start: 0, limit: 10}});
    /*}
     }
     else
     {
     
     Ext.Msg.show({
     title:'Error en Busqueda',
     msg: 'Por Favor Ingrese criterios de fecha.',
     buttons: Ext.Msg.OK,
     animEl: 'elId',
     icon: Ext.MessageBox.ERROR
     });
     }*/
}

function eliminar(direccion)
{
    //alert(direccion);
    Ext.Msg.confirm('Alerta', 'Se eliminara el registro. Desea continuar?', function (btn) {
        if (btn == 'yes') {
            Ext.Ajax.request({
                url: direccion,
                method: 'post',
                success: function (response) {
                    var text = response.responseText;
                    store.load();
                },
                failure: function (result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            });
        }
    });
}

function limpiar() {
    Ext.getCmp('fechaDesde').setRawValue("");
    Ext.getCmp('fechaHasta').setRawValue("");
    Ext.getCmp('idestado').setRawValue("");

}

/**
 * El metodo actualizaComprobanteElec envia el id documento
 * para que el comprobante xml sea actualizado.
 * @param {int} intIdDocumento
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 22-04-2014
 */
function actualizaComprobanteElec(intIdDocumento, intTipoDocumentoId) {
    Ext.Ajax.request({
        url: url_updateCompElectronico,
        method: 'post',
        params: {intIdDocumento: intIdDocumento, intTipoDocumentoId: intTipoDocumentoId},
        success: function (response) {
            var text = Ext.decode(response.responseText);
            if (text.boolConfirmacion == true) {
                Ext.Msg.alert('Success', text.strMensaje);
            } else {
                Ext.Msg.alert('Alert', text.strMensaje);
            }
        },
        failure: function (result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
    store.load();
}

/**
 * El metodo envioNotificacionComprobante envia el id documento
 * para que busque la clave de acceso del comprobante y envie una notificacion
 * @param {int} intIdDocumento
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 22-04-2014
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.1 09-04-2018 Se setea valor para tiempo de timeout. 
 */
function envioNotificacionComprobante(intIdDocumento) {
    Ext.Ajax.request({
        timeout: 400000,
        url: url_envianotificacion,
        method: 'post',
        params: {intIdDocumento: intIdDocumento},
        success: function (response) {
            var text = Ext.decode(response.responseText);
            if (text.boolStatus == true) {
                Ext.Msg.alert('Success', text.strMensaje);
            } else {
                Ext.Msg.alert('Alert', text.strMensaje);
            }
        },
        failure: function (result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
    store.load();
}

/**
 * El metodo downloadComprobante
 * permite descargar un archivo pdf, xml, txt
 * @param {int}    intIdDocumento
 * @param {string} strNombre
 * @param {string} strExtension
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 22-04-2014
 */
function downloadComprobante(intIdDocumento, strNombre, strExtension) {
    window.location = url_downloadCompElectronico+'?strNombre=' + strNombre + '&strExtension=' + strExtension+'&intIdDocumento='+intIdDocumento;
}
/**
 * El metodo getMensajesCompElectronico
 * Obtiene el grid de mensajes de los comprobantes electronicos
 * @param {int} intIdDocumento
 * @author Alexander Samaniego <awsamaniego@telconet.ec>
 * @version 1.0 22-04-2014
 */
function getMensajesCompElectronico(intIdDocumento) {
    var storeMensajesCompElectronico = new Ext.data.Store({
        pageSize: 10,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_mensajesCompElectronico,
            reader: {
                type: 'json',
                totalProperty: 'intTotalMensajes',
                root: 'storeMensajesCompElectronico'
            },
            extraParams: {
                intIdDocumento: intIdDocumento
            }
        },
        fields:
            [
                {name: 'strTipo', mapping: 'tipo'},
                {name: 'strMensaje', mapping: 'mensaje'},
                {name: 'strInformacionAdicional', mapping: 'informacionAdicional'},
                {name: 'strfeCreacion', mapping: 'feCreacion'}
            ]
    });

    gridMensajesCompElectronico = Ext.create('Ext.grid.Panel', {
        id: 'gridHistorialServicio',
        store: storeMensajesCompElectronico,
        autoScroll: true,
        columnLines: true,
        columns: [{
                //id: 'nombreDetalle',
                header: 'Tipo',
                dataIndex: 'strTipo',
                width: 90,
                sortable: true
            }, {
                header: 'Mensaje',
                dataIndex: 'strMensaje',
                width: 250
            },
            {
                header: 'Informacion Adicional',
                dataIndex: 'strInformacionAdicional',
                width: 478
            },
            {
                header: 'Fecha de Creacion',
                dataIndex: 'strfeCreacion',
                width: 110
            }],
        viewConfig: {
            stripeRows: true
        },
        frame: true,
        height: 200,
        width: 955,
        listeners: {
            itemdblclick: function (view, record, item, index, eventobj, obj)
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
            viewready: function (grid)
            {
                var view = grid.view;

                // record the current cellIndex
                grid.mon(view, {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e) {
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
        },
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeMensajesCompElectronico,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}'
        })
    });

    var frmMensajeComprobante = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'fieldset',
                title: '',
                defaultType: 'textfield',
                defaults: {
                    width: 955
                },
                items: [
                    gridMensajesCompElectronico
                ]
            }
        ],
        buttons: [{
                text: 'Cerrar',
                handler: function () {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Mensajes Comprobantes Electronicos',
        modal: true,
        width: 1000,
        height: 300,
        closable: true,
        resizable: false,
        items: [frmMensajeComprobante]
    }).show();
}
function showProcesar(idfactura) {
    winDetalle = "";
    if (!winDetalle) {

        var form = Ext.widget('form', {
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
            url: url_procesar,
            items: [{
                    xtype: 'combo',
                    id: 'id',
                    name: 'motivos',
                    fieldLabel: 'Motivos',
                    hiddenName: 'motivos',
                    emptyText: 'Seleccione el motivo...',
                    store: store_motivos, // end of Ext.data.SimpleStore
                    displayField: 'descripcion',
                    valueField: 'id',
                    selectOnFocus: true,
                    mode: 'local',
                    typeAhead: true,
                    editable: false,
                    triggerAction: 'all',
                }, {
                    xtype: 'hiddenfield',
                    name: 'idfactura',
                    name: 'idfactura',
                        value: idfactura
                }],
            buttons: [{
                    text: 'Cancel',
                    handler: function () {
                        this.up('form').getForm().reset();
                        this.up('window').hide();
                    }
                }, {
                    text: 'Grabar',
                    handler: function () {
                        var form1 = this.up('form').getForm();
                        if (form1.isValid()) {
                            form1.submit({
                                waitMsg: "Procesando",
                                success: function (form1, action) {
                                    Ext.Msg.alert('Success', 'Se realizo la anulacion');
                                    form1.reset();
                                    if (store) {
                                        store.load();
                                    }
                                },
                                failure: function (form1, action) {
                                    Ext.Msg.alert('Failed', 'Error al ingresar los datos, por favor comunicarse con el departamento de Sistemas');
                                }
                            });
                            this.up('window').hide();
                        }
                    }
                }]
        });

        winDetalle = Ext.widget('window', {
            title: 'Procesar Anulación',
            closeAction: 'hide',
            width: 350,
            height: 300,
            minHeight: 200,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: form
        });

    }
    winDetalle.show();

}


