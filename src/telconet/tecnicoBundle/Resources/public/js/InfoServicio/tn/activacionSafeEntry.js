/**
 * Funcion para seleccionar y cargar los equipos requeridos para la activacion del producto SAFE ENTRY 
 * 
 * @author Leonardo Mero <lemero@telconet.ec>
 * @version 1.0 09-12-2022 - Version inicial
 * 
 */

function confirmarServicioSafeEntry (data, gridIndex)
{
    Ext.get(gridServicios.getId()).mask('Cargando datos');
    Ext.Ajax.request({
        url: getElementosSafeEntry,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();

            var jsonData = Ext.JSON.decode(response.responseText);
            console.log(jsonData)
            var arrayElementos = jsonData.encontrados;
            arrayDataAddElementos = [];

            //-------------------------------------------------------------------------------------------

            if(jsonData.status != "OK")
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: jsonData.mensaje,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
            else
            {
                var storeDispositivosValid = new Ext.data.Store ({
                    autoDestroy: true,
                    data       : [],
                    proxy      : {type: 'memory'},
                    fields     : [
                        'serieElemento',
                        'modeloElemento'
                    ]
                });
                var gridDispositivosValid = Ext.create('Ext.grid.Panel', {
                    id       : 'gridDispositivosValid',
                    width    :  650,
                    height   :  114,
                    store    :  storeDispositivosValid,
                    hidden   :  true,
                    frame    :  false,
                    columns:
                    [
                        {
                            header    : 'Serie',
                            dataIndex : 'serieElemento',
                            width     :  150,
                            sortable  :  false,
                            hideable  :  false
                        },
                        {
                            header    : 'Modelo',
                            dataIndex : 'modeloElemento',
                            width     :  150,
                            sortable  :  false,
                            hideable  :  false
                        }
                    ]
                });
                //-------------------------------------------------------------------------------------------
                var formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding  : 2,
                    waitMsgTarget: true,
                    height: 700, 
                    width: 400,
                    autoScroll: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85, 
                        msgTarget: 'side',
                        bodyStyle: 'padding:20px'
                    },
                    layout: {
                        type: 'table',
                        // The total column count must be specified here
                        columns: 1
                    },
                    defaults: {
                        // applied to each contained panel
                        bodyStyle: 'padding:20px'
                    },
                    items: [
                        //Información del servicio/producto
                        {
                            colspan :  1,
                            xtype   : 'panel',
                            title   : 'Información del Servicio',
                            items:
                            [
                                {
                                    xtype: 'container',
                                    id: 'serviceInfoPanel',
                                    layout: {
                                        type: 'table',
                                        columns: 7,
                                        align: 'stretch'
                                    },
                                    items: [

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'producto',
                                            fieldLabel: 'Producto',
                                            displayField: data.descripcionProducto,
                                            value: data.descripcionProducto,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'login',
                                            fieldLabel: 'Login',
                                            displayField: data.login,
                                            value: data.login,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        { width: '35%', border: false},
                                        { width: '15%', border: false},

                                        //---------------------------------------------
                                    ]
                                }
                            ]
                        },//cierre de la informacion servicio/producto

                        //Información de los elementos del cliente
                        {
                            xtype  : 'panel',
                            title  : 'Información de los Elementos del Cliente',
                            colspan:  1,
                            items:
                            [
                                //Selección Técnico Responsable
                                comboEmpleado(data),

                                //Elementos
                                {
                                    xtype : 'fieldset',
                                    id    : 'nuevoDispositivos',
                                    layout: {
                                        type   : 'table',
                                        pack   : 'center',
                                        columns:  1
                                    },
                                    items:[]
                                }
                            ]
                        },
                        gridDispositivosValid
                    ],
                    buttons: 
                    [{
                        text    : 'Activar',
                        formBind: true,
                        handler : function()
                        {
                            //obtener tecnico
                            var idTecnicoEncargado     = Ext.getCmp('comboFilterTecnico').getValue();
                            if (Ext.isEmpty(idTecnicoEncargado)) {
                                Ext.Msg.show({
                                    title: 'Alerta',msg: 'Por favor seleccione el Técnico Encargado.!',
                                    icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                                    buttonText: {cancel: 'Cerrar'}
                                });
                                return;
                            }
                            //setear parametros
                            var paramsData = {};
                            paramsData['idTecnicoEncargado'] = idTecnicoEncargado;
                            paramsData['idServicio']         = data.idServicio;
                            paramsData['idProducto']         = data.productoId;
                            paramsData['login']              = data.login;
                            paramsData['loginAux']           = data.loginAux;
                            paramsData['idAccion']           = 847;
                            paramsData['strNombreTecnico']   = data.descripcionProducto;
                            //validar elementos
                            for (tipoKeyEle in arrayDataAddElementos){
                                var serie  = Ext.getCmp(arrayDataAddElementos[tipoKeyEle].keySerie).getValue();
                                var modelo = Ext.getCmp(arrayDataAddElementos[tipoKeyEle].keyModelo).getValue();
                                var isMac  = arrayDataAddElementos[tipoKeyEle].mac;
                                var titulo = arrayDataAddElementos[tipoKeyEle].titulo;
                                if(serie=="" || modelo==""){
                                    Ext.Msg.show({
                                        title: 'Alerta',msg: 'Por favor ingrese el elemento '+titulo+'.!',
                                        icon: Ext.Msg.WARNING,buttons: Ext.Msg.CANCEL,
                                        buttonText: {cancel: 'Cerrar'}
                                    });
                                    return;
                                }
                                else {
                                    paramsData[arrayDataAddElementos[tipoKeyEle].keySerie]  = serie;
                                    paramsData[arrayDataAddElementos[tipoKeyEle].keyModelo] = modelo;
                                    if(isMac){
                                        var mac = Ext.getCmp(arrayDataAddElementos[tipoKeyEle].keyMac).getValue();
                                        paramsData[arrayDataAddElementos[tipoKeyEle].keyMac] = mac;
                                    }
                                }
                            }

                            Ext.get(formPanel.getId()).mask('Guardando datos...!');
                            Ext.Ajax.request({
                                url: confirmarActivacionBoton,
                                method: 'post',
                                timeout: 1000000,
                                params: paramsData,
                                success: function(response)
                                {
                                    Ext.get(formPanel.getId()).unmask();
                                    if(response.responseText === "OK")
                                    {
                                        Ext.Msg.alert('Mensaje','Se Confirmo el Cliente', function(btn){
                                            if(btn==='ok')
                                            {
                                                win.destroy();
                                                store.load();
                                            }
                                        });
                                    }
                                    else if(response.responseText === "NO EXISTE PRODUCTO"){
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Mensaje ','No existe el producto, favor revisar en el NAF' );
                                    }
                                    else{
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Mensaje ',response.responseText );
                                    }
                                },
                                failure: function(result)
                                {
                                    Ext.get(formPanel.getId()).unmask();
                                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                                }
                            });
                        }
                    },
                    {
                        text   : 'Cancelar',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }]
                });

                for (tipoKeyElemento in arrayElementos){
                    arrayItemElementos = [];
                    for(contEle = 0; contEle<arrayElementos[tipoKeyElemento].length; contEle++){
                        var dataAddElemento = {};
                        dataAddElemento.titulo         = arrayElementos[tipoKeyElemento][contEle].title;
                        dataAddElemento.tiposElementos = arrayElementos[tipoKeyElemento][contEle].tipos;
                        dataAddElemento.mac            = arrayElementos[tipoKeyElemento][contEle].mac;
                        dataAddElemento.keySerie       = 'serie' + arrayElementos[tipoKeyElemento][contEle].key;
                        dataAddElemento.keyModelo      = 'modelo' + arrayElementos[tipoKeyElemento][contEle].key;
                        dataAddElemento.keyMac         = 'mac' + arrayElementos[tipoKeyElemento][contEle].key;
                        arrayDataAddElementos[arrayElementos[tipoKeyElemento][contEle].key] = dataAddElemento;
                        //crear fieldset
                        arrayItemElementos[contEle] = new Ext.form.FieldSet({
                            id    : 'nuevoEle' + arrayElementos[tipoKeyElemento][contEle].key,
                            title : arrayElementos[tipoKeyElemento][contEle].title,
                            layout: {
                                tdAttrs: {style: 'padding:1px;'},
                                type   : 'table',
                                pack   : 'stretch',
                                columns:  arrayElementos[tipoKeyElemento].length === 1 ? 4 : 2
                            },
                            items: [
                                {
                                    xtype  : 'container',
                                    id     : 'seleccioneDispositivo' + arrayElementos[tipoKeyElemento][contEle].key,
                                    padding: '0 0 10 0',
                                    layout : {
                                        type    : 'table',
                                        pack    : 'left',
                                        columns :  2
                                    },
                                    items: [
                                        {
                                            xtype: 'component',
                                            html : '<label style="color:green;">'+
                                                        '<b>Seleccione el dispositivo</b>'+
                                                '</label>&nbsp;&nbsp;'+
                                                '<label style="color:black;">'+
                                                        '<i class="fa fa-long-arrow-right" aria-hidden="true"></i>' +
                                                '</label>'
                                        },
                                        {
                                            xtype: 'component',
                                            html : '<div id="agregar-dispositivo-'+arrayElementos[tipoKeyElemento][contEle].key+
                                                    '" align="center" title="Seleccionar Dispositivo" style="cursor:pointer;">'+
                                                    '&nbsp;&nbsp<label style="color:#3a87ad;"><i class="fa fa-plus-square fa-lg" '+
                                                    'style="cursor:pointer;" aria-hidden="true" '+
                                                    'onclick="addDispositivosSafeEntry(\''+arrayElementos[tipoKeyElemento][contEle].key+'\',0)"'+
                                                    '></i></label></div>'
                                        },
                                    ]
                                },
                                {
                                    xtype:          'textfield',
                                    id:             dataAddElemento.keySerie,
                                    name:           dataAddElemento.keySerie,
                                    fieldLabel:     'Serie',
                                    displayField:   '',
                                    value:          '',
                                    readOnly:       true,
                                    labelAlign:     arrayElementos[tipoKeyElemento].length === 1 ? 'right' : 'left',
                                    listeners: {
                                    }
                                },
                                {
                                    xtype:          'textfield',
                                    id:             dataAddElemento.keyModelo,
                                    name:           dataAddElemento.keyModelo,
                                    fieldLabel:     'Modelo',
                                    displayField:   '',
                                    valueField:     '',
                                    labelAlign:     arrayElementos[tipoKeyElemento].length === 1 ? 'right' : 'left',
                                    readOnly:       true
                                },
                                {
                                    xtype:          'textfield',
                                    id:             dataAddElemento.keyMac,
                                    name:           dataAddElemento.keyMac,
                                    fieldLabel:     'MAC',
                                    displayField:   '',
                                    value:          '',
                                    labelAlign:     arrayElementos[tipoKeyElemento].length === 1 ? 'right' : 'left',
                                    hidden:         !arrayElementos[tipoKeyElemento][contEle].mac,
                                    readOnly:       true
                                }
                            ]
                        });
                    }
                    if(arrayItemElementos.length == 1){
                        Ext.getCmp('nuevoDispositivos').add(arrayItemElementos);
                    }
                    else{
                        var fieldsetElementos = new Ext.form.FieldSet({
                            xtype : 'fieldset',
                            id    : 'div' + tipoKeyElemento.replace(' ',''),
                            title : tipoKeyElemento,
                            layout: {
                                type   : 'table',
                                pack   : 'center',
                                columns:  2
                            },
                            items: arrayItemElementos
                        });
                        Ext.getCmp('nuevoDispositivos').add(fieldsetElementos);
                    }
                }

                Ext.getCmp('comboFilterTecnico').setVisible(true);
                var win = Ext.create('Ext.window.Window', {
                    title    : "Confirmar Servicio",
                    modal    : true,
                    width    : 1100,
                    closable : true,
                    layout   : 'fit',
                    items    : [formPanel]
                }).show();
            }
        }//cierre response
    });
}


function addDispositivosSafeEntry(name, accion){
    var dataAdd = arrayDataAddElementos[name];
    agregarDispositivos(dataAdd,accion);
}