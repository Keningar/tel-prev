function cortarServicioL3mpls(data,idAccion)
{

    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: urlGetDatosBackboneL3mpls,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio,
            tipoElementoPadre: 'ROUTER'
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            
            var datosBackbone = Ext.JSON.decode(response.responseText);
            
            if(datosBackbone.idElementoPadre=="")
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: datosBackbone.elementoPadre,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                }); 
                return;
            }
            else
            {
                var storeMotivos = new Ext.data.Store({  
                    pageSize: 50,
                    autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        url : getMotivos,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                        extraParams: {
                            accion: "cortarCliente"
                        }
                    },
                    fields:
                        [
                          {name:'idMotivo', mapping:'idMotivo'},
                          {name:'nombreMotivo', mapping:'nombreMotivo'}
                        ]
                });

                var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
                items: [{
                    items: [

                        //informacion de backbone
                            {
                                xtype: 'fieldset',
                                title: 'Informacion de backbone',
                                defaultType: 'textfield',

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
                                                id:'elementoPadre',
                                                name: 'elementoPadre',
                                                fieldLabel: 'Elemento Padre',
                                                displayField: datosBackbone.elementoPadre,
                                                value: datosBackbone.elementoPadre,
                                                readOnly: true,
                                                width: 480
                                            },
                                            {   width: '10%', border: false },
                                            {   width: '10%', border: false },
                                            {   width: '10%', border: false },
                                            //---------------------------------------------
                                           { width: '10%', border: false}, 
                                           {
                                                xtype: 'textfield',
                                                name: 'elemento',
                                                fieldLabel: 'Elemento',
                                                displayField: datosBackbone.elemento,
                                                value: datosBackbone.elemento,
                                                readOnly: true,
                                                width: 480
                                            },
                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'interfaceElemento',
                                                fieldLabel: 'Interface Elemento',
                                                displayField: datosBackbone.interfaceElemento,
                                                value: datosBackbone.interfaceElemento,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            { width: '0%', border: false},
                                            //---------------------------------------------

                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'elementoContenedor',
                                                fieldLabel: 'Elemento Contenedor',
                                                displayField: datosBackbone.elementoContenedor,
                                                value: datosBackbone.elementoContenedor,
                                                readOnly: true,
                                                width: 480
                                            },
                                            { width: '10%', border: false},
                                            { width: '10%', border: false},
                                            { width: '10%', border: false},
                                            //---------------------------------------------
                                            {   width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'elementoConector',
                                                fieldLabel: 'Elemento Conector',
                                                displayField: datosBackbone.elementoConector,
                                                value: datosBackbone.elementoConector,
                                                readOnly: true,
                                                width: 480
                                            },
                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'Hilo',
                                                fieldLabel: 'Hilo',
                                                displayField: datosBackbone.hilo,
                                                value: datosBackbone.hilo,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            { width: '10%', border: false},
                                            //---------------------------------------------

                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'vlan',
                                                fieldLabel: 'Vlan',
                                                displayField: datosBackbone.vlan,
                                                value: datosBackbone.vlan,
                                                readOnly: true,
                                                width: '50%'
                                            },
                                            { width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'vrf',
                                                fieldLabel: 'Vrf',
                                                displayField: datosBackbone.vrf,
                                                value: datosBackbone.vrf,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            { width: '10%', border: false},

                                            //---------------------------------------------

                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'protocolo',
                                                fieldLabel: 'Protocolo',
                                                displayField: datosBackbone.protocolos,
                                                value: datosBackbone.protocolos,
                                                readOnly: true,
                                                width: '50%'
                                            },
                                            { width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'asPrivado',
                                                fieldLabel: 'AS Privado',
                                                displayField: datosBackbone.asPrivado,
                                                value: datosBackbone.asPrivado,
                                                readOnly: true,
                                                width: '30%'
                                            },
                                            { width: '10%', border: false},

                                            //---------------------------------------------

                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                id:'ultimaMilla',
                                                name: 'ultimaMilla',
                                                fieldLabel: 'Ultima Milla',
                                                displayField: datosBackbone.um,
                                                value: datosBackbone.um,
                                                readOnly: true,
                                                width: '50%'
                                            },
                                            { width: '15%', border: false},
                                            { width: '15%', border: false},
                                            { width: '10%', border: false},

                                        ]
                                    }

                                ]
                            },//cierre de info de backbone

                        //informacion del servicio/producto
                            {
                                xtype: 'fieldset',
                                title: 'Informacion del Servicio',
                                defaultType: 'textfield',
                                items: [

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
                                                xtype: 'textfield',
                                                name: 'producto',
                                                fieldLabel: 'Producto',
                                                displayField: data.nombreProducto,
                                                value: data.nombreProducto,
                                                readOnly: true,
                                                width: 480
                                            },
                                            { width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'login',
                                                fieldLabel: 'Login',
                                                displayField: data.login,
                                                value: data.login,
                                                readOnly: true,
                                                width: '50%'
                                            },
                                            { width: '10%', border: false},

                                            //---------------------------------------------

                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'capacidadUno',
                                                fieldLabel: 'Capacidad Uno',
                                                displayField: data.capacidadUno,
                                                value: data.capacidadUno,
                                                readOnly: true,
                                                width: '50%'
                                            },
                                            { width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'capacidadDos',
                                                fieldLabel: 'Capacidad Dos',
                                                displayField: data.capacidadDos,
                                                value: data.capacidadDos,
                                                readOnly: true,
                                                width: '50%'
                                            },
                                            { width: '10%', border: false},
                                           //---------------------------------------------

                                            { width: '10%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'ipServicio',
                                                fieldLabel: 'Ip Wan',
                                                displayField: datosBackbone.ip,
                                                value: datosBackbone.ip,
                                                readOnly: true,
                                                width: '50%'
                                            },
                                            { width: '15%', border: false},
                                            {
                                                xtype: 'textfield',
                                                name: 'macServicio',
                                                fieldLabel: 'Mac',
                                                displayField: datosBackbone.mac,
                                                value: datosBackbone.mac,
                                                readOnly: true,
                                                width: '50%'
                                            },
                                            { width: '10%', border: false},
                                                
                                                //---------------------------------------------

                                                {width: '10%', border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'tipoEnlace',
                                                    fieldLabel: 'Tipo Enlace',
                                                    displayField: data.tipoEnlace,
                                                    value: data.tipoEnlace,
                                                    readOnly: true,
                                                    width: '50%'
                                                },
                                                {width: '15%', border: false},
                                                {width: '15%', border: false},
                                                {width: '10%', border: false}

                                        ]
                                    }

                                ]
                            },//cierre de la informacion servicio/producto
                            //motivo de corte
                            {
                                xtype: 'fieldset',
                                title: 'Motivo Corte',
                                defaultType: 'textfield',
                                items: [

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
                                                xtype: 'combo',
                                                id:'comboMotivos',
                                                name: 'comboMotivos',
                                                store: storeMotivos,
                                                fieldLabel: 'Motivo',
                                                displayField: 'nombreMotivo',
                                                valueField: 'idMotivo',
                                                allowBlank:      false,
                                                queryMode: 'local',
                                                width: 480
                                            },
                                            { width: '15%', border: false},
                                            { width: '30%', border: false},
                                            { width: '10%', border: false}
                                        ]
                                    }

                                ]
                            }//cierre del motivo de cancelacion
                    ]
                }],
                buttons: [{
                    text: 'Ejecutar',
                    formBind: true,
                    handler: function(){
                        var idMotivo     = Ext.getCmp('comboMotivos').getValue();

                        if(idMotivo==null){
                            Ext.Msg.alert("Error","Debe escoger un Motivo de Corte", function(btn){
                                    if(btn==='ok'){
                                        return;
                                    }
                            });
                            return ;
                        }

                        Ext.Msg.alert('Mensaje','Esta seguro que desea Cortar el Servicio?', function(btn){
                            if(btn==='ok'){
                                Ext.MessageBox.wait('Cortando Servicio. Favor espere..');
                                Ext.Ajax.request({
                                    url: urlCortarClienteTN,
                                    method: 'post',
                                    timeout: 300000, 
                                    params: { 
                                        idServicio: data.idServicio,
                                        idProducto: data.productoId,                                    
                                        idMotivo: idMotivo,
                                        idAccion: idAccion,
                                        vlan    : data.vlan,
                                        mac:      datosBackbone.mac,
                                        anillo  : data.anillo,
                                        capacidadUno : data.capacidadUno,
                                        capacidadDos : data.capacidadDos
                                    },
                                    success: function(response){
                                        Ext.MessageBox.hide();
                                        win.hide();

                                        var respuesta = response.responseText;

                                        if( respuesta === "OK")
                                        {
                                            Ext.Msg.alert('Mensaje','Se Corto el Servicio', function(btn){
                                                if(btn==='ok'){
                                                    store.load();
                                                    win.destroy();
                                                }
                                            });
                                        }
                                        else{
                                            Ext.Msg.alert('Mensaje',respuesta, function(btn){
                                                if(btn==='ok'){
                                                    win.show();
                                                }
                                            });
                                        }
                                    },
                                    failure: function(result)
                                    {
                                        Ext.MessageBox.hide();
                                        win.hide();
                                        Ext.Msg.alert('Error',result.statusText, function(btn){
                                            if(btn=='ok'){
                                                win.destroy();
                                            }
                                        });
                                    }
                                }); 
                            }
                        });          

                    }
                },{
                    text: 'Cerrar',
                    handler: function(){
                        win.destroy();
                    }
                }]
            });

                var win = Ext.create('Ext.window.Window', {
                    title: 'Cortar Servicio',
                    modal: true,
                    width: 800,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
            }
        }//cierre Success
        ,
        failure: function(result)
        {
            Ext.get(gridServicios.getId()).unmask();
            Ext.Msg.alert('Error',result.statusText, function(btn){
                if(btn=='ok'){
                    
                }
            });
        }
    });       
}