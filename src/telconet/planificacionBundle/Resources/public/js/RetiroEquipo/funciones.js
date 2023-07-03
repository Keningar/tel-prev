var winRetiroEquipo;

var connRetiroEquipo = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Grabando los datos, Por favor espere!!',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        }
    }
});

var connValidaSerie = new Ext.data.Connection({
    listeners: {
        'beforerequest': {
            fn: function(con, opt) {
                Ext.MessageBox.show({
                    msg: 'Verificando Serie, Por favor espere!!',
                    progressText: 'verificando...',
                    width: 300,
                    wait: true,
                    waitConfig: {interval: 200}
                });
            },
            scope: this
        },
        'requestcomplete': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        },
        'requestexception': {
            fn: function(con, res, opt) {
                Ext.MessageBox.hide();
            },
            scope: this
        }
    }
});

function showRetiroEquipo(rec)
{
    winRetiroEquipo = "";
    if (!winRetiroEquipo)
    {
        var first_time          = true;
        storeResponsables = new Ext.data.Store
        ({
            total: 'total',
            pageSize: 10000,
            listeners: {
                load: function() {
                    
                    if (rec.data.idResponsable > 0 && first_time ) 
                    {
                        var intToTalStore=this.getCount();
                        if(intToTalStore>0)
                        {
                            Ext.getCmp('cmb_responsable').setValue(rec.data.idResponsable);
                        }
                        first_time=false;
                    }
                }
            },
            proxy:
            {
                type: 'ajax',
                method: 'post',
                url: '../../planificar/asignar_responsable/' + rec.data.url_responsable,
                reader:
                {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    query:   rec.data.responsable
                },
                actionMethods: 
                {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                }
            },
            fields:
            [
                {name: rec.data.fieldIdResponsable,     mapping: rec.data.fieldIdResponsable},
                {name: rec.data.fieldValueResponsable,  mapping: rec.data.fieldValueResponsable}
            ],
            autoLoad: true
        });

        combo_responsables = new Ext.form.ComboBox({
            id: 'cmb_responsable',
            name: 'cmb_responsable',
            fieldLabel: "Nombre ",
            anchor: '30%',
            queryMode: 'remote',
            width: 350,
            store: storeResponsables,
            displayField: rec.data.fieldValueResponsable,
            valueField: rec.data.fieldIdResponsable,
            layout: 'anchor',
            disabled: false
        });
        if (rec.get("tercerizadora")) {
            itemTercerizadora = new Ext.form.TextField({
                xtype: 'textfield',
                fieldLabel: 'Tercerizadora',
                name: 'fieldtercerizadora',
                id: 'fieldtercerizadora',
                value: rec.get("tercerizadora"),
                allowBlank: false,
                readOnly: true
            });
        } else {
            itemTercerizadora = Ext.create('Ext.Component', {
                html: "<br>",
            });
        }

        Ext.define('estadoCpeModel', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'estado', type: 'string'}
            ]
        });

        storeEstadoCpe = new Ext.data.Store({
            model: 'estadoCpeModel',
            data: [
                {estado: 'BUENO'},
                {estado: 'MALO'},
                {estado: 'NO ENTREGADO'}
            ]
        });


        storeModelosCpe = new Ext.data.Store({
            pageSize: 1000,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: ajaxGetModelosElemento,
                extraParams: {
                    tipo:   'CPE',
                    forma:  'Empieza con',
                    estado: "Activo"
                },
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'modelo', mapping: 'modelo'},
                    {name: 'codigo', mapping: 'codigo'}
                ]
        });

        storeElementosSolicitud = new Ext.data.Store({
            pageSize: 100,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: 'ajaxGetElementosSolicitud',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    idSolicitud: rec.get("id_factibilidad")
                }
            },
            fields:
                [
                    {name: 'idSolCaract',       mapping: 'idSolCaract'},
                    {name: 'tipoElemento',      mapping: 'tipoElemento'},
                    {name: 'nombreElemento',    mapping: 'nombreElemento'},
                    {name: 'serieElemento',     mapping: 'serieElemento'},
                    {name: 'modeloElemento',    mapping: 'modeloElemento'},
                    {name: 'estadoElemento',    mapping: 'estadoElemento'},
                    {name: 'idElemento',        mapping: 'idElemento'}
                ]
        });

        var cellEditingElementos = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 2,
            listeners: {
                edit: function(editor, object) {
                    var rowIdx = object.rowIdx;
                    var column = object.field;
                    var currentIp = object.value;
                    var storeElemento = gridElementos.getStore().getAt(rowIdx);

                    if (typeof storeElemento != 'undefined') {
                        var tipo = storeElemento.get('tipo');

                        if (column == "serieElemento" || column == "modeloElemento") {
                            var serie = storeElemento.get('serieElemento');
                            var codigo = storeElemento.get('modeloElemento');
                            var estado = storeElemento.get('estadoElemento');
                            var id = storeElemento.get('idElemento');
                            if(estado=="NO ENTREGADO")
                            {
                                var sm = gridElementos.getSelectionModel();
                                var recSelection = sm.getSelection()[0];
                                recSelection.set('serieElemento', '');
                                recSelection.set('modeloElemento', '');
                            }
                            else if (serie != "" && codigo != "" ) 
                            {
                                if (rec.get('buscar_cpe_naf'))
                                {
                                    connValidaSerie.request({
                                        url: "ajaxBuscarCpeNaf",
                                        timeout: 120000,
                                        method: 'post',
                                        params: {
                                            serieCpe: serie,
                                            codigoArticulo: codigo,
                                            idServicio: rec.data.id_servicio,
                                            idElementoCpe: id,
                                            estadoCpe: "IN",
                                            bandera: "RetiroEquipo"
                                        },
                                        success: function(response) {
                                            var respuesta = Ext.decode(response.responseText);

                                            if (respuesta.strMensaje != "") {
                                                Ext.Msg.alert('Mensaje ', respuesta.strMensaje + 
                                                              '<br>Por favor Corregir o no podrá continuar con la Finalización del Retiro de Equipo');
                                                storeElementosSolicitud.load();
                                            } else {
                                                Ext.Msg.alert('Exito', respuesta.strDescripcionCpe);
                                            }
                                        },
                                        failure: function(result) {
                                            storeElementosSolicitud.load();
                                            Ext.Msg.alert('Error', result.responseText + ', Favor Notificar a Sistemas');
                                        }
                                    });
                                }
                            }
                        }
                    }
                }
            }
        });

        gridElementos = Ext.create('Ext.grid.Panel', {
            id: 'gridElementos',
            store: storeElementosSolicitud,
            columnLines: true,
            plugins: [cellEditingElementos],
            columns: [
                {
                    id: 'idSolCaract',
                    header: 'idSolCaract',
                    dataIndex: 'idSolCaract',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'entregado',
                    header: 'entregado',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'idElemento',
                    header: 'idElemento',
                    dataIndex: 'idElemento',
                    hidden: true,
                    hideable: false
                },
                {
                    header: 'Tipo Elemento',
                    dataIndex: 'tipoElemento',
                    width: 90,
                },
                {
                    header: 'Elemento',
                    dataIndex: 'nombreElemento',
                    width: 155,
                },
                {
                    header: 'Serie',
                    dataIndex: 'serieElemento',
                    width: 180,
                    editor: {
                        xtype: 'textfield',
                        valueField: ''
                    }
                },
                {
                    header: 'Modelo',
                    dataIndex: 'modeloElemento',
                    width: 150,
                    sortable: true,
                    editor: {
                        queryMode: 'local',
                        editable: false,
                        xtype: 'combobox',
                        displayField: 'modelo',
                        valueField: 'modelo',
                        loadingText: 'Buscando...',                        
                        listeners: {
                             el: {
                                 click: function() {
                                        storeModelosCpe.proxy.extraParams = { 
                                        tipo:   gridElementos.getSelectionModel().getSelection()[0].get('tipoElemento'),
                                        forma:  'Empieza con',
                                        estado: "Activo"
                                      };
                                        storeModelosCpe.load({params: {}});
                                  },
                                  scope: this
                                 }
                         },                        
                        store: storeModelosCpe
                    }
                },
                {
                    header: 'Estado',
                    dataIndex: 'estadoElemento',
                    width: 180,
                    sortable: true,
                    editor: {
                        queryMode: 'local',
                        editable: false,
                        xtype: 'combobox',
                        displayField: 'estado',
                        valueField: 'estado',
                        loadingText: 'Buscando...',
                        store: storeEstadoCpe,
                        listeners: {
                            select:
                            function(combo, records)
                            {
                                    var sm = gridElementos.getSelectionModel();
                                    var recSelection = sm.getSelection()[0];
                                    if(combo.getValue()=="NO ENTREGADO")
                                    {
                                        recSelection.set('serieElemento', '');
                                        recSelection.set('modeloElemento', '');
                                    }
                            }
                        }
                    }
                }],
            viewConfig: {
                stripeRows: true
            },
            frame: true,
            height: 200,
            title: 'Elementos'
        });

        formPanelRetiroEquipo = Ext.create('Ext.form.Panel', {
            BodyPadding: 10,
            autoScroll: true,
            buttonAlign: 'center',
            frame: true,
            items: [
                {
                    xtype: 'panel',
                    border: false,
                    frame: true,
                    layout: {type: 'hbox', align: 'stretch'},
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Cliente',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 15px;",
                            layout: 'anchor',
                            defaults: {
                                width: '350px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Cliente',
                                    name: 'info_cliente',
                                    id: 'info_cliente',
                                    value: rec.get("cliente"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Login',
                                    name: 'info_login',
                                    id: 'info_login',
                                    value: rec.get("login2"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Ciudad',
                                    name: 'info_ciudad',
                                    id: 'info_ciudad',
                                    value: rec.get("ciudad"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Direccion',
                                    name: 'info_direccion',
                                    id: 'info_direccion',
                                    value: rec.get("direccion"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Sector',
                                    name: 'info_nombreSector',
                                    id: 'info_nombreSector',
                                    value: rec.get("nombreSector"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Es Recontratacion',
                                    name: 'es_recontratacion',
                                    id: 'es_recontratacion',
                                    value: rec.get("esRecontratacion"),
                                    allowBlank: false,
                                    readOnly: true
                                }
                            ]
                        },
                        {
                            xtype: 'fieldset',
                            title: 'Datos del Servicio',
                            defaultType: 'textfield',
                            style: "font-weight:bold; margin-bottom: 15px;",
                            defaults: {
                                width: '350px'
                            },
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Servicio',
                                    name: 'info_servicio',
                                    id: 'info_servicio',
                                    value: rec.get("producto"),
                                    allowBlank: false,
                                    readOnly: true
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Tipo Orden',
                                    name: 'tipo_orden_servicio',
                                    id: 'tipo_orden_servicio',
                                    value: rec.get("tipo_orden"),
                                    allowBlank: false,
                                    readOnly: true
                                }, itemTercerizadora
                            ]
                        }
                    ]
                }
                ,
                {
                    xtype: 'fieldset',
                    title: 'Custodio Asignado',
                    defaultType: 'textfield',
                    style: "font-weight:bold; margin-top: 15px;",
                    defaults: {
                        width: '350px'
                    },
                    items: [combo_responsables]
                },
                //informacion del cpe
                {
                    xtype: 'fieldset',
                    title: 'Informacion de los Elementos',
                    defaultType: 'textfield',
                    defaults: {
                        width: 750
                    },
                    items: [gridElementos
                    ]//cierre del fieldset
                }//cierre informacion cpe
            ],
            buttons: [
                {
                    text: 'Finalizar',
                    handler: function() {
                        var id_solicitud = rec.get("id_factibilidad");
                        var id_responsable = Ext.getCmp('cmb_responsable').value;
                        var mensajeError = "";
                        var boolError = false;
                        var datosElementos = getInfoElementos();

                        if (parseInt(datosElementos) == 1) {
                            boolError = true;
                            mensajeError  = "Por favor ingrese la(s) serie(s) o lo(s) modelos de lo(s) elemento(s)";
                            mensajeError += "que tengan estado BUENO ó MALO para poder finalizar";
                        }

                        if (!id_responsable) 
                        {
                            boolError = true;
                            mensajeError  = "Por favor seleccione un custodio asignado";
                        }

                        if (!boolError)
                        {
                            connRetiroEquipo.request({
                                url: "ajaxFinalizarRetiroEquipo",
                                method: 'post',
                                params:
                                    {
                                        idSolicitud:        id_solicitud,
                                        idResponsable:      id_responsable,
                                        datosElementos:     datosElementos,
                                        buscarCpeNaf:       rec.get('buscar_cpe_naf') ? "SI" : "NO"
                                    },
                                success: function(response) {
                                    var json = Ext.JSON.decode(response.responseText);
                                    
                                    if(json.success == true)
                                    {						      
                                        Ext.Msg.alert('Mensaje', json.msg, function(btn) {
                                            if (btn == 'ok') {
                                                cierraVentanaRetiroEquipo();
                                                store.load();
                                            }
                                        });
                                    }
                                    else 
                                    {
                                        storeElementosSolicitud.load();
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: json.msg,
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                                    }
                                },
                                failure: function(result) {
                                    storeElementosSolicitud.load();
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: 'Ha ocurrido un problema. Por favor informar a Sistemas!',
                                        buttons: Ext.MessageBox.OK,
                                        icon: Ext.MessageBox.ERROR
                                    });
                                }
                            });
                        } 
                        else 
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: mensajeError,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                        }
                    }
                },
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaRetiroEquipo();
                    }
                }
            ]
        });

        winRetiroEquipo = Ext.widget('window', {
            title: 'Finalizar - Retiro Equipo',
//             width: 1030,
//             height: 650,
//             minHeight: 380,
            layout: 'fit',
            resizable: false,
            closabled: false,
            items: [formPanelRetiroEquipo]
        });
    }

    winRetiroEquipo.show();
}

