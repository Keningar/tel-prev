//declaro el panel global
var formPanel;
var storeLineasConsulta;
function consultarTelefonia(idServicio, estado)
{
    var intento = 0;
    
    storeLineasConsulta = new Ext.data.Store({
        pageSize: 50,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_verLineasTelefonicas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: idServicio
            }
        },
        fields:
            [
                {name: 'idNumero', mapping: 'idNumero'},
                {name: 'numero', mapping: 'numero'},
                {name: 'idDominio', mapping: 'idDominio'},
                {name: 'dominio', mapping: 'dominio'},
                {name: 'idClave', mapping: 'idClave'},
                {name: 'clave', mapping: 'clave'},
                {name: 'idNumeroCanales', mapping: 'idNumeroCanales'},
                {name: 'numeroCanales', mapping: 'numeroCanales'},
                {name: 'estado', mapping: 'estado'}
            ]
    });

    
    Ext.get(document.body).mask('Por Favor Espere...');
    

    Ext.Ajax.request({
        url: url_gestionarLineasTelefonicas,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: idServicio,
            opcion: 'INFO_TECNICA'
        },
        success: function (response) {
            var json = Ext.JSON.decode(response.responseText);
            
            Ext.get(document.body).unmask();

            //grid de usuarios
            var gridCorreos = Ext.create('Ext.grid.Panel', {
                id: 'gridCorreos',
                store: storeLineasConsulta,
                columnLines: true,
                dockedItems: [toolbar],
                columns: [
                    {
                        header: 'Numero',
                        dataIndex: 'numero',
                        width: 85,
                        sortable: true
                    },
                    {
                        header: 'Dominio',
                        dataIndex: 'dominio',
                        width: 110,
                        sortable: true
                    },
                    {
                        header: 'Clave',
                        dataIndex: 'clave',
                        width: 75,
                        sortable: true
                    },
                    {
                        header: 'Canales',
                        dataIndex: 'numeroCanales',
                        width: 55,
                        sortable: true
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: 70
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Accion',
                        width: 100,
                        items: [
                            {
                                getClass: function (v, meta, rec) {
                                    var permiso = $("#ROLE_415-6046");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    
                                    if (boolPermiso &&  (rec.get('estado') == "In-Corte" || rec.get('estado') == "Activo")) 
                                    {
                                        return 'button-grid-verCorreo';
                                    }
                                    else
                                    {
                                        return 'button-grid-invisible';
                                    }

                                },
                                tooltip: 'Enviar detalle llamada',
                                handler: function (grid, rowIndex) {
                                    detalleLlamada(grid.getStore().getAt(rowIndex).data);
                                }
                            }
                        ]
                    }
                ],
                viewConfig: {
                    stripeRows: true
                },

                frame: true,
                height: 200
            });

            var btnCortar;
            var btnReactivar;

            var permiso = $("#ROLE_415-6047");
            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);


            if(estado == 'Activo' && boolPermiso)
            {
                btnCortar = Ext.create('Ext.Button', {
                    text: 'Cortar',
                    iconCls: 'icon_corteMasivo',
                    handler: function () {
                        if(intento == 0)
                        {
                            intento = 1;
                            cortarLlamadasSalientes(idServicio);
                        }
                        else
                        {
                            Ext.Msg.alert('Mensaje', 'Las llamadas salientes se encuentran cortadas.');    
                            win.destroy();
                            store.load();
                        }                        
                    }
                });        
                
            }
            

            var permiso2 = $("#ROLE_415-6048");
            var boolPermiso2 = (typeof permiso2 === 'undefined') ? false : (permiso2.val() == 1 ? true : false);                                    
            
            if(estado == 'In-Corte' && boolPermiso2)
            {
                btnReactivar = Ext.create('Ext.Button', {
                    text: 'Reactivar',
                    iconCls: 'icon_reactivacionMasiva',
                    handler: function () {
                        if(intento == 0)
                        {
                            intento = 1;
                            activarLlamadaSaliente(idServicio);
                        }
                        else
                        {
                            Ext.Msg.alert('Mensaje', 'Las llamadas salientes se encuentran activadas.');
                            win.destroy();
                            store.load();
                        }
                    }
                });
            }

            formPanel = Ext.create('Ext.form.Panel', {
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
                            width: 530
                        },
                        items: [
                            //um actual
                            {
                                colspan: 2,
                                rowspan: 2,
                                xtype: 'fieldset',
                                title: 'Informacion Tecnica',
                                defaults: {
                                    height: 120
                                },
                                items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 5,
                                            align: 'stretch'
                                        },
                                        items:
                                            [
                                                {width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'nombreElemento',
                                                    fieldLabel: 'Nombre Elemento',
                                                    displayField: json.nombreElemento, 
                                                    value: json.nombreElemento,
                                                    readOnly: true,
                                                    labelStyle: 'font-weight:bold',
                                                    width: '40%'
                                                },
                                                {width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'modeloElemento',
                                                    fieldLabel: 'Modelo Elemento',
                                                    displayField: json.modeloElemento,
                                                    value: json.modeloElemento,
                                                    readOnly: true,
                                                    labelStyle: 'font-weight:bold',
                                                    width: '40%'
                                                },
                                                {width: '10%', border: false},
                                                {width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'serie',
                                                    fieldLabel: 'Serie',
                                                    displayField: json.serie, 
                                                    value: json.serie,
                                                    readOnly: true,
                                                    labelStyle: 'font-weight:bold',
                                                    width: '40%'
                                                },
                                                {width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'mac',
                                                    fieldLabel: 'Mac',
                                                    displayField: json.mac,
                                                    value: json.mac,
                                                    readOnly: true,
                                                    labelStyle: 'font-weight:bold',
                                                    width: '40%'
                                                },
                                                {width: '10%', border: false},                                                

                                                //-----------------------------------

                                                {width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'categoria',
                                                    fieldLabel: 'Categoria',
                                                    displayField: json.categoriaTelefonia,
                                                    value: json.categoriaTelefonia,
                                                    readOnly: true,
                                                    labelStyle: 'font-weight:bold',
                                                    width: '40%'
                                                },
                                                {width: '15%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'proveedor',
                                                    fieldLabel: 'Proveedor',
                                                    displayField: json.proveedor,
                                                    value: json.proveedor,
                                                    readOnly: true,
                                                    labelStyle: 'font-weight:bold',
                                                    width: '40%'
                                                },
                                                {width: '10%', border: false},

                                                //------------------------------------------------

                                                {width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'planTelefonia',
                                                    fieldLabel: 'Plan Telefonia',
                                                    displayField: json.planTelefonia,
                                                    value: json.planTelefonia,
                                                    readOnly: true,
                                                    labelStyle: 'font-weight:bold',
                                                    width: '40%'
                                                },
                                                {width: '15%', border: false},
                                                
                                                //aqui va el otro bloquesito
                                                
                                                {width: '10%', border: false},

                                                
                                                {width: '15%', border: false}
                                            ]
                                    }
                                ]
                            },
                            gridCorreos

                        ]
                    }//cierre interfaces cpe
                ],
                buttons: [                   
                    btnCortar,
                    btnReactivar,                    
                    {
                        text: 'Cerrar',
                        handler: function () {
                            win.destroy();
                            store.load();
                        }
                    }
                ]
            });

            var win = Ext.create('Ext.window.Window', {
                title: 'Lineas Telefónicas',
                modal: true,
                width: 580,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();

        }

    });

}

