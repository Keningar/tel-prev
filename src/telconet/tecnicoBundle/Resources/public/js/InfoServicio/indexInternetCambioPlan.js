/* Funcion que sirve para mostrar la pantalla de cambio de
 * plan y realiza la llamada ajax para la ejecucion de scripts
 * y actualizacion en la base de datos sobre el servicio, para la
 * empresa TTCO
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 */
function cambioPlanCliente(data){
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
                accion: "cambioVelocidad"
            }
        },
        fields:
            [
              {name:'idMotivo', mapping:'idMotivo'},
              {name:'nombreMotivo', mapping:'nombreMotivo'}
            ]
    });
    
    var storeCambioPlanes = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getPlanesPorEstado,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                plan: data.nombrePlan
            }
        },
        fields:
                  [
                    {name:'idPlan', mapping:'idPlan'},
                    {name:'nombrePlan', mapping:'nombrePlan'},
                    {name:'valorCapacidad1', mapping:'valorCapacidad1'},
                    {name:'valorCapacidad2', mapping:'valorCapacidad2'},
                    {name:'valorCapacidad3', mapping:'valorCapacidad3'},
                    {name:'valorCapacidad4', mapping:'valorCapacidad4'},
                    {name:'total', mapping:'total'}
                  ],
        autoLoad: true
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

            //informacion de la velocidad actual
            {
                xtype: 'fieldset',
                title: 'Velocidad Actual',
                defaultType: 'textfield',
                defaults: {
                    width: 600
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
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'planActual',
                                fieldLabel: 'Plan Actual',
                                displayField: data.nombrePlan,
                                value: data.nombrePlan,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '0%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'login',
                                fieldLabel: 'Login',
                                displayField: data.login,
                                value: data.login,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '0%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
//                                id:'capacidadUno',
                                name: 'capacidadUno',
                                fieldLabel: 'Capacidad Uno',
                                displayField: data.capacidadUno,
                                value: data.capacidadUno,
//                                queryMode: 'local',
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
//                                id:'capacidadDos',
                                name: 'capacidadDos',
                                fieldLabel: 'Capacidad Dos',
                                displayField: data.capacidadDos,
                                value: data.capacidadDos,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------
                            
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
//                                id:'capacidadUno',
                                name: 'capacidadTres',
                                fieldLabel: 'Capacidad Promo/Int Uno',
                                displayField: data.capacidadTres,
                                value: data.capacidadTres,
//                                queryMode: 'local',
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
//                                id:'capacidadDos',
                                name: 'capacidadCuatro',
                                fieldLabel: 'Capacidad Promo/Int Dos',
                                displayField: data.capacidadCuatro,
                                value: data.capacidadCuatro,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------
                           
                        ]
                    }

                ]
            },//cierre de velocidad actual

            //velocidad nueva
            {
                xtype: 'fieldset',
                title: 'Nueva Velocidad',
                defaultType: 'textfield',
                defaults: {
                    width: 600
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
                            { width: '10%', border: false},
                            {
                                xtype: 'combo',
                                id:'comboPlanNuevo',
                                name: 'comboPlanNuevo',
                                store: storeCambioPlanes,
                                fieldLabel: 'Plan Nuevo',
                                displayField: 'nombrePlan',
                                valueField: 'idPlan',
                                queryMode: 'local',
                                width: '30%',
                                listeners: {
                                    select: function(combo){
                                        for (var i = 0;i< storeCambioPlanes.data.items.length;i++)
                                        {
                                            if (storeCambioPlanes.data.items[i].data.idPlan == combo.getValue())
                                            {
                                                //console.log("entre");
                                                Ext.getCmp('capacidadUnoNuevo').setValue = storeCambioPlanes.data.items[i].data.valorCapacidad1;
                                                Ext.getCmp('capacidadDosNuevo').setValue = storeCambioPlanes.data.items[i].data.valorCapacidad2;
                                                
                                                Ext.getCmp('capacidadUnoNuevo').setRawValue(storeCambioPlanes.data.items[i].data.valorCapacidad1);
                                                Ext.getCmp('capacidadDosNuevo').setRawValue(storeCambioPlanes.data.items[i].data.valorCapacidad2);
                                                
                                                Ext.getCmp('capacidadTresNuevo').setValue = storeCambioPlanes.data.items[i].data.valorCapacidad3;
                                                Ext.getCmp('capacidadCuatroNuevo').setValue = storeCambioPlanes.data.items[i].data.valorCapacidad4;
                                                
                                                Ext.getCmp('capacidadTresNuevo').setRawValue(storeCambioPlanes.data.items[i].data.valorCapacidad3);
                                                Ext.getCmp('capacidadCuatroNuevo').setRawValue(storeCambioPlanes.data.items[i].data.valorCapacidad4);
                                                
                                                Ext.getCmp('total').setValue = storeCambioPlanes.data.items[i].data.total;
                                                Ext.getCmp('total').setRawValue(storeCambioPlanes.data.items[i].data.total);
                                                
                                                break;
                                            }
                                        }
                                    }
                                }//cierre listener
                            },
                            { width: '15%', border: false},
                            { width: '30%', border: false},
                            { width: '10%', border: false},

                            //---------------------------------------------
                            
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id:'capacidadUnoNuevo',
                                name: 'capacidadUnoNuevo',
                                fieldLabel: 'Capacidad Uno Nuevo',
                                displayField: "",
                                value: "",
//                                queryMode: 'local',
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                id:'capacidadDosNuevo',
                                name: 'capacidadDosNuevo',
                                fieldLabel: 'Capacidad Dos Nuevo',
                                displayField: "",
                                value: "",
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------
                            
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id:'capacidadTresNuevo',
                                name: 'capacidadTresNuevo',
                                fieldLabel: 'Capacidad Promo/Inter Uno Nuevo',
                                displayField: "",
                                value: "",
//                                queryMode: 'local',
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                id:'capacidadCuatroNuevo',
                                name: 'capacidadCuatroNuevo',
                                fieldLabel: 'Capacidad Promo/Inter Dos Nuevo',
                                displayField: "",
                                value: "",
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------
                            
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id:'total',
                                name: 'total',
                                fieldLabel: 'Precio $',
                                displayField: "",
                                value: "",
//                                queryMode: 'local',
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                id:'totalNuevo',
                                name: 'totalNuevo',
                                fieldLabel: 'Precio Nuevo $',
                                displayField: 0,
                                value: 0,
                                width: '30%'
                            },
                            { width: '10%', border: false}

                            //---------------------------------------------
                        ]
                    }

                ]
            }//cierre velocidad nueva

        ],//cierre items

        buttons: [{
            text: 'Ejecutar',
            formBind: true,
            handler: function(){
                Ext.get(formPanel.getId()).mask('Esperando Respuesta del Elemento...');
                var planId = Ext.getCmp('comboPlanNuevo').getValue();
                var precioViejo = Ext.getCmp('total').getValue();
                var precioNuevo = Ext.getCmp('totalNuevo').getValue();
                var cap1 = Ext.getCmp('capacidadUnoNuevo').getValue();
                var cap2 = Ext.getCmp('capacidadDosNuevo').getValue();
                
                var validacion = false;
                if(Number(precioNuevo)>0){
                    if(Number(precioNuevo) > Number(precioViejo)){
                        validacion=true;
                    }
                    else{
                        validacion=false;
                    }
                }
                else{
                    validacion=true;
                }
                if(validacion){
                    Ext.Ajax.request({
                        url: cambioVelocidad,
                        method: 'post',
                        timeout: 400000,
                        params: { 
                            idServicio: data.idServicio,
                            planId: planId,
                            precioViejo: precioViejo,
                            precioNuevo: precioNuevo,
                            capacidad1: cap1,
                            capacidad2: cap2
                        },
                        success: function(response){
                            Ext.get(formPanel.getId()).unmask();
                            var objData     = Ext.JSON.decode(response.responseText);
                            var strStatus   = objData.status;
                            var strMensaje  = objData.mensaje;
                            if(strStatus == "OK") {
                                Ext.Msg.alert('Mensaje', 'Se Cambio el Plan del Cliente: '+data.login, function (btn) {
                                    if (btn == 'ok') {
                                        win.destroy();
                                        store.load();
                                    }
                                });
                            }else{
                                Ext.Msg.alert('Mensaje ', strMensaje);
                            }
                        },
                        failure: function(result)
                        {
                            Ext.get(formPanel.getId()).unmask();
                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                        }
                    });   
                }
                else{
                    Ext.Msg.alert("Failed","El nuevo precio tiene que ser mayor al actual.!", function(btn){
                            if(btn=='ok'){
                                Ext.get(formPanel.getId()).unmask();
                            }
                    });
                }

            }
        },{
            text: 'Cancelar',
            handler: function(){
                win.destroy();
            }
        }]
    });
    
    var win = Ext.create('Ext.window.Window', {
        title: 'Cambio de Velocidad',
        modal: true,
        width: 610,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

/* Funcion que sirve para mostrar la pantalla de cambio de
 * plan y realiza la llamada ajax para la ejecucion de scripts
 * y actualizacion en la base de datos sobre el servicio, para la
 * empresa MD
 * 
 * @author      Francisco Adum <fadum@telconet.ec>
 * @version     1.0     17-10-2014
 * @param Array data        Informacion que fue cargada en el grid
 * 
 * @author      Daniel Reyes <djreyes@telconet.ec>
 * @version     2.0     03-03-2021 - Se agrega nuevo metodo para validar peticion de producto y el orden de carga de los metodos
 * 
 * @author Jose Cruz <jfcruzc@telconet.ec>
 * @version  2.1 se agrega el campo de motivos al cambio de plan
 */
function cambioPlanClienteMd(data){
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
                accion: "cambioVelocidad"
            }
        },
        fields:
            [
              {name:'idMotivo', mapping:'idMotivo'},
              {name:'nombreMotivo', mapping:'nombreMotivo'}
            ]
    });

    var storeCambioPlanes = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : getPlanesPorEstado,
            timeout: 400000,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                plan: data.nombrePlan
            }
        },
        fields:
                  [
                    {name:'idPlan', mapping:'idPlan'},
                    {name:'nombrePlan', mapping:'nombrePlan'},
                    {name:'valorCapacidad1', mapping:'valorCapacidad1'},
                    {name:'valorCapacidad2', mapping:'valorCapacidad2'},
                    {name:'valorCapacidad3', mapping:'valorCapacidad3'},
                    {name:'valorCapacidad4', mapping:'valorCapacidad4'},
                    {name:'total', mapping:'total'}
                  ],
       // autoLoad: true
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

            //informacion de la velocidad actual
            {
                xtype: 'fieldset',
                title: 'Velocidad Actual',
                defaultType: 'textfield',
                defaults: {
                    width: 600
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
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'planActual',
                                fieldLabel: 'Plan Actual',
                                displayField: data.nombrePlan,
                                value: data.nombrePlan,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '0%', border: false},
                            {
                                xtype: 'textfield',
                                name: 'login',
                                fieldLabel: 'Login',
                                displayField: data.login,
                                value: data.login,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '0%', border: false},

                            //---------------------------------------------

                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id:'capacidadUnoActual',
                                name: 'capacidadUno',
                                fieldLabel: 'Capacidad Uno',
                                displayField: data.capacidadUno,
                                value: data.capacidadUno,
//                                queryMode: 'local',
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                id:'capacidadDosActual',
                                name: 'capacidadDos',
                                fieldLabel: 'Capacidad Dos',
                                displayField: data.capacidadDos,
                                value: data.capacidadDos,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------
                            
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
//                                id:'capacidadUno',
                                name: 'capacidadTres',
                                fieldLabel: 'Capacidad Promo/Int Uno',
                                displayField: data.capacidadTres,
                                value: data.capacidadTres,
//                                queryMode: 'local',
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
//                                id:'capacidadDos',
                                name: 'capacidadCuatro',
                                fieldLabel: 'Capacidad Promo/Int Dos',
                                displayField: data.capacidadCuatro,
                                value: data.capacidadCuatro,
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------
                           
                        ]
                    }

                ]
            },//cierre de velocidad actual

            //velocidad nueva
            {
                xtype: 'fieldset',
                title: 'Nueva Velocidad',
                defaultType: 'textfield',
                defaults: {
                    width: 600
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
                            { width: '10%', border: false},
                            {
                                xtype: 'combobox',
                                fieldLabel: 'Tipo Plan',
                                id: 'cmbTipoPlan',
                                name: 'cmbTipoPlan',
                                value:'',
                                store: [
                                        ['HOME','HOME'],
                                        ['PYME','PYME'],
                                        ['PRO','PRO']                                   
                                ],
                                listeners:{
                                        select: {
                                            fn:function(e){
                                                  Ext.getCmp('PROM_MPLA').setDisabled(true);
                                                  document.getElementById('PROM_MPLA-inputEl').value = "";
                                                  strIdTipoPromoMens = "";
                                                  Ext.getCmp('PROM_BW').setDisabled(true);
                                                  document.getElementById('PROM_BW-inputEl').value = "";
                                                  strIdTipoPromoMens = "";
                                                  Ext.getCmp('comboPlanNuevo').setDisabled(false);                                                
                                                  Ext.getCmp('comboPlanNuevo').setValue('');
                                                  Ext.getCmp('comboPlanNuevo').setRawValue('');                                           
                                                  Ext.getCmp('capacidadUnoNuevo').setValue = '';
                                                  Ext.getCmp('capacidadUnoNuevo').setRawValue("");
                                                  Ext.getCmp('capacidadDosNuevo').setValue = '';
                                                  Ext.getCmp('capacidadDosNuevo').setRawValue("");
                                                  Ext.getCmp('capacidadTresNuevo').setValue = '';
                                                  Ext.getCmp('capacidadTresNuevo').setRawValue("");
                                                  Ext.getCmp('capacidadCuatroNuevo').setValue = '';
                                                  Ext.getCmp('capacidadCuatroNuevo').setRawValue("");
                                                  Ext.getCmp('total').setValue = '';
                                                  Ext.getCmp('total').setRawValue("");
                                                  Ext.getCmp('totalNuevo').setValue = 0;
                                                  Ext.getCmp('totalNuevo').setRawValue(0);
                                                  
                                                  storeCambioPlanes.proxy.extraParams = { tipoPlan:e.getValue()};
                                                  storeCambioPlanes.load();
                                              } 
                                            }
                                        }
                            },
                            { width: '15%', border: false},
                            { width: '30%', border: false},
                            { width: '10%', border: false},
                            
                            { width: '10%', border: false},
                            {
                                xtype: 'combo',
                                id:'comboPlanNuevo',
                                name: 'comboPlanNuevo',
                                store: storeCambioPlanes,
                                fieldLabel: 'Plan Nuevo',
                                displayField: 'nombrePlan',
                                valueField: 'idPlan',
                                queryMode: 'remote',
                                disabled:true,
                                width: '30%',
                                listeners: {
                                    select: function(combo){
                                        if (strRolIngresarCodigoPromocion)
                                        {
                                            Ext.getCmp('PROM_MPLA').setDisabled(false);
                                            Ext.getCmp('PROM_BW').setDisabled(false);
                                        }
                                        document.getElementById('PROM_MPLA-inputEl').value = "";
                                        strIdTipoPromoMens = "";
                                        document.getElementById('PROM_BW-inputEl').value = "";
                                        strIdTipoPromoBw = "";
                                        for (var i = 0;i< storeCambioPlanes.data.items.length;i++)
                                        {
                                            if (storeCambioPlanes.data.items[i].data.idPlan == combo.getValue())
                                            {
                                                //console.log("entre");
                                                Ext.getCmp('PROM_MPLA').setValue = "";
                                                strIdTipoPromoMens = "";
                                                Ext.getCmp('PROM_BW').setValue = "";
                                                strIdTipoPromoBw = "";
                                                Ext.getCmp('capacidadUnoNuevo').setValue = storeCambioPlanes.data.items[i].data.valorCapacidad1;
                                                Ext.getCmp('capacidadDosNuevo').setValue = storeCambioPlanes.data.items[i].data.valorCapacidad2;
                                                
                                                Ext.getCmp('capacidadUnoNuevo').setRawValue(storeCambioPlanes.data.items[i].data.valorCapacidad1);
                                                Ext.getCmp('capacidadDosNuevo').setRawValue(storeCambioPlanes.data.items[i].data.valorCapacidad2);
                                                
                                                Ext.getCmp('capacidadTresNuevo').setValue = storeCambioPlanes.data.items[i].data.valorCapacidad3;
                                                Ext.getCmp('capacidadCuatroNuevo').setValue = storeCambioPlanes.data.items[i].data.valorCapacidad4;
                                                
                                                Ext.getCmp('capacidadTresNuevo').setRawValue(storeCambioPlanes.data.items[i].data.valorCapacidad3);
                                                Ext.getCmp('capacidadCuatroNuevo').setRawValue(storeCambioPlanes.data.items[i].data.valorCapacidad4);
                                                
                                                Ext.getCmp('total').setValue = storeCambioPlanes.data.items[i].data.total;
                                                Ext.getCmp('total').setRawValue(storeCambioPlanes.data.items[i].data.total);
                                                
                                                break;
                                            }
                                        }
                                    }
                                }//cierre listener
                            },
                            { width: '15%', border: false},
                            { width: '30%', border: false},
                            { width: '10%', border: false},

                            {   id:'comboMotivosCell1',
                                width: '10%', 
                                border: false,
                                listeners: {
                                    render: function(p) {
                                        if (!consultarEmpresaMotivoCambioPlan()){
                                            Ext.getCmp("comboMotivosCell1").setVisible(false);
                                        }
                                    }
                                }
                            },
                            {
                                xtype: 'combo',
                                id:'comboMotivos',
                                name: 'comboMotivos',
                                store: storeMotivos,
                                fieldLabel: 'Motivo',
                                displayField: 'nombreMotivo',
                                valueField: 'nombreMotivo',
                                queryMode: 'local',
                                listeners: {
                                    render: function(p) {
                                        if (!consultarEmpresaMotivoCambioPlan()){
                                            Ext.getCmp("comboMotivos").setVisible(false);
                                        }
                                    }
                                }
                            },
                            { id:'comboMotivosCell2', width: '15%', border: false,
                                listeners: {
                                    render: function(p) {
                                        if (!consultarEmpresaMotivoCambioPlan()){
                                            Ext.getCmp("comboMotivosCell2").setVisible(false);
                                        }
                                    }
                                }
                            },
                            { id:'comboMotivosCell3', width: '30%', border: false,
                                listeners: {
                                    render: function(p) {
                                        if (!consultarEmpresaMotivoCambioPlan()){
                                            Ext.getCmp("comboMotivosCell3").setVisible(false);
                                        }
                                    }
                                }
                            },
                            { id:'comboMotivosCell4', width: '10%', border: false,
                                listeners: {
                                    render: function(p) {
                                        if (!consultarEmpresaMotivoCambioPlan()){
                                            Ext.getCmp("comboMotivosCell4").setVisible(false);
                                        }
                                    }
                                }
                            },
                            //---------------------------------------------
                            
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id:'capacidadUnoNuevo',
                                name: 'capacidadUnoNuevo',
                                fieldLabel: 'Capacidad Uno Nuevo',
                                displayField: "",
                                value: "",
//                                queryMode: 'local',
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                id:'capacidadDosNuevo',
                                name: 'capacidadDosNuevo',
                                fieldLabel: 'Capacidad Dos Nuevo',
                                displayField: "",
                                value: "",
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------
                            
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id:'capacidadTresNuevo',
                                name: 'capacidadTresNuevo',
                                fieldLabel: 'Capacidad Promo/Inter Uno Nuevo',
                                displayField: "",
                                value: "",
//                                queryMode: 'local',
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                id:'capacidadCuatroNuevo',
                                name: 'capacidadCuatroNuevo',
                                fieldLabel: 'Capacidad Promo/Inter Dos Nuevo',
                                displayField: "",
                                value: "",
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '10%', border: false},

                            //---------------------------------------------
                            
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id:'total',
                                name: 'total',
                                fieldLabel: 'Precio $',
                                displayField: "",
                                value: "",
//                                queryMode: 'local',
                                readOnly: true,
                                width: '30%'
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                id:'totalNuevo',
                                name: 'totalNuevo',
                                fieldLabel: 'Precio Nuevo $',
                                displayField: 0,
                                value: 0,
                                width: '30%'
                            },
                            { width: '10%', border: false}

                            //------------PROMOCIONES----------------------
                            ,{ width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id:'PROM_MPLA',
                                name: 'PROM_MPLA',
                                fieldLabel: 'Código Promoción Mensualidad',
                                displayField: "",
                                value: "",
                                width: '30%',
                                disabled:true,
                                enableKeyEvents: true,
                                listeners: {
                                keyup: function(form, e){
                                    convertirTextoEnMayusculas('PROM_MPLA-inputEl','PROM_MPLA');
                                    },
                                blur: function () {
                                          validaCodigo('PROM_MPLA', data.idServicio);
                                    }
                                }
                            },
                            { width: '15%', border: false},
                            {
                                xtype: 'textfield',
                                id:'PROM_BW',
                                name: 'PROM_BW',
                                fieldLabel: 'Código Promoción Ancho de Banda',
                                displayField: 0,
                                value: "",
                                width: '30%',
                                disabled:true,
                                enableKeyEvents: true,
                                listeners: {
                                keyup: function(form, e){
                                    convertirTextoEnMayusculas('PROM_BW-inputEl','PROM_BW');
                                    },
                                blur: function (field, e) {
                                        validaCodigo('PROM_BW', data.idServicio);
                                    }
                                }
                            },
                            { width: '10%', border: false}
                            //---------------------------------------------
                        ]
                    }

                ]
            }//cierre velocidad nueva

        ],//cierre items

        buttons: [{
            text: 'Ejecutar',
            id: 'Ejecutar_MD',
            disabled: false, 
            //formBind: true,
            handler: function(){
                var motivo = Ext.getCmp('comboMotivos').getValue();
                console.log("Motivo cambio de plan: "+motivo);
                Ext.get(formPanel.getId()).mask('Verificando información ingresada...');
                var planId = Ext.getCmp('comboPlanNuevo').getValue();
                var precioViejo = Ext.getCmp('total').getValue();
                var precioNuevo = Ext.getCmp('totalNuevo').getValue();
                var cap1 = Ext.getCmp('capacidadUnoNuevo').getValue();
                var cap2 = Ext.getCmp('capacidadDosNuevo').getValue();
                var cap1Actual = Ext.getCmp('capacidadUnoActual').getValue();
                var cap2Actual = Ext.getCmp('capacidadDosActual').getValue();
                var strTipoPlanActual = data.strTipoPlan;
                var strTipoPlanNuevo  = Ext.getCmp('cmbTipoPlan').getValue();
                var codigoPromocionMens = Ext.getCmp('PROM_MPLA').getValue();
                var codigoPromocionBw = Ext.getCmp('PROM_BW').getValue();
                
                var validacion = false;
                if(Number(precioNuevo)>0){
                    if(Number(precioNuevo) > Number(precioViejo)){
                        validacion=true;
                    }
                    else{
                        validacion=false;
                    }
                }
                else{
                    validacion=true;
                }
                if(validacion){
                    Ext.Ajax.request({
                        url: strUrlVerificaServiciosDualBandCambioPlan,
                        method: 'post',
                        timeout: 900000,
                        params: { 
                            intIdServicioInternet: data.idServicio,
                            intIdPlanNuevo: planId
                        },
                        success: function(response){
                            Ext.get(formPanel.getId()).unmask();
                            var objData     = Ext.JSON.decode(response.responseText);
                            var strStatus   = objData.status;
                            var strMensaje  = objData.mensaje;
                            var strNecesitaConfirmacion = objData.necesitaConfirmacion;
                            if(strStatus == "OK") {
                                var objInfo = {
                                    motivo: motivo,
                                    idServicio: data.idServicio,
                                    planId: planId,
                                    strTipoPlanActual: strTipoPlanActual,
                                    strTipoPlanNuevo: strTipoPlanNuevo,
                                    precioViejo: precioViejo,
                                    precioNuevo: precioNuevo,
                                    cap1: cap1,
                                    cap2: cap2,
                                    cap1Actual: cap1Actual,
                                    cap2Actual: cap2Actual,
                                    formPanel: formPanel,
                                    strCodigoMens : codigoPromocionMens,
                                    idTipoPromoMens: strIdTipoPromoMens,
                                    strCodigoBw : codigoPromocionBw,
                                    idTipoPromoBw: strIdTipoPromoBw,
                                    win: win
                                };
                                if(strNecesitaConfirmacion == "SI") {
                                    Ext.Msg.alert('Mensaje', strMensaje, function (btn) {
                                        if (btn == 'ok') {
                                            //procesaCambioPlanClienteMd(objInfo);
                                            verificaProductoNuevoPlan(objInfo);
                                        }
                                    });
                                } else {
                                    //procesaCambioPlanClienteMd(objInfo);
                                    verificaProductoNuevoPlan(objInfo);
                                }
                            } else {
                                Ext.Msg.alert('Mensaje ', strMensaje);
                            }
                        },
                        failure: function(result)
                        {
                            Ext.get(formPanel.getId()).unmask();
                            Ext.Msg.alert('Error ','Error: ' + result.statusText);
                        }
                    });
                }
                else{
                    Ext.Msg.alert("Failed","El nuevo precio tiene que ser mayor al actual.!", function(btn){
                            if(btn=='ok'){
                                Ext.get(formPanel.getId()).unmask();
                            }
                    });
                }

            }
        },{
            text: 'Cancelar',
            handler: function(){
                win.destroy();
            }
        }]
    });
    
    var win = Ext.create('Ext.window.Window', {
        title: 'Cambio de Velocidad',
        modal: true,
        width: 610,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
    
    if (prefijoEmpresa === "TNP")
    {
        Ext.getCmp('cmbTipoPlan').setVisible(false);
        storeCambioPlanes.load();
        
        Ext.getCmp('comboPlanNuevo').setDisabled(false);                                                
        Ext.getCmp('comboPlanNuevo').setValue('');
        Ext.getCmp('comboPlanNuevo').setRawValue('');                                           
        Ext.getCmp('capacidadUnoNuevo').setValue = '';
        Ext.getCmp('capacidadUnoNuevo').setRawValue("");
        Ext.getCmp('capacidadDosNuevo').setValue = '';
        Ext.getCmp('capacidadDosNuevo').setRawValue("");
        Ext.getCmp('capacidadTresNuevo').setValue = '';
        Ext.getCmp('capacidadTresNuevo').setRawValue("");
        Ext.getCmp('capacidadCuatroNuevo').setValue = '';
        Ext.getCmp('capacidadCuatroNuevo').setRawValue("");
        Ext.getCmp('total').setValue = '';
        Ext.getCmp('total').setRawValue("");
        Ext.getCmp('totalNuevo').setValue = 0;
        Ext.getCmp('totalNuevo').setRawValue(0);
    }
}

function procesaCambioPlanClienteMd(objInfo){
    var strConservarIp = "";
    if (objInfo.strTipoPlanActual === "PYME" && objInfo.strTipoPlanNuevo === "PYME") {
        Ext.get(objInfo.formPanel.getId()).mask('Verificando información de planes PYME...');
        Ext.Ajax.request({
            url: strUrlVerificaIpWanCambioPlan,
            method: 'post',
            timeout: 900000,
            params: {
                intIdServicioInternet: objInfo.idServicio,
                intIdPlanNuevo: objInfo.planId,
            },
            success: function(response){
                Ext.get(objInfo.formPanel.getId()).unmask();
                var objData     = Ext.JSON.decode(response.responseText);
                var strStatusIp   = objData.strStatus;
                var strMensajeIp  = objData.strMensaje;
                var strMostrarConfirmacion = objData.strMostrarConfirmacion;
                var strPlanNuevoConIp      = objData.strPlanNuevoConIp;
                if(strStatusIp == "OK") {
                    if (strMostrarConfirmacion === "SI" && strPlanNuevoConIp === "NO") {
                        strConservarIp = "NO";
                        Ext.MessageBox.show({
                            title      : 'Mensaje',
                            msg        : 'Al realizar el cambio de plan seleccionado se generará un valor adicional '+
                                         'de la IP en la WAN. Al no aceptar se realizará el cambio de Plan y se '+
                                         'cancelará la IP en la WAN. ¿Deseas continuar?',
                            closable   : false,
                            multiline  : false,
                            icon       : Ext.Msg.QUESTION,
                            buttons    : Ext.Msg.YESNO,
                            buttonText : {yes: 'Si', no: 'No'},
                            fn: function (buttonValue)
                            {
                                if(buttonValue === 'yes')
                                {
                                    strConservarIp = "SI";
                                }
                                ejecutaCambioPlanClienteMd({...objInfo, strConservarIp: strConservarIp});
                            }
                        });
                    } else {
                        ejecutaCambioPlanClienteMd({...objInfo, strConservarIp: strConservarIp});
                    }
                } else {
                    Ext.Msg.alert('Error ','Error: ' + strMensajeIp);
                    Ext.Msg.alert('Mensaje ', strMensajeIp);
                }
            },
            failure: function(result)
            {
                Ext.get(objInfo.formPanel.getId()).unmask();
                Ext.Msg.alert('Error ','Error: ' + result.statusText);
            }
        });
    } else {
        ejecutaCambioPlanClienteMd({...objInfo, strConservarIp: strConservarIp})
    }
}

/*
 * consultarEmpresaCambioPlan
 * @author Jonathan Burgos <jsburgos@telconet.ec> 
 * @version 1.0 - Consulta configuracion de empresa para mostrar motivo cambio de plan.
 */
function consultarEmpresaMotivoCambioPlan(){
    Ext.Ajax.request({
        url: consultarEmpresaCambioPlan,
        method: 'post',
        timeout: 900000,
        success: function(response){
            var objData     = Ext.JSON.decode(response.responseText);
            var strStatus   = objData.status;
            var isVisible   = false;
            if(strStatus == "SI") {
                isVisible = true;
            }
            Ext.getCmp("comboMotivos").setVisible(isVisible);
            Ext.getCmp("comboMotivosCell1").setVisible(isVisible);
            Ext.getCmp("comboMotivosCell2").setVisible(isVisible);
            Ext.getCmp("comboMotivosCell3").setVisible(isVisible);
            Ext.getCmp("comboMotivosCell4").setVisible(isVisible);
            return isVisible;
        },
        failure: function(result)
        {
            Ext.getCmp("comboMotivos").setVisible(false);
            Ext.getCmp("comboMotivosCell1").setVisible(false);
            Ext.getCmp("comboMotivosCell2").setVisible(false);
            Ext.getCmp("comboMotivosCell3").setVisible(false);
            Ext.getCmp("comboMotivosCell4").setVisible(false);
            return false;
        }
    });
}

/*
 * @author Jose Cruz <jfcruzc@telconet.ec> 
 * @version 2.2 - se incluye el motivo para enviarlo en los parametros
 */
function ejecutaCambioPlanClienteMd(objInfo){
    console.log('Inicia ejecucion: ', objInfo.strIppcSolicita);
    Ext.get(objInfo.formPanel.getId()).mask('Ejecutando el cambio de plan del servicio...');
    Ext.Ajax.request({
        url: cambioVelocidad,
        method: 'post',
        timeout: 900000,
        params: { 
            idServicio: objInfo.idServicio,
            planId: objInfo.planId,
            precioViejo: objInfo.precioViejo,
            precioNuevo: objInfo.precioNuevo,
            capacidad1: objInfo.cap1,
            capacidad2: objInfo.cap2,
            capacidad1Actual: objInfo.cap1Actual,
            capacidad2Actual: objInfo.cap2Actual,
            strConservarIp: objInfo.strConservarIp,
            ippcSolicita: objInfo.strIppcSolicita,
            strCodigoMens : objInfo.strCodigoMens,
            idTipoPromoMens: objInfo.idTipoPromoMens,
            strCodigoBw : objInfo.strCodigoBw,
            idTipoPromoBw: objInfo.idTipoPromoBw,
            motivo: objInfo.motivo,
        },
        success: function(response){
            Ext.get(objInfo.formPanel.getId()).unmask();
            var objData     = Ext.JSON.decode(response.responseText);
            var strStatus   = objData.status;
            var strMensaje  = objData.mensaje;
            if(strStatus == "OK") {
                Ext.Msg.alert('Mensaje', strMensaje, function (btn) {
                    if (btn == 'ok') {
                        objInfo.win.destroy();
                        store.load();
                    }
                });
            }else{
                Ext.Msg.alert('Mensaje ', strMensaje);
            }
        },
        failure: function(result)
        {
            Ext.get(objInfo.formPanel.getId()).unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });
}

function verificaProductoNuevoPlan(objInfo){
    console.log('Llego validacion: ', objInfo.planId);
    Ext.Ajax.request({
        url: productoPorPlan,
        method: 'post',
        timeout: 900000,
        params: { 
            planId: objInfo.planId,
        },
        success: function(response){
            Ext.get(objInfo.formPanel.getId()).unmask();
            var objData     = Ext.JSON.decode(response.responseText);
            var strStatus   = objData.status;
            var strIppcSolicita = 'NO';
            if(strStatus == "OK") {
                Ext.Msg.confirm('Alerta ', 'Este plan posee el producto Cableado Ethernet. ¿El cliente desea instalarlo?', function (btn) {
                    if (btn == 'yes') {
                        strIppcSolicita = 'SI';
                        procesaCambioPlanClienteMd({...objInfo, strIppcSolicita: strIppcSolicita});
                    } else {
                        if (btn == 'no') {
                            procesaCambioPlanClienteMd({...objInfo, strIppcSolicita: strIppcSolicita});
                        }
                    }
                });
            } else {
                procesaCambioPlanClienteMd({...objInfo, strIppcSolicita: strIppcSolicita});
            }
        },
        failure: function(result) {
            Ext.get(objInfo.formPanel.getId()).unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    });
}

/* Function convertirTextoEnMayusculas realiza control de espacios en blanco, control del botón ejecutar y
 * convierte el mayúscula las letras ingresada por el Usuario.
 * 
 * @author      José Candelario <jcandelario@telconet.ec>
 * @version     1.0  09-11-2020
 */
function convertirTextoEnMayusculas(idTexto, strValor)
{
    var strTexto      = document.getElementById(idTexto).value;
    var strMayusculas = strTexto.toUpperCase();
    var strTipoPromo   = strValor;
    var strCodigoPromo = Ext.getCmp(''+strTipoPromo).getValue();
    var intCodigoPromo = strCodigoPromo.length;
    if (intCodigoPromo > 0) 
    {
        Ext.getCmp('Ejecutar_MD').setDisabled(true);
    }
    else
    {
        Ext.getCmp('Ejecutar_MD').setDisabled(false);
        strIdTipoPromoMens = "";
        strIdTipoPromoBw   = "";
    }
    document.getElementById(idTexto).value = strMayusculas.trim();
}

/* Function validaCodigo realiza validaciones previas del código ingresado para el servicio, código
 * existente y reglas de promoción.
 * 
 * @author      José Candelario <jcandelario@telconet.ec>
 * @version     1.0  09-11-2020
 */
function validaCodigo(strValor, IdServicio) {
    var strTipoPromo   = strValor;
    var intIdServicio  = IdServicio;
    var intIdPlan      = Ext.getCmp('comboPlanNuevo').getValue();
    var strCodigoPromo = Ext.getCmp(''+strTipoPromo).getValue();
    var intCodigoPromo = strCodigoPromo.length;
    var strGrupoPromo;
    var strTipoProceso;
    var precioViejo;
    var precioNuevo;
    var precioTotal;

    if (intCodigoPromo > 0) 
    {
        if (strTipoPromo === 'PROM_MPLA' || strTipoPromo === 'PROM_MPRO')
        {
            strGrupoPromo = 'PROM_MENS';
        }

        if (strTipoPromo === 'PROM_BW')
        {
            strGrupoPromo = 'PROM_BW';
        }

        if (strGrupoPromo === 'PROM_MENS')
        {
            strTipoProceso  = 'GRADE';
            precioViejo     = Ext.getCmp('total').getValue();
            precioNuevo     = Ext.getCmp('totalNuevo').getValue();

            if(Number(precioNuevo)>0){
                if(Number(precioNuevo) > Number(precioViejo)){
                    precioTotal = precioNuevo;
                }
                else{
                    precioTotal = precioViejo;
                }
            }
            else{
                precioTotal = precioViejo;
            }
        }
        else
        {
            strTipoProceso = 'EXISTENTE';
        }

        var parametros = {
            "strGrupoPromocion"  : strGrupoPromo,
            "strTipoPromocion"   : strTipoPromo,
            "strTipoProceso"     : strTipoProceso,
            "strCodigo"          : strCodigoPromo,
            "intIdServicio"      : intIdServicio,
            "intIdPlan"          : intIdPlan,
            "strPrecioTotal"     : precioTotal
        };
        $.ajax({
            type: "POST",
            data: parametros,
            url: urlValidaCodigoPromocion,
            success: function(msg)
            {
                if (msg.strAplica !== 'S')
                {
                    if (strGrupoPromo === 'PROM_MENS')
                    {
                        document.getElementById('PROM_MPLA-inputEl').value = "";
                        strIdTipoPromoMens = "";
                    }
                    else
                    {
                        document.getElementById('PROM_BW-inputEl').value = "";
                        strIdTipoPromoBw = "";
                    }
                    Ext.Msg.alert("Advertencia",msg.strMensaje);
                    Ext.getCmp('Ejecutar_MD').setDisabled(false);
                }
                else
                {
                    if (strGrupoPromo === 'PROM_MENS')
                    {
                        strIdTipoPromoMens = msg.strIdTipoPromocion;
                    }
                    else
                    {
                        strIdTipoPromoBw = msg.strIdTipoPromocion;
                    }
                    Ext.Msg.alert("Advertencia",msg.strMensaje);
                    Ext.getCmp('Ejecutar_MD').setDisabled(false);
                }
            },
            failure: function()
            {
                if (strGrupoPromo === 'PROM_MENS')
                {
                    document.getElementById('PROM_MPLA-inputEl').value = "";
                    strIdTipoPromoMens = "";
                }
                else
                {
                    document.getElementById('PROM_BW-inputEl').value = "";
                    strIdTipoPromoBw = "";
                }
                Ext.Msg.alert("Advertencia", `<b>Ocurrió un error.</b>`);
                Ext.getCmp('Ejecutar_MD').setDisabled(false);
            }
        });
    }

}