function getCodigoByModelo(modelo)
{
    for (var i = 0; i < storeModelosCpe.getCount(); i++)
    {
        if (storeModelosCpe.getAt(i).data.modelo == modelo) {
            return storeModelosCpe.getAt(i).data.codigo;
        }
    }

    return "";

}

function getInfoElementos()
{
    var responseElementos = new Object();
    responseElementos['total'] = gridElementos.getStore().getCount();
    responseElementos['elementos'] = new Array();

    var arrayElemento = new Array();

    for (var i = 0; i < gridElementos.getStore().getCount(); i++)
    {   //Se agrega que a la roseta no sea obligatorio ni modelo
        if ((gridElementos.getStore().getAt(i).data.serieElemento == "" || gridElementos.getStore().getAt(i).data.modeloElemento == "") 
             && (gridElementos.getStore().getAt(i).data.estadoElemento != "NO ENTREGADO") 
             && (gridElementos.getStore().getAt(i).data.tipoElemento != "ROSETA"))
        {
            return 1;
        }
        // se añade validacion, cuando se selecciona estado NO ENTREGADO se validan campos 
        if (gridElementos.getStore().getAt(i).data.estadoElemento != "NO ENTREGADO")
        {
            gridElementos.getStore().getAt(i).data.entregado        = "si";
            gridElementos.getStore().getAt(i).data.serieElemento    = gridElementos.getStore().getAt(i).data.serieElemento;
            gridElementos.getStore().getAt(i).data.modeloElemento   = gridElementos.getStore().getAt(i).data.modeloElemento;
            arrayElemento.push(gridElementos.getStore().getAt(i).data);
        }
        else 
        {
            gridElementos.getStore().getAt(i).data.entregado        = "no";
            gridElementos.getStore().getAt(i).data.serieElemento    = "";
            gridElementos.getStore().getAt(i).data.modeloElemento   = "";
            arrayElemento.push(gridElementos.getStore().getAt(i).data);
        }

    }

    responseElementos['elementos'] = arrayElemento;
    return Ext.JSON.encode(responseElementos);
}