function cortarLlamadasSalientes(idServicio)
{
    Ext.Msg.alert('Mensaje', '¿Está seguro que desea cortar las llamadas salientes?', function (btn) {
        if (btn == 'ok') {

            Ext.get(formPanel.getId()).mask('Procesando...');
            Ext.Ajax.request({
                url: url_gestionarLineasTelefonicas,
                method: 'post',
                timeout: 400000,
                params: {
                    opcion: 'CORTAR',
                    idServicio: idServicio                    
                },
                success: function (response) {
                    Ext.get(formPanel.getId()).unmask();

                    if (response.responseText == "OK") {
                        Ext.Msg.alert('Mensaje', 'Transacción Exitosa.', function (btn) {
                            if (btn == 'ok') {
                                storeLineasConsulta.load();
                            }
                        });
                    } else {

                        Ext.Msg.alert('Error', response.responseText);
                    }

                }

            });
        }
    });
}


function detalleLlamada(data)
{
    Ext.apply(Ext.form.field.VTypes,
        {
            daterange: function (val, field)
            {
                var date = field.parseDate(val);

                if (!date)
                {
                    return false;
                }

                if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime())))
                {
                    var start = field.up('form').down('#' + field.startDateField);
                    start.setMaxValue(date);
                    start.validate();
                    this.dateRangeMax = date;
                } else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime())))
                {
                    var end = field.up('form').down('#' + field.endDateField);
                    end.setMinValue(date);
                    end.validate();
                    this.dateRangeMin = date;
                }

                return true;
            }
        });
    //Campo Fecha Desde General
    var dateFechaDesde = new Ext.form.DateField
        ({
            id: 'dateFechaDesde',
            fieldLabel: 'Desde',
            xtype: 'datefield',
            format: 'd-m-Y',
            editable: false,
            name: 'dateFechaDesde',
            maxValue: Ext.Date.add(new Date()),
            vtype: 'daterange',
            endDateField: 'dateFechaHasta',
            allowBlank: false,
        });

    //Campo Fecha Hasta General
    var dateFechaHasta = new Ext.form.DateField
        ({
            id: 'dateFechaHasta',
            fieldLabel: 'Hasta',
            xtype: 'datefield',
            format: 'd-m-Y',
            maxValue: Ext.Date.add(new Date()),
            editable: false,
            name: 'dateFechaHasta',
            vtype: 'daterange',
            startDateField: 'dateFechaDesde',
            allowBlank: false,
        });


    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'container',
                autoScroll: true,

                layout: {
                type: 'table',
                columns: 1,
                align: 'stretch'
                },

                items: [
                    dateFechaDesde,
                    dateFechaHasta,
                    {

                        xtype: 'textfield',
                        id: 'correo',
                        name: 'correo',
                        fieldLabel: 'Correo ',
                        displayField: "",
                        value: '',
                        width: '180',
                        vtype: 'email',
                        allowBlank: false,
                    }
                ]
            }
        ],
        buttons: [{
                text: 'Enviar',
                formBind: true,
                handler: function () {

                    Ext.get(formPanel.getId()).mask('Procesando...');

                    var startDate = new Date(Ext.getCmp('dateFechaDesde').getValue());
                    startDate.setHours(0, 0, 0, 0);
                    
                    var endDate = new Date(Ext.getCmp('dateFechaHasta').getValue());
                    endDate.setHours(23, 59, 59, 999);

                    Ext.Ajax.request({
                        url: url_gestionarLineasTelefonicas,
                        method: 'post',
                        timeout: 400000,
                        params: {
                            opcion: 'DETALLE_LLAMADA',
                            idNumero: data.idNumero,
                            fechaInicio: startDate,
                            fechaFin: endDate,
                            correo: Ext.getCmp('correo').getValue()
                        },
                        success: function (response) {
                            Ext.get(formPanel.getId()).unmask();

                            if (response.responseText == "OK") {
                                Ext.Msg.alert('Mensaje', 'Transacción Exitosa.', function (btn) {
                                    if (btn == 'ok') {
                                        win.destroy();
                                    }
                                });
                            } else {

                                Ext.Msg.alert('Error', response.responseText);
                            }
                        }
                    });
                }
            }, {
                text: 'Cancelar',
                handler: function () {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Enviar Informe Detallado',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function cancelarLineasNetvoice(idServicio)
{
    Ext.Msg.alert('Mensaje', '¿Está seguro de que desea cancelar todos los números?', function (btn) {
        if (btn == 'ok') {
            Ext.get(document.body).mask('Por Favor Espere...');
            Ext.Ajax.request({
                url: url_gestionarLineasTelefonicas,
                method: 'post',
                timeout: 400000,
                params: {
                    idServicio: idServicio,
                    opcion: 'CANCELAR_LINEAS'
                    
                },
                success: function (response) {
                    Ext.get(document.body).unmask();

                    if (response.responseText == "OK") {
                        Ext.Msg.alert('Mensaje', 'Transacción Exitosa', function (btn) {
                            if (btn == 'ok') {
                                store.load();
                            }
                        });
                    } else {
                        Ext.Msg.alert('Mensaje', response.responseText);
                    }
                }
            });
        }
    });
}

function activarLlamadaSaliente(idServicio)
{
    Ext.Msg.alert('Mensaje', '¿Está seguro que desea activar las llamadas salientes?', function (btn) {
        if (btn == 'ok') {
            Ext.get(formPanel.getId()).mask('Procesando...');
            Ext.Ajax.request({
                url: url_gestionarLineasTelefonicas,
                method: 'post',
                timeout: 400000,
                params: {
                    opcion: 'RECONECTAR',
                    idServicio: idServicio
                },
                success: function (response) {
                    Ext.get(formPanel.getId()).unmask();

                    if (response.responseText == "OK") {
                        Ext.Msg.alert('Mensaje', 'Transacción Exitosa.', function (btn) {
                            if (btn == 'ok') {
                                storeLineasConsulta.load();
                            }
                        });
                    } else {

                        Ext.Msg.alert('Error', response.responseText);
                    }
                }
            });
        }
    });
}

