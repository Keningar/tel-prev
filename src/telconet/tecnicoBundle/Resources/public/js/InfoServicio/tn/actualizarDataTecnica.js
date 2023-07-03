/*
 * Funcion utilizada para actualizar información de cpe TN
 * 
 * @author Jenniffer Mujica <jmujica@telconet.ec>
 * @version 1.0 23-09-2022 
 * @since 1.0
 */
function actualizaDataTecnicaCpe(data)
{
    Ext.get(gridServicios.getId()).mask('Consultando Información...');
    Ext.Ajax.request({
        url: getDataTecnicaCpe,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.idServicio,
            idEmpresa: data.idEmpresa,
            prefijoEmpresa: data.prefijoEmpresa
        },
        success: function(response) {
            Ext.get(gridServicios.getId()).unmask();

            var datosTecnicoCpe = Ext.JSON.decode(response.responseText);

            var storeEmpleados = new Ext.data.Store({
                total: 'total',
                pageSize: 200,
                proxy: {
                    type: 'ajax',
                    method: 'post',
                    url: url_empleadosPorEmpresa,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        nombre: '',
                        estado: 'Activo'
                    }
                },
                fields:
                    [
                        {name: 'intIdPersonEmpresaRol', mapping: 'intIdPersonEmpresaRol'},
                        {name: 'strNombresEmpleado', mapping: 'strNombresEmpleado'}
                    ]
            });

            var storeEstadoNaf = Ext.create('Ext.data.Store', {
                fields: ['opcion', 'valor'],
                data:
                    [
                        {
                            "opcion": "Pendiente de Instalar",
                            "valor": "PI"
                        }, {
                            "opcion": "Instalado",
                            "valor": "IN"
                        },
                        {
                            "opcion": "Pendiente de Retirar",
                            "valor": "PR"
                        },
                        {
                            "opcion": "Retirado",
                            "valor": "RE"
                        },
                        {
                            "opcion": "Ingresado a bodega",
                            "valor": "IB"
                        },
                        {
                            "opcion": "No Entregado",
                            "valor": "NE"
                        }
                    ]
            });

            var formPanel = Ext.create('Ext.form.Panel',{
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
            },
            items:[{
                items:[
                    //Informacion de Cpe
                    {
                        xtype: 'fieldset',
                        title: 'Información Cpe Existente',
                        defaultType: 'textfield',
                        defaults: {
                            width: 600,
                            layout: 'fit'
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch',
                                },
                                items: [
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'serieCpe',
                                        name: 'serieCpe',
                                        fieldLabel: '<b>Serie Cpe</b>',
                                        displayField: datosTecnicoCpe.serieCpe,
                                        value: datosTecnicoCpe.serieCpe,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    {width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'estadoNaf',
                                        name: 'estadoNaf',
                                        fieldLabel: '<b>Estado Naf</b>',
                                        displayField: datosTecnicoCpe.estadoNaf,
                                        value: datosTecnicoCpe.estadoNaf,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    {width: '10%', border: false},
                                    //---------------------------------------------
                                    {width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'nombreElemento',
                                        fieldLabel: '<b>Nombre</b>',
                                        displayField: datosTecnicoCpe.nombreElemento,
                                        value: datosTecnicoCpe.nombreElemento,
                                        readOnly: true,
                                        width: '35%'
                                    },
                                    {width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'estadoTelcos',
                                        fieldLabel: '<b>Estado Telcos</b>',
                                        displayField: datosTecnicoCpe.estadoTelcos,
                                        value: datosTecnicoCpe.estadoTelcos,
                                        readOnly: true,
                                        width: '35%'
                                    },
                                    {width: '10%', border: false},
                                    //-----------------------------------------------
                                    {width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'macCpe',
                                        name: 'macCpe',
                                        fieldLabel: '<b>Mac</b>',
                                        displayField: datosTecnicoCpe.macCpe,
                                        value: datosTecnicoCpe.macCpe,
                                        disabled: true,
                                        width: '35%'
                                    },
                                    {width: '15%', border: false},
                                    {
                                        xtype: 'button',
                                        id: 'btnEditar',
                                        text: '',
                                        ui:'',
                                        cls: 'button-editar',
                                        handler: function()
                                        {
                                            let disable = Ext.getCmp('macCpe').disabled;
                                            if(disable)
                                            {
                                                Ext.getCmp('macCpe').enable();
                                            }
                                            else
                                            {
                                                Ext.getCmp('macCpe').disable();
                                            }
                                            //window.location = urlEditarEmpresaExterna;
                                        }
                                    },
                                    {width: '10%', border: false},
                                    //-----------------------------------------------------------
                                    {width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        name: 'ipCpe',
                                        fieldLabel: '<b>Ip</b>',
                                        displayField: datosTecnicoCpe.ipCpe,
                                        value: datosTecnicoCpe.ipCpe,
                                        readOnly: true,
                                        width: '35%'
                                    },
                                    {width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'custodio',
                                        name: 'custodio',
                                        fieldLabel: '<b>Custodio</b>',
                                        displayField: datosTecnicoCpe.custodio,
                                        value: datosTecnicoCpe.custodio,
                                        readOnly: true,
                                        width: '35%'
                                    },
                                    //--------------------------------------------------------
                                    {width: '10%', border: false},
                                    {width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id: 'usuarioCustodio',
                                        name: 'usuarioCustodio',
                                        fieldLabel: '<b>Usuario Custodio</b>',
                                        displayField: datosTecnicoCpe.usuarioCustodio,
                                        value: datosTecnicoCpe.usuarioCustodio,
                                        readOnly: true,
                                        width: '35%'
                                    },
                                ]
                            }
                        ]
                    },
                    {width: '10%', border: false},
                    //-----------Data Tecnica a cambiar Cpe Existente--------------------------
                    {
                        xtype: 'fieldset',
                        title: 'Información de Cpe para realizar Actualización de Data Tecnica',
                        defaultType: 'textfield',
                        default: {
                            width: 600,
                            layout: 'fit'
                        },
                        items:[
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch'
                                },
                                items: [
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'combobox',
                                        id: 'nafNuevo',
                                        name: 'nafNuevo',
                                        fieldLabel: '<b>Estado Naf</b>',
                                        store: storeEstadoNaf,
                                        displayField: 'opcion',
                                        valueField: 'valor',
                                        queryMode: "remote",
                                        emptyText: '',
                                        forceSelection: true,
                                        width: '35%'
                                    },
                                    {width: '10%', border: false},
                                    {width: '10%', border: false}
                                ]
                            }
                        ]
                    },
                    {width: '10%', border: false},
                    //-----------Custodio del cpe nuevo-----------------
                    {
                        xtype: 'fieldset',
                        title: 'Información de Cpe Nueva para realizar el cambio de Custodio',
                        defaultType: 'textfield',
                        defaults: {
                            width: 600,
                            layout: 'fit'
                        },
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 5,
                                    align: 'stretch'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        id: 'serieNueva',
                                        name: 'serieNueva',
                                        fieldLabel: '<b>Serie Nueva</b>',
                                        displayField: '',
                                        value: '',
                                        width: '30%'
                                    },
                                    {width: '10%', border: false},
                                    {
                                        id: 'cbmcustodio',
                                        name: 'cbmcustodio',
                                        xtype: 'combobox',
                                        fieldLabel: '<b>Custodio</b>',
                                        store: storeEmpleados,
                                        queryMode: 'remote',
                                        displayField: 'strNombresEmpleado',
                                        valueField: 'intIdPersonEmpresaRol',
                                        width: '20%',
                                        emptyText: '',
                                        forceSelection: true
                                    },
                                    {width: '10%', border: false},
                                    {width: '10%', border: false}
                                ]
                            }
                        ]
                    },
                    {width: '10%', border: false},
                    
                ]
            }],
            buttons: [{
                text: 'Ejecutar',
                formBind: true,
                handler: function(){
                    
                    var idResponsable         = Ext.getCmp('cbmcustodio').getValue();
                    var serieCpeNueva         = Ext.getCmp('serieNueva').getValue();
                    var estadoNafNueva        = Ext.getCmp('nafNuevo').getValue();
                    var disableMac            = Ext.getCmp('macCpe').disabled;
                    var macNueva              = null;
                    var serieAntigua          = Ext.getCmp('serieCpe').getValue();
                    var estadoNafAnt          = Ext.getCmp('estadoNaf').getValue();

                    if(!disableMac)
                    {
                        macNueva = Ext.getCmp('macCpe').getValue();
                    }
                    else
                    {
                        macNueva = "";
                    }

                    //validar que llene campos a modificar
                    if (Ext.isEmpty(estadoNafNueva) && Ext.isEmpty(macNueva)&& 
                        Ext.isEmpty(idResponsable) && Ext.isEmpty(serieCpeNueva))
                    {
                        Ext.Msg.show({
                            title     : 'Alerta',
                            msg       : 'Ingrese y/o seleccione los datos a ser modificados.',
                            icon      :  Ext.Msg.WARNING,
                            buttons   :  Ext.Msg.CANCEL,
                            buttonText: {cancel: 'Cerrar'}
                        });
                        return;
                    }

                    if (!Ext.isEmpty(serieCpeNueva) && Ext.isEmpty(idResponsable))
                    {
                        Ext.Msg.show({
                            title     : 'Alerta',
                            msg       : 'Seleccione empleado responsable para cambio de custodio.',
                            icon      :  Ext.Msg.WARNING,
                            buttons   :  Ext.Msg.CANCEL,
                            buttonText: {cancel: 'Cerrar'}
                        });
                        return;
                    }

                    if (!Ext.isEmpty(idResponsable) && Ext.isEmpty(serieCpeNueva))
                    {
                        Ext.Msg.show({
                            title     : 'Alerta',
                            msg       : 'Ingrese serie de cpe a cambiar el custodio.',
                            icon      :  Ext.Msg.WARNING,
                            buttons   :  Ext.Msg.CANCEL,
                            buttonText: {cancel: 'Cerrar'}
                        });
                        return;
                    }

                    //valida que serie anterior y nueva no sean iguales
                    if(!Ext.isEmpty(serieCpeNueva) && (serieCpeNueva === serieAntigua))
                    {
                        Ext.Msg.show({
                            title     : 'Alerta',
                            msg       : 'Serie Nueva coincide con Serie Cpe Actual, favor verificar los datos',
                            icon      :  Ext.Msg.WARNING,
                            buttons   :  Ext.Msg.CANCEL,
                            buttonText: {cancel: 'Cerrar'}
                        });
                        return;
                    }
                    
                    //actualizar custodio y que estado naf este en IN
                    if ((estadoNafAnt != 'Instalado' && !Ext.isEmpty(estadoNafAnt)) && 
                        (Ext.isEmpty(estadoNafNueva) || !Ext.isEmpty(estadoNafNueva)) && 
                        (!Ext.isEmpty(idResponsable) && !Ext.isEmpty(serieCpeNueva)) )
                    {
                        Ext.Msg.show({
                            title     : 'Alerta',
                            msg       : 'No se puede actualizar dicha informacion, verifique el estado del elemento',
                            icon      :  Ext.Msg.WARNING,
                            buttons   :  Ext.Msg.CANCEL,
                            buttonText: {cancel: 'Cerrar'}
                        });
                        return;
                    }

                    //actualizar custodio y que estado naf este lleno , notif a bodega
                    if (Ext.isEmpty(estadoNafAnt) && (Ext.isEmpty(estadoNafNueva) || !Ext.isEmpty(estadoNafNueva)) && 
                        (!Ext.isEmpty(idResponsable) && !Ext.isEmpty(serieCpeNueva)) )
                    {
                        Ext.Msg.show({
                            title     : 'Alerta',
                            msg       : 'No se puede actualizar dicha informacion, serie no encontrada en Naf. Escalar Tarea a Bodega',
                            icon      :  Ext.Msg.WARNING,
                            buttons   :  Ext.Msg.CANCEL,
                            buttonText: {cancel: 'Cerrar'}
                        });
                        return;
                    }

                    //notificar a bodega
                    if ((Ext.isEmpty(estadoNafAnt) && Ext.isEmpty(macNueva)) && !Ext.isEmpty(estadoNafNueva))
                    {
                        Ext.Msg.show({
                            title     : 'Alerta',
                            msg       : 'No se puede actualizar dicha informacion, serie no encontrada en Naf. Escalar Tarea a Bodega',
                            icon      :  Ext.Msg.WARNING,
                            buttons   :  Ext.Msg.CANCEL,
                            buttonText: {cancel: 'Cerrar'}
                        });
                        return;
                    }

                    //validar MAC
                    if(!(macNueva === ""))
                    {
                        if(!macNueva.match("[a-fA-f0-9]{4}[\.]+[a-fA-f0-9]{4}[\.]+[a-fA-F0-9]{4}$"))
                        {
                            Ext.Msg.alert('Mensaje ', 'Formato de Mac Incorrecto (xxxx.xxxx.xxxx), Favor Revisar');
                            return;
                        }                         
                    } 

                    Ext.Msg.confirm({
                        title: 'Alerta',
                        msg: 'Esta seguro de Actualizar los datos?',
                        buttons: Ext.Msg.YESNO,
                        icon: Ext.MessageBox.QUESTION,
                        buttonText: {
                            yes: 'Si', no: 'No'
                        },
                        fn: function (btn){
                            if(btn == 'yes'){
                                Ext.MessageBox.wait('Actualizando, Por favor espere..');

                                Ext.Ajax.request({
                                    url: urlActualizacionDataTecnicaCpe,
                                    method: 'post',
                                    timeout: 100000,
                                    params: {
                                        idServicio: data.idServicio,
                                        serieNueva: serieCpeNueva,
                                        idEmpresa: data.idEmpresa,
                                        idElemento: datosTecnicoCpe.idElemento,
                                        idInterface: datosTecnicoCpe.idInterface,
                                        serie: datosTecnicoCpe.serieCpe,
                                        estado: datosTecnicoCpe.estadoTelcos,
                                        macAnterior: datosTecnicoCpe.macCpe,
                                        nafAnterior: datosTecnicoCpe.estadoNaf,
                                        macNueva: macNueva,
                                        nafNueva: estadoNafNueva,
                                        idResponsable: idResponsable
                                    },
                                    success: function(response) {
                                        Ext.MessageBox.hide();
                                        win.hide();
            
                                        var respuesta = Ext.JSON.decode(response.responseText);
            
                                        Ext.Msg.alert('Mensaje', respuesta.mensaje, function(btn) {
                                            if (btn === 'ok') {
                                                store.load();
                                                win.destroy();
                                            }
                                        });
            
                                    },
                                    failure: function(result)
                                    {
                                        Ext.MessageBox.hide();
                                        win.hide();
                                        Ext.Msg.alert('Error', result.statusText, function(btn) {
                                            if (btn == 'ok') {
                                                win.destroy();
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    });
                }
            },
            {
                text: 'Cerrar',
                handler: function() {
                    win.destroy();
                }
            }]      
    });
    var win = Ext.create('Ext.window.Window', {
        title: 'Actualizar Data Tecnica Cpe',
        modal: true,
        width: 650,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
    },
        failure: function(result)
        {
            Ext.get(gridServicios.getId()).unmask();
            Ext.Msg.alert('Error', result.statusText, function(btn) {
                if (btn == 'ok') {
                    win.destroy();
                }
            });
        }
    }); 
}