function buscarEquipoNaf(rec) 
{
    if (Ext.getCmp('serieCpe').getValue() != "" && Ext.getCmp('modeloCpe').value) 
    {
        Ext.MessageBox.wait("Buscando Cpe...");
        Ext.Ajax.request({
            url: "ajaxBuscarCpeNaf",
            method: 'post',
            params: 
            {
                serieCpe: Ext.getCmp('serieCpe').getValue(),
                codigoArticulo: Ext.getCmp('modeloCpe').value,
                idServicio: rec.data.id_servicio,
                idElementoCpe: Ext.getCmp('idElemento').value
            },
            success: function(response) 
            {
                Ext.MessageBox.hide();
                var respuesta = Ext.decode(response.responseText);

                if (respuesta.strMensaje != "")
                {
                    Ext.Msg.alert('Mensaje ', respuesta.strMensaje + ', <BR> NO PODRA CONTINUAR CON LA FINALIZACION DEL RETIRO DEL EQUIPO');

                    var nombreCpe = "";
                    var descripcionCpe = "";
                    var macCpe = "";
                    var modoCpe = "";

                    Ext.getCmp('serieCpe').setValue = "";
                    Ext.getCmp('serieCpe').setRawValue("");

                    Ext.getCmp('modeloCpe').value = "";
                    Ext.getCmp('modeloCpe').setRawValue("");
                    Ext.getCmp('estadoCpe').value = "";
                    Ext.getCmp('estadoCpe').setRawValue("");
                } 
                else 
                {

                    var nombreCpe = respuesta.strNombreCpe;
                    var descripcionCpe = respuesta.strDescripcionCpe;
                    var macCpe = respuesta.strMacCpe;
                    var modoCpe = respuesta.strModoCpe;
                }

                Ext.getCmp('nombreCpe').setValue = nombreCpe;
                Ext.getCmp('nombreCpe').setRawValue(nombreCpe);

                Ext.getCmp('descripcionCpe').setValue = descripcionCpe;
                Ext.getCmp('descripcionCpe').setRawValue(descripcionCpe);

                Ext.getCmp('macCpe').setValue = macCpe;
                Ext.getCmp('macCpe').setRawValue(macCpe);


                Ext.getCmp('modoCpe').setValue = modoCpe;
                Ext.getCmp('modoCpe').setRawValue(modoCpe);

            },
            failure: function(result)
            {
                Ext.MessageBox.hide();
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: result.statusText,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        });
    }


}
function cierraVentanaRetiroEquipo() {
    winRetiroEquipo.close();
    winRetiroEquipo.destroy();
}