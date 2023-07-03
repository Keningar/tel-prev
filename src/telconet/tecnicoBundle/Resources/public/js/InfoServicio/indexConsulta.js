function verInformactionTecnicaCompleta(data, gridIndex){
    
//    if(data.estado=="EnPruebas" || data.estado=="Asignada"){
//        accion = "pruebas";
//    }
//    else{
//        accion = "consultar";
//    }
    
    Ext.get(gridServicios.getId()).mask('Consultando Info Tecnica...');
    Ext.Ajax.request({
        url: getDatosTecnicos,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio,
            accion: "consultar"
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            
            var json = Ext.JSON.decode(response.responseText);
            var datosTecnicos = json.encontrados;
            
    
            //CARACTERISTICAS IP (PUBLICA, WAN, LAN, MONITOERO
            //-------------------------------------------------------------------------------------------
            Ext.define('tipoCaracteristica', {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'tipo', type: 'string'}
                ]
            });

            var comboCaracteristica = new Ext.data.Store({ 
                model: 'tipoCaracteristica',
                data : [
                    {tipo:'PUBLICA' },
                    {tipo:'MONITOREO' },
                    {tipo:'WAN' },
                    {tipo:'LAN' }
                ]
            });


            var storeIpPublica = new Ext.data.Store({  
                pageSize: 50,
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url : getIpPublicas,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'encontrados'
                    },
                    extraParams: {
                        idServicio: data.idServicio
                    }
                },
                fields:
                    [
                      {name:'ip', mapping:'ip'},
                      {name:'mascara', mapping:'mascara'},
                      {name:'gateway', mapping:'gateway'},
                      {name:'tipo', mapping:'tipo'}
                    ]
            });

            Ext.define('IpPublica', {
                extend: 'Ext.data.Model',
                fields: [
                    {name:'ip', mapping:'ip'},
                      {name:'mascara', mapping:'mascara'},
                      {name:'gateway', mapping:'gateway'},
                      {name:'tipo', mapping:'tipo'}
                ]
            });

            var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToEdit: 2,
                listeners: {
                    edit: function(){
                        // refresh summaries
                        gridIpPublica.getView().refresh();
                    }
                }
            });

            var selIpPublica = Ext.create('Ext.selection.CheckboxModel', {
                listeners: {
                    selectionchange: function(sm, selections) {
                        gridIpPublica.down('#removeButton').setDisabled(selections.length == 0);
                    }
                }
            });

            //grid de usuarios
            gridIpPublica = Ext.create('Ext.grid.Panel', {
                id:'gridIpPublica',
                store: storeIpPublica,
                columnLines: true,
                columns: [{
                    //id: 'nombreDetalle',
                    header: 'Tipo',
                    dataIndex: 'tipo',
                    width: 100,
                    sortable: true
                },{
                    header: 'Ip',
                    dataIndex: 'ip',
                    width: 150
                },
                {
                    header: 'Mascara',
                    dataIndex: 'mascara',
                    width: 150
                },
                {
                    header: 'Gateway',
                    dataIndex: 'gateway',
                    width: 150
                }],
                selModel: selIpPublica,
                viewConfig:{
                    stripeRows:true
                },

//                // inline buttons
//                dockedItems: [{
//                    xtype: 'toolbar',
//                    items: [{
//                        itemId: 'removeButton',
//                        text:'Eliminar',
//                        tooltip:'Elimina el item seleccionado',
//                        iconCls:'remove',
//                        disabled: true,
//                        handler : function(){eliminarSeleccion(gridIpPublica);}
//                    }, '-', {
//                        text:'Agregar',
//                        tooltip:'Agrega un item a la lista',
//                        iconCls:'add',
//                        handler : function(){
//                            // Create a model instance
//                            var r = Ext.create('IpPublica', { 
//                                ip: '',
//                                mascara: '',
//                                gateway: '',
//                                tipo: ''
//
//                            });
//                            if(!existeRecordIpPublica(r, gridIpPublica))
//                            {
//                                storeIpPublica.insert(0, r);
//                                cellEditing.startEditByPosition({row: 0, column: 1});
//                            }
//                            else
//                            {
//                              alert('Ya existe un registro vacio.');
//                            }
//                        }
//                    }]
//                }],
                frame: true,
                height: 200,
                title: 'Caracteristicas del Cliente',
                plugins: [cellEditing]
            });

            //-------------------------------------------------------------------------------------------
            
            var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side',
                    bodyStyle: 'padding:20px'
                },
                layout: {
                    type: 'table',
                    // The total column count must be specified here
                    columns: 2
                },
                defaults: {
                    // applied to each contained panel
                    bodyStyle: 'padding:20px'
                },

                items: [
                    
                    //informacion cpe y dslam
                    {
                        xtype: 'fieldset',
                        title: 'Informacion Tecnica',
                        defaultType: 'textfield',
                        defaults: {
                            width: 550,
                            height: 130
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
                                        //id:'nombreCpe',
                                        name: 'nombreDslam',
                                        fieldLabel: 'Elemento',
                                        displayField: datosTecnicos[0].nombreDslam,
                                        value: datosTecnicos[0].nombreDslam,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        //id:'ipCpe',
                                        name: 'ipDslam',
                                        fieldLabel: 'Ip Elemento',
                                        displayField: datosTecnicos[0].ipDslam,
                                        value: datosTecnicos[0].ipDslam,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        //id:'modeloDslam',
                                        name: 'modeloDslam',
                                        fieldLabel: 'Modelo Elemento',
                                        displayField: datosTecnicos[0].modeloDslam,
                                        value: datosTecnicos[0].modeloDslam,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        //id:'interfaceDslam',
                                        name: 'perfilDslam',
                                        fieldLabel: 'Perfil Elemento',
                                        displayField: datosTecnicos[0].perfilDslam,
                                        value: datosTecnicos[0].perfilDslam,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        //id:'interfaceCpe',
                                        name: 'interfaceDslam',
                                        fieldLabel: 'Interface Elemento',
                                        displayField: datosTecnicos[0].interfaceDslam,
                                        value: datosTecnicos[0].interfaceDslam,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'tipoMedio',
                                        name: 'tipoMedio',
                                        fieldLabel: 'UM',
                                        displayField: data.ultimaMilla,
                                        value: data.ultimaMilla,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false },

                                    //---------------------------------------------
                                    
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        //id:'interfaceCpe',
                                        name: 'vci',
                                        fieldLabel: 'VCI',
                                        displayField: datosTecnicos[0].vci,
                                        value: datosTecnicos[0].vci,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    { width: '30%', border: false},
                                    { width: '10%', border: false },

                                    //---------------------------------------------

                                    
                                ]
                            }

                        ]
                    },//cierre informacion cpe y dslam
                    //
                    //informacion del servicio/producto
                    {
                        xtype: 'fieldset',
                        title: 'Informacion Servicio',
                        defaultType: 'textfield',
                        defaults: {
                            width: 550,
                            height: 130
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
        //                                id:'capacidadUno',
                                        name: 'plan',
                                        fieldLabel: 'Plan',
                                        displayField: data.nombrePlan,
                                        value: data.nombrePlan,
        //                                queryMode: 'local',
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
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
        //                                id:'perfilDslam',
                                        name: 'capacidadTres',
                                        fieldLabel: 'Capacidad Int/Prom Uno',
                                        displayField: data.capacidadTres,
                                        value: data.capacidadTres,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        //id:'numeroPcCliente',
                                        name: 'capacidadCuatro',
                                        fieldLabel: 'Capacidad Int/Prom Dos',
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
                    },//cierre de la informacion servicio/producto
                    
                    //informacion del cliente
                    {
                        xtype: 'fieldset',
                        title: 'Cliente',
                        defaultType: 'textfield',
                        defaults: {
                            width: 550,
                            height: 230
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
        //                                id:'capacidadUno',
                                        name: 'nombreCpe',
                                        fieldLabel: 'Cpe',
                                        displayField: datosTecnicos[0].nombreCpe,
                                        value: datosTecnicos[0].nombreCpe,
        //                                queryMode: 'local',
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
                                        name: 'ipCpe',
                                        fieldLabel: 'Ip Cpe',
                                        displayField: datosTecnicos[0].ipCpe,
                                        value: datosTecnicos[0].ipCpe,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'macCpe',
                                        fieldLabel: 'Mac Cpe',
                                        displayField: datosTecnicos[0].mac,
                                        value: datosTecnicos[0].mac,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'capacidadDos',
                                        name: 'serieCpe',
                                        fieldLabel: 'Serie Cpe',
                                        displayField: datosTecnicos[0].serieCpe,
                                        value: datosTecnicos[0].serieCpe,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------

                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'modeloCpe',
                                        name: 'modeloCpe',
                                        fieldLabel: 'Modelo Cpe',
                                        displayField: datosTecnicos[0].modeloCpe,
                                        value: datosTecnicos[0].modeloCpe,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'numPc',
                                        fieldLabel: 'Numero PC',
                                        displayField: datosTecnicos[0].numPc,
                                        value: datosTecnicos[0].numPc,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------
                                    
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'ssid',
                                        fieldLabel: 'SSID',
                                        displayField: datosTecnicos[0].ssid,
                                        value: datosTecnicos[0].ssid,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        //id:'modeloCpe',
                                        name: 'passSsid',
                                        fieldLabel: 'Password',
                                        displayField: datosTecnicos[0].passSsid,
                                        value: datosTecnicos[0].passSsid,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------
                                    
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
        //                                id:'perfilDslam',
                                        name: 'operacion',
                                        fieldLabel: 'Modo Oper.',
                                        displayField: datosTecnicos[0].operacion,
                                        value: datosTecnicos[0].operacion,
                                        readOnly: true,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    { width: '30%', border: false},
                                    { width: '10%', border: false},

                                    //---------------------------------------------
                                    
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textareafield',
                                        id:'observacionCliente',
                                        name: 'observacionCliente',
                                        fieldLabel: 'Observacion',
                                        displayField: datosTecnicos[0].observacion,
                                        labelPad: -57,
                                        //html: '4,1', 
                                        colspan: 4,
                                        value: datosTecnicos[0].observacion,
                                        readOnly: true,
                                        width: '87%'

                                    }
                                ]
                            }

                        ]
                    },//cierre de la informacion del cliente

                    {
                        xtype: 'fieldset',
                        title: 'Caracteristicas',
                        defaultType: 'textfield',
            //                checkboxToggle: true,
            //                collapsed: true,
                        defaults: {
                            width: 590,
                            height: 230
                        },
                        items: [

                            gridIpPublica

                        ]
                    },//cierre interfaces cpe

                    

                    //Configuracion Dslam
                    {
                        xtype: 'fieldset',
                        title: 'Ver Configuracion Elemento',
                        defaultType: 'textfield',
        //                checkboxToggle: true,
        //                collapsed: true,
                        defaults: {
                            width: 550,
                            height: 125
                        },
                        items: [
                                {
                                    xtype: 'textareafield',
                                    id:'mensaje',
                                    name: 'mensaje',
                                    fieldLabel: 'Configuracion',
                                    value: datosTecnicos[0].mensaje,
                                    cols: 10,
                                    rows: 9,
                                    anchor: '100%',
                                    readOnly:true
                                }

                        ],
                        colspan: 2
                    }//cierre interfaces cpe

                ],//cierre items
                buttons: [{
                    text: 'Cerrar',
                    handler: function(){
                        win.destroy();
                    }
                }]
            });

            var win = Ext.create('Ext.window.Window', {
                title: 'Ver Informacion Tecnica',
                modal: true,
                width: 1225,
                closable: true,
                layout: 'fit',
                items: [formPanel]
            }).show();
        },
        failure: function(result)
        {
            Ext.get(gridServicios.getId()).unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    }); 
    
    
    
    
}

function verParametrosIniciales(data){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getParametrosIniciales,
        method: 'post',
        timeout: 400000,
        params: { 
            modeloElemento: data.modeloElemento,
            interfaceElementoId: data.interfaceElementoId
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            
            var json = Ext.JSON.decode(response.responseText);
            var datos = json.encontrados;
            
            if(data.modeloElemento=="6524"){
                var formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding: 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side'
                    },
                    items: [

                        //config 1
                        {
                            xtype: 'fieldset',
                            title: 'Atenuacion',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500
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
                                        //---------------------------------------------

                                        
                                        {
                                            xtype: 'textareafield',
                                            displayField: datos[0].valor,
                                            value: datos[0].valor,
                                            readOnly: true,
                                            cols: 120,
                                            rows: 8,
                                            anchor: '100%'
                                        }

                                        //---------------------------------------------
                                    ]
                                }

                            ]
                        },//cierre de la config 1

                        //config 2
                        {
                            xtype: 'fieldset',
                            title: 'Señal Ruido',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500
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
                                        //---------------------------------------------

                                        
                                        {
                                            xtype: 'textareafield',
                                            displayField: datos[1].valor,
                                            value: datos[1].valor,
                                            readOnly: true,
                                            cols: 120,
                                            rows: 8,
                                            anchor: '100%'
                                        }

                                        //---------------------------------------------
                                    ]
                                }

                            ]
                        },//cierre de la config 2
                        
                        //config 3
                        {
                            xtype: 'fieldset',
                            title: 'CRC',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500
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
                                        //---------------------------------------------

                                        
                                        {
                                            xtype: 'textareafield',
                                            displayField: datos[2].valor,
                                            value: datos[2].valor,
                                            readOnly: true,
                                            cols: 120,
                                            rows: 8,
                                            anchor: '100%'
                                        }

                                        //---------------------------------------------
                                    ]
                                }

                            ]
                        },//cierre de la config 3
                    ],
                    buttons: [{
                        text: 'Cerrar',
                        handler: function(){
                            win.destroy();
                        }
                    }]
                });
            }
            else if(data.modeloElemento=="7224"){
                var formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding: 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side'
                    },
                    items: [

                        //config 1
                        {
                            xtype: 'fieldset',
                            title: 'Monitorear Puerto',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500
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
                                        //---------------------------------------------

                                        
                                        {
                                            xtype: 'textareafield',
                                            displayField: datos[0].valor,
                                            value: datos[0].valor,
                                            readOnly: true,
                                            cols: 120,
                                            rows: 8,
                                            anchor: '100%'
                                        }

                                        //---------------------------------------------
                                    ]
                                }

                            ]
                        },//cierre de la config 1

                        //config 2
                        {
                            xtype: 'fieldset',
                            title: 'Parametros Linea',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500
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
                                        //---------------------------------------------

                                        
                                        {
                                            xtype: 'textareafield',
                                            displayField: datos[1].valor,
                                            value: datos[1].valor,
                                            readOnly: true,
                                            cols: 120,
                                            rows: 8,
                                            anchor: '100%'
                                        }

                                        //---------------------------------------------
                                    ]
                                }

                            ]
                        },//cierre de la config 2
                    ],
                    buttons: [{
                        text: 'Cerrar',
                        handler: function(){
                            win.destroy();
                        }
                    }]
                });
            }
            else if(data.modeloElemento=="R1AD24A" || data.modeloElemento=="R1AD48A"){
                var formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding: 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side'
                    },
                    items: [

                        //config 1
                        {
                            xtype: 'fieldset',
                            title: 'Monitorear Puerto I',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500
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
                                        //---------------------------------------------

                                        
                                        {
                                            xtype: 'textareafield',
                                            //grow: true,
                                            displayField: datos[0].valor,
                                            value: datos[0].valor,
                                            readOnly: true,
                                            cols: 120,
                                            rows: 8,
                                            anchor: '100%'
                                        }

                                        //---------------------------------------------
                                    ]
                                }

                            ]
                        },//cierre de la config 1

                        //config 2
                        {
                            xtype: 'fieldset',
                            title: 'Monitorear Puerto II',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500
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
                                        //---------------------------------------------

                                        
                                        {
                                            xtype: 'textareafield',
                                            //grow: true,
                                            displayField: datos[1].valor,
                                            value: datos[1].valor,
                                            readOnly: true,
                                            cols: 120,
                                            rows: 8,
                                            anchor: '100%'
                                        }

                                        //---------------------------------------------
                                    ]
                                }

                            ]
                        },//cierre de la config 2
                    ],
                    buttons: [{
                        text: 'Cerrar',
                        handler: function(){
                            win.destroy();
                        }
                    }]
                });
            }
            else if(data.modeloElemento=="A2048" || data.modeloElemento=="A2024"){
                var formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding: 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side'
                    },
                    items: [

                        //config 1
                        {
                            xtype: 'fieldset',
                            title: 'Configuracion Interface',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500
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
                                        //---------------------------------------------

                                        
                                        {
                                            xtype: 'textareafield',
                                            displayField: datos[0].valor,
                                            value: datos[0].valor,
                                            readOnly: true,
                                            cols: 120,
                                            rows: 8,
                                            anchor: '100%'
                                        }

                                        //---------------------------------------------
                                    ]
                                }

                            ]
                        },//cierre de la config 1

                        //config 2
                        {
                            xtype: 'fieldset',
                            title: 'Velocidad Real',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500
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
                                        //---------------------------------------------

                                        
                                        {
                                            xtype: 'textareafield',
                                            displayField: datos[1].valor,
                                            value: datos[1].valor,
                                            readOnly: true,
                                            cols: 120,
                                            rows: 8,
                                            anchor: '100%'
                                        }

                                        //---------------------------------------------
                                    ]
                                }

                            ]
                        },//cierre de la config 2
                        
                        //config 3
                        {
                            xtype: 'fieldset',
                            title: 'Señales Nivel Extremo Lejano',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500
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
                                        //---------------------------------------------

                                        
                                        {
                                            xtype: 'textareafield',
                                            displayField: datos[2].valor,
                                            value: datos[2].valor,
                                            readOnly: true,
                                            cols: 120,
                                            rows: 8,
                                            anchor: '100%'
                                        }

                                        //---------------------------------------------
                                    ]
                                }

                            ]
                        },//cierre de la config 3
                        
                        //config 4
                        {
                            xtype: 'fieldset',
                            title: 'Señales Nivel Extremo Cercano',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500
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
                                        //---------------------------------------------

                                        
                                        {
                                            xtype: 'textareafield',
                                            displayField: datos[3].valor,
                                            value: datos[3].valor,
                                            readOnly: true,
                                            cols: 120,
                                            rows: 8,
                                            anchor: '100%'
                                        }

                                        //---------------------------------------------
                                    ]
                                }

                            ]
                        },//cierre de la config 4
                        
                        //config 5
                        {
                            xtype: 'fieldset',
                            title: 'Desempeño Puerto Intervalo',
                            defaultType: 'textfield',
                            defaults: {
                                width: 500
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
                                        //---------------------------------------------

                                        
                                        {
                                            xtype: 'textareafield',
                                            displayField: datos[4].valor,
                                            value: datos[4].valor,
                                            readOnly: true,
                                            cols: 120,
                                            rows: 8,
                                            anchor: '100%'
                                        }

                                        //---------------------------------------------
                                    ]
                                }

                            ]
                        },//cierre de la config 5
                    ],
                    buttons: [{
                        text: 'Cerrar',
                        handler: function(){
                            win.destroy();
                        }
                    }]
                });
            }
            
            

        var win = Ext.create('Ext.window.Window', {
            title: 'Parametros Iniciales',
            modal: true,
            width: 580,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
            
        }//cierre response
    });       
}

function verParametrosInicialesMd(data){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getParametrosInicialesMd,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio,
            idProducto: data.productoId
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            
            var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
                items: [

                    //config 1
                    {
                        xtype: 'fieldset',
                        title: 'Potencia',
                        defaultType: 'textfield',
                        defaults: {
                            width: 500
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
                                    //---------------------------------------------
                                    {
                                        xtype: 'textareafield',
                                        displayField: response.responseText,
                                        value: response.responseText,
                                        readOnly: true,
                                        cols: 120,
                                        rows: 8,
                                        anchor: '100%'
                                    }
                                    //---------------------------------------------
                                ]
                            }

                        ]
                    },//cierre de la config 1
                ],
                buttons: [{
                    text: 'Cerrar',
                    handler: function(){
                        win.destroy();
                    }
                }]
            });
            
                       

        var win = Ext.create('Ext.window.Window', {
            title: 'Parametros Iniciales',
            modal: true,
            width: 580,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
            
        }//cierre response
    });    
}


function reconfigurarLdapServicio(data)
{
    Ext.Msg.alert('Mensaje','Esta seguro que desea Reconfigurar el Ldap?', function(btn)
    {
        if(btn=='ok')
        {
            Ext.get("grid").mask('Reconfigurando ldap del cliente...');
            Ext.Ajax.request
            ({
                url: ConfigurarLdapServicio,
                method: 'post',
                timeout: 400000,
                params: 
                { 
                    idServicio: data.idServicio
                },
                success: function(response)
                {
                    Ext.get("grid").unmask();
                    
                    Ext.Msg.alert('Mensaje ',response.responseText );
                }
            });
        }
    });
}

    function eliminarLdapServicio(data)
    {
        Ext.Msg.alert('Mensaje','Esta seguro que desea eliminar el Ldap del cliente?', function(btn)
        {
            if(btn=='ok')
            {
                Ext.get("grid").mask('Eliminando ldap del cliente...');
                Ext.Ajax.request
                ({
                    url: EliminarLdapServicio,
                    method: 'post',
                    timeout: 400000,
                    params: 
                    { 
                        idServicio: data.idServicio
                    },
                    success: function(response)
                    {
                        Ext.get("grid").unmask();

                        Ext.Msg.alert('Mensaje ',response.responseText );
                    }
                });
            }
        });
    }

function crearLdapServicio(data)
{
    Ext.Msg.alert('Mensaje','Esta seguro que desea crearlo en el Ldap?', function(btn)
    {
        if(btn=='ok')
        {
            Ext.get("grid").mask('Creando cliente en el ldap...');
            Ext.Ajax.request
            ({
                url: CrearLdapServicio,
                method: 'post',
                timeout: 400000,
                params: 
                { 
                    idServicio: data.idServicio
                },
                success: function(response)
                {
                    Ext.get("grid").unmask();
                    
                    Ext.Msg.alert('Mensaje ',response.responseText );
                }
            });
        }
    });
}



function verLdapServicio(data){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: VerLdapServicio,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();
            
            var formPanel = Ext.create('Ext.form.Panel', {
                bodyPadding: 2,
                waitMsgTarget: true,
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 85,
                    msgTarget: 'side'
                },
                items: [

                    //config 1
                    {
                        xtype: 'fieldset',
                        title: 'Datos',
                        defaultType: 'textfield',
                        defaults: {
                            width: 500
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
                                    //---------------------------------------------
                                    {
                                        xtype: 'textareafield',
                                        displayField: response.responseText,
                                        value: response.responseText,
                                        readOnly: true,
                                        cols: 120,
                                        rows: 8,
                                        anchor: '100%'
                                    }
                                    //---------------------------------------------
                                ]
                            }

                        ]
                    },//cierre de la config 1
                ],
                buttons: [{
                    text: 'Cerrar',
                    handler: function(){
                        win.destroy();
                    }
                }]
            });
            
                       

        var win = Ext.create('Ext.window.Window', {
            title: 'Datos en el Ldap',
            modal: true,
            width: 580,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
            
        }//cierre response
    });    
}

/**
 * consultarHorasSoporte
 *
 * Función encargada de mostrar una nueva página enviando los datos para la consulta de las horas de soporte
 * 
 *
 * @author Jonathan Quintana <jiquintana@telconet.ec>
 * @version 1.0 21-11-2022
 *
 *
 */
function consultarHorasSoporte(data)
{
    
    var idServicio = data.idServicio;
    var idPersonaEmpresaRol = data.idPersonaEmpresaRol;

    const uri = `/soporte/gestionPaqueteSoporte/${idPersonaEmpresaRol}/consultarPaqueteSoporte?`
                                                                +`idServicio=${idServicio}`;

    window.location.href = uri;
}

function verLogsServicio(data){
    var storeHistorial = new Ext.data.Store({  
        pageSize: 50000,
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url : getHistorialServicio,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                idServicio: data.idServicio
            }
        },
        fields:
            [
              {name:'usrCreacion', mapping:'usrCreacion'},
              {name:'feCreacion', mapping:'feCreacion'},
              {name:'ipCreacion', mapping:'ipCreacion'},
              {name:'estado', mapping:'estado'},
              {name:'nombreMotivo', mapping:'nombreMotivo'},
              {name:'observacion', mapping:'observacion'},
              {name:'accion', mapping:'accion'}
            ]
    });
    
    Ext.define('HistorialServicio', {
        extend: 'Ext.data.Model',
        fields: [
              {name:'usrCreacion', mapping:'usrCreacion'},
              {name:'feCreacion', mapping:'feCreacion'},
              {name:'ipCreacion', mapping:'ipCreacion'},
              {name:'estado', mapping:'estado'},
              {name:'nombreMotivo', mapping:'nombreMotivo'},
              {name:'observacion', mapping:'observacion'},
              {name:'accion', mapping:'accion'}
        ]
    });
    
    //grid de usuarios
    gridHistorialServicio = Ext.create('Ext.grid.Panel', {
        id:'gridHistorialServicio',
        store: storeHistorial,
        columnLines: true,
        listeners: 
        {
            viewready: function (grid)
            {
                var view = grid.view;

                grid.mon(view,
                {
                    uievent: function (type, view, cell, recordIndex, cellIndex, e)
                    {
                        grid.cellIndex   = cellIndex;
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
                        beforeshow: function(tip)
                        {
                            if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1)
                            {
                                header = grid.headerCt.getGridColumns()[grid.cellIndex];
                                
                                if( header.dataIndex != null )
                                {
                                    var trigger         = tip.triggerElement,
                                        parent          = tip.triggerElement.parentElement,
                                        columnTitle     = view.getHeaderByCell(trigger).text,
                                        columnDataIndex = view.getHeaderByCell(trigger).dataIndex;

                                    if( view.getRecord(parent).get(columnDataIndex) != null )
                                    {
                                        var columnText      = view.getRecord(parent).get(columnDataIndex).toString();
                                        
                                        if (columnText)
                                        {
                                            tip.update(columnText);
                                        }
                                        else
                                        {
                                            return false;
                                        }
                                    }
                                    else
                                    {
                                        return false;
                                    }
                                }
                                else
                                {
                                    return false;
                                }
                            }     
                        }
                    }
                });
                
                grid.tip.on('show', function()
                {
                    var timeout;

                    grid.tip.getEl().on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });

                    grid.tip.getEl().on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseover', function(){window.clearTimeout(timeout);});

                    Ext.get(view.el).on('mouseout', function()
                    {
                        timeout = window.setTimeout(function(){grid.tip.hide();}, 500);
                    });
                });
            }
        },
        columns: 
        [
            {
                //id: 'nombreDetalle',
                header: 'Usuario Creacion',
                dataIndex: 'usrCreacion',
                width: 100,
                sortable: true
            },
            {
                header: 'Fecha Creacion',
                dataIndex: 'feCreacion',
                width: 120
            },
            {
                header: 'Ip Creacion',
                dataIndex: 'ipCreacion',
                width: 100
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 100
            },
            {
                header: 'Motivo',
                dataIndex: 'nombreMotivo',
                width: 130
            },
            {
                header: 'Accion',
                dataIndex: 'accion',
                width: 100
            },
            {
                header: 'Observacion',
                dataIndex: 'observacion',
                width: 438
            }
        ],
        viewConfig:
        {
            stripeRows:true,
            enableTextSelection: true
        },
        frame: true,
        height: 300
        //title: 'Historial del Servicio'
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
            xtype: 'fieldset',
            title: '',
            defaultType: 'textfield',
//                checkboxToggle: true,
//                collapsed: true,
            defaults: {
                width: 1100
            },
            items: [

                gridHistorialServicio

            ]
        }//cierre interfaces cpe
        ],
        buttons: [{
            text: 'Cerrar',
            handler: function(){
                win.destroy();
            }
        }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Historial del Servicio',
        modal: true,
        width: 1150,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function verSubscribers(data, action){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: scriptsOlt+action,
        method: 'post',
        waitMsg: 'Esperando Respuesta del Elemento',
        timeout: 400000,
        params: { modelo: data.modeloElemento,
                  idElemento: data.elementoId
                },
        success: function(response){
                Ext.get(gridServicios.getId()).unmask();
                var variable = response.responseText.split("&");
                var resp = variable[0];
                var script = variable[1];

                if(script=="NO EXISTE RELACION TAREA - ACCION"){
                    Ext.Msg.alert('Error ','No Existe la Relacion Tarea - Accion');
                    Ext.get(gridServicios.getId()).unmask();
                }
                else{
                    var ejecucion = Ext.JSON.decode(resp);
                    Ext.get(gridServicios.getId()).unmask();
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
                                xtype: 'fieldset',
                                title: 'Script',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 550,
                                    height: 70
                                },
                                items: [

                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'hbox',
                                            pack: 'left'
                                        },
                                        items: [{
                                            xtype: 'textareafield',
                                            id:'script',
                                            name: 'script',
                                            fieldLabel: 'Script',
                                            value: script,
                                            cols: 75,
                                            rows: 3,
                                            anchor: '100%',
                                            readOnly:true
                                        }]
                                    },

                                ]
                            },
        //                    {
        //                        xtype: 'component',
        //                        html: 'Comando: '+json.encontrados[0].script
        //                    }
                            ,{
                            xtype: 'fieldset',
                            title: 'Configuracion',
                            defaultType: 'textfield',
                            defaults: {
                                width: 550,
                                height: 325
                            },
                            items: [

                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'hbox',
                                        pack: 'left'
                                    },
                                    items: [{
                                        xtype: 'textareafield',
                                        id:'mensaje',
                                        name: 'mensaje',
                                        fieldLabel: 'Configuracion',
                                        value: ejecucion.mensaje,
                                        cols: 75,
                                        rows: 19,
                                        anchor: '100%',
                                        readOnly:true
                                    }]
                                },

                            ]
                        }],
                        buttons: [{
                            text: 'Cerrar',
                            formBind: true,
                            handler: function(){
                                win.destroy();
                            }
                        }]
                    });

                    var win = Ext.create('Ext.window.Window', {
                        title: 'Ver Configuracion',
                        modal: true,
                        width: 630,
                        height: 550,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();

                }//cierre else

        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    }); 
}

function verificarServicios(data, action){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: scriptsOlt+action,
        method: 'post',
        waitMsg: 'Esperando Respuesta del Elemento',
        timeout: 400000,
        params: { modelo: data.modeloElemento,
                  idElemento: data.elementoId,
                  interfaceElemento: data.interfaceElementoNombre,
                  idServicio: data.idServicio
                },
        success: function(response){
                Ext.get(gridServicios.getId()).unmask();
                var variable = response.responseText.split("&");
                var resp = variable[0];
                var script = variable[1];

                if(script=="NO EXISTE RELACION TAREA - ACCION"){
                    Ext.Msg.alert('Error ','No Existe la Relacion Tarea - Accion');
                    Ext.get(gridServicios.getId()).unmask();
                }
                else{
                    var ejecucion = Ext.JSON.decode(resp);
                    Ext.get(gridServicios.getId()).unmask();
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
                                xtype: 'fieldset',
                                title: 'Script',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 550,
                                    height: 70
                                },
                                items: [

                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'hbox',
                                            pack: 'left'
                                        },
                                        items: [{
                                            xtype: 'textareafield',
                                            id:'script',
                                            name: 'script',
                                            fieldLabel: 'Script',
                                            value: script,
                                            cols: 75,
                                            rows: 3,
                                            anchor: '100%',
                                            readOnly:true
                                        }]
                                    },

                                ]
                            },
        //                    {
        //                        xtype: 'component',
        //                        html: 'Comando: '+json.encontrados[0].script
        //                    }
                            ,{
                            xtype: 'fieldset',
                            title: 'Configuracion',
                            defaultType: 'textfield',
                            defaults: {
                                width: 550,
                                height: 325
                            },
                            items: [

                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'hbox',
                                        pack: 'left'
                                    },
                                    items: [{
                                        xtype: 'textareafield',
                                        id:'mensaje',
                                        name: 'mensaje',
                                        fieldLabel: 'Configuracion',
                                        value: ejecucion.mensaje,
                                        cols: 75,
                                        rows: 19,
                                        anchor: '100%',
                                        readOnly:true
                                    }]
                                },

                            ]
                        }],
                        buttons: [{
                            text: 'Cerrar',
                            formBind: true,
                            handler: function(){
                                win.destroy();
                            }
                        }]
                    });

                    var win = Ext.create('Ext.window.Window', {
                        title: 'Ver Configuracion',
                        modal: true,
                        width: 630,
                        height: 550,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();

                }//cierre else

        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    }); 
}

function verMacsConectadas(data, action){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: scriptsOlt+action,
        method: 'post',
        waitMsg: 'Esperando Respuesta del Elemento',
        timeout: 400000,
        params: { modelo: data.modeloElemento,
                  idElemento: data.elementoId,
                  interfaceElemento: data.interfaceElementoNombre,
                  idServicio: data.idServicio
                },
        success: function(response){
                Ext.get(gridServicios.getId()).unmask();
                var variable = response.responseText.split("&");
                var resp = variable[0];
                var script = variable[1];

                if(script=="NO EXISTE RELACION TAREA - ACCION"){
                    Ext.Msg.alert('Error ','No Existe la Relacion Tarea - Accion');
                    Ext.get(gridServicios.getId()).unmask();
                }
                else{
                    var ejecucion = Ext.JSON.decode(resp);
                    Ext.get(gridServicios.getId()).unmask();
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
                                xtype: 'fieldset',
                                title: 'Script',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 550,
                                    height: 70
                                },
                                items: [

                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'hbox',
                                            pack: 'left'
                                        },
                                        items: [{
                                            xtype: 'textareafield',
                                            id:'script',
                                            name: 'script',
                                            fieldLabel: 'Script',
                                            value: script,
                                            cols: 75,
                                            rows: 3,
                                            anchor: '100%',
                                            readOnly:true
                                        }]
                                    },

                                ]
                            },
        //                    {
        //                        xtype: 'component',
        //                        html: 'Comando: '+json.encontrados[0].script
        //                    }
                            ,{
                            xtype: 'fieldset',
                            title: 'Configuracion',
                            defaultType: 'textfield',
                            defaults: {
                                width: 550,
                                height: 325
                            },
                            items: [

                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'hbox',
                                        pack: 'left'
                                    },
                                    items: [{
                                        xtype: 'textareafield',
                                        id:'mensaje',
                                        name: 'mensaje',
                                        fieldLabel: 'Configuracion',
                                        value: ejecucion.mensaje,
                                        cols: 75,
                                        rows: 19,
                                        anchor: '100%',
                                        readOnly:true
                                    }]
                                },

                            ]
                        }],
                        buttons: [{
                            text: 'Cerrar',
                            formBind: true,
                            handler: function(){
                                win.destroy();
                            }
                        }]
                    });

                    var win = Ext.create('Ext.window.Window', {
                        title: 'Ver Configuracion',
                        modal: true,
                        width: 630,
                        height: 550,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();

                }//cierre else

        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    }); 
}

/*
 * Funcion que llama a un requerimiento
 * ajax para ejecutar un script en el olt
 */
function verScriptVariableOlt(data, action){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: scriptsOlt+action,
        method: 'post',
        waitMsg: 'Esperando Respuesta del Elemento',
        timeout: 400000,
        params: { modelo: data.modeloElemento,
                  idElemento: data.elementoId,
                  interfaceElemento: data.interfaceElementoNombre,
                  idServicio: data.idServicio
                },
        success: function(response){
                Ext.get(gridServicios.getId()).unmask();
                var variable = response.responseText.split("&");
                var resp = variable[0];
                var script = variable[1];

                if(script=="NO EXISTE RELACION TAREA - ACCION"){
                    Ext.Msg.alert('Error ','No Existe la Relacion Tarea - Accion');
                    Ext.get(gridServicios.getId()).unmask();
                }
                else{
                    var ejecucion = Ext.JSON.decode(resp);
                    Ext.get(gridServicios.getId()).unmask();
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
                                xtype: 'fieldset',
                                title: 'Script',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 550,
                                    height: 70
                                },
                                items: 
                                [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'hbox',
                                            pack: 'left'
                                        },
                                        items: [{
                                            xtype: 'textareafield',
                                            id:'script',
                                            name: 'script',
                                            fieldLabel: 'Script',
                                            value: script,
                                            cols: 75,
                                            rows: 3,
                                            anchor: '100%',
                                            readOnly:true
                                        }]
                                    }
                                ]
                            },
                            {
                                xtype: 'fieldset',
                                title: 'Configuracion',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 550,
                                    height: 325
                                },
                                items: 
                                [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'hbox',
                                            pack: 'left'
                                        },
                                        items: [{
                                            xtype: 'textareafield',
                                            id:'mensaje',
                                            name: 'mensaje',
                                            fieldLabel: 'Configuracion',
                                            value: ejecucion.mensaje,
                                            cols: 75,
                                            rows: 19,
                                            anchor: '100%',
                                            readOnly:true
                                        }]
                                    }
                                ]
                        }],
                        buttons: [{
                            text: 'Cerrar',
                            formBind: true,
                            handler: function(){
                                win.destroy();
                            }
                        }]
                    });

                    var win = Ext.create('Ext.window.Window', {
                        title: 'Ver Configuracion',
                        modal: true,
                        width: 630,
                        height: 550,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();
                }//cierre else
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    }); 
}

function verSubscribersConectados(data, action){
    Ext.get(gridServicios.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: scriptsOlt+action,
        method: 'post',
        waitMsg: 'Esperando Respuesta del Elemento',
        timeout: 400000,
        params: { modelo: data.modeloElemento,
                  idElemento: data.elementoId,
                  interfaceElemento: data.interfaceElementoNombre,
                  idServicio: data.idServicio
                },
        success: function(response){
                Ext.get(gridServicios.getId()).unmask();
                var variable = response.responseText.split("&");
                var resp = variable[0];
                var script = variable[1];

                if(script=="NO EXISTE RELACION TAREA - ACCION"){
                    Ext.Msg.alert('Error ','No Existe la Relacion Tarea - Accion');
                    Ext.get(gridServicios.getId()).unmask();
                }
                else{
                    var ejecucion = Ext.JSON.decode(resp);
                    Ext.get(gridServicios.getId()).unmask();
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
                                xtype: 'fieldset',
                                title: 'Script',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 550,
                                    height: 70
                                },
                                items: [

                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'hbox',
                                            pack: 'left'
                                        },
                                        items: [{
                                            xtype: 'textareafield',
                                            id:'script',
                                            name: 'script',
                                            fieldLabel: 'Script',
                                            value: script,
                                            cols: 75,
                                            rows: 3,
                                            anchor: '100%',
                                            readOnly:true
                                        }]
                                    },

                                ]
                            },
        //                    {
        //                        xtype: 'component',
        //                        html: 'Comando: '+json.encontrados[0].script
        //                    }
                            ,{
                            xtype: 'fieldset',
                            title: 'Configuracion',
                            defaultType: 'textfield',
                            defaults: {
                                width: 550,
                                height: 325
                            },
                            items: [

                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'hbox',
                                        pack: 'left'
                                    },
                                    items: [{
                                        xtype: 'textareafield',
                                        id:'mensaje',
                                        name: 'mensaje',
                                        fieldLabel: 'Configuracion',
                                        value: ejecucion.mensaje,
                                        cols: 75,
                                        rows: 19,
                                        anchor: '100%',
                                        readOnly:true
                                    }]
                                },

                            ]
                        }],
                        buttons: [{
                            text: 'Cerrar',
                            formBind: true,
                            handler: function(){
                                win.destroy();
                            }
                        }]
                    });

                    var win = Ext.create('Ext.window.Window', {
                        title: 'Ver Configuracion',
                        modal: true,
                        width: 630,
                        height: 550,
                        closable: true,
                        layout: 'fit',
                        items: [formPanel]
                    }).show();

                }//cierre else

        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ','Error: ' + result.statusText);
        }
    }); 
}

//ejecuta los scripts sin variable para los dslams
function verScriptVariableDslam(data, action){
    Ext.get(gridServicios.getId()).mask('Loading...');
    
    var str = -1 ;
    str = action.search("cambiarCodificacionPuerto");
    
    if(str!=-1){
        //ENTRA A BUSCAR LA CODIFICACION
        Ext.define('codificacion', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'opcion', type: 'string'},
                {name: 'valor',  type: 'string'}
            ]
        });
        
        var mod1 =0;
        var mod2 =0;
        var mod3 =0;
        
        mod1 = action.search("6524");
        mod2 = action.search("7224");
        mod3 = action.search("R1");
        
        
        if(mod1!=0){ //6524
            comboCodificacion = new Ext.data.Store({ 
                model: 'codificacion',
                data : [
                    {opcion:'G.DMT ONLY MODE', valor:'0'},
                    {opcion:'G.LITE ONLY MODE'   , valor:'2'},
                    {opcion:'T1.413 ONLY MODE'   , valor:'1'},
                    {opcion:'AUTO SENSING MODE'   , valor:'3'}
                ]
            });
        }
        else if(mod2!=0){ //7224
            comboCodificacion = new Ext.data.Store({ 
                model: 'codificacion',
                data : [
                    {opcion:'ADSL2', valor:'adsl2'},
                    {opcion:'ADSL2+'   , valor:'adsl2+'},
                    {opcion:'DMT'   , valor:'dmt'},
                    {opcion:'MULTIMODE'   , valor:'multimode'}
                ]
            });
        }
        else if(mod3!=0){ //R1
            comboCodificacion = new Ext.data.Store({ 
                model: 'codificacion',
                data : [
                    {opcion:'ADSL.BIS', valor:'adsl.bis'},
                    {opcion:'ADSL.BIS.PLUS'  , valor:'adsl.bis.plus'},
                    {opcion:'G.DMT', valor:'g.dmt'},
                    {opcion:'AUTO'   , valor:'auto'}
                ]
            });
        }
        
        var formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 85,
                msgTarget: 'side'
            },
            items: [{
                xtype: 'fieldset',
                title: 'Codificacion',
                defaultType: 'textfield',
                defaults: {
                    width: 650
                },
                items: [

                    {
                        xtype: 'container',
                        layout: {
                            type: 'hbox',
                            pack: 'left'
                        },
                        items: [{
                            xtype: 'combo',
                            id:'comboCodificacion',
                            name: 'comboCodificacion',
                            store: comboCodificacion,
                            fieldLabel: 'Codificacion',
                            displayField: 'opcion',
                            valueField: 'valor',
                            queryMode: 'local'
                        }]
                    }

                ]
            }],
            buttons: [{
                text: 'Ejecutar',
                formBind: true,
                handler: function(){
                    if(true){
                        
                        Ext.Ajax.request({
                            url: scriptsDslam+action,
                            method: 'post',
                            waitMsg: 'Esperando Respuesta del Dslam',
                            timeout: 400000,
                            params: { modelo: data.modeloElemento,
                                      idElemento: data.elementoId,
                                      interfaceElemento:data.interfaceElementoNombre,
                                      codificacion: Ext.getCmp('comboCodificacion').value
                                    },
                            success: function(response){
                                    //alert("hola");
                                    var variable = response.responseText.split("&");
                                    var resp = variable[0];
                                    var script = variable[1];

                                    //alert(resp);

                                    if(script=="NO EXISTE RELACION TAREA - ACCION"){
                                        Ext.Msg.alert('Error ','No Existe la Relacion Tarea - Accion');
                                        Ext.get(gridServicios.getId()).unmask();
                                    }
                                    else if(response.responseText.indexOf("El host no es alcanzable a nivel de red")!=-1){
                                        Ext.Msg.alert('Error ','No se puede Conectar al Dslam <br>Favor revisar con el Departamento Tecnico');
                                        Ext.get(gridServicios.getId()).unmask();
                                    }
                                    else{
                                        var ejecucion = Ext.JSON.decode(resp);
                                        Ext.get(gridServicios.getId()).unmask();

                                        var formPanel1 = Ext.create('Ext.form.Panel', {
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
                                                    title: 'Script',
                                                    defaultType: 'textfield',
                                                    defaults: {
                                                        width: 550,
                                                        height: 70
                                                    },
                                                    items: [

                                                        {
                                                            xtype: 'container',
                                                            layout: {
                                                                type: 'hbox',
                                                                pack: 'left'
                                                            },
                                                            items: [{
                                                                xtype: 'textareafield',
                                                                id:'script',
                                                                name: 'script',
                                                                fieldLabel: 'Script',
                                                                value: script,
                                                                cols: 75,
                                                                rows: 3,
                                                                anchor: '100%',
                                                                readOnly:true
                                                            }]
                                                        },

                                                    ]
                                                },
                            //                    {
                            //                        xtype: 'component',
                            //                        html: 'Comando: '+json.encontrados[0].script
                            //                    }
                                                ,{
                                                xtype: 'fieldset',
                                                title: 'Configuracion',
                                                defaultType: 'textfield',
                                                defaults: {
                                                    width: 550,
                                                    height: 325
                                                },
                                                items: [

                                                    {
                                                        xtype: 'container',
                                                        layout: {
                                                            type: 'hbox',
                                                            pack: 'left'
                                                        },
                                                        items: [{
                                                            xtype: 'textareafield',
                                                            id:'mensaje',
                                                            name: 'mensaje',
                                                            fieldLabel: 'Configuracion',
                                                            value: ejecucion.mensaje,
                                                            cols: 75,
                                                            rows: 19,
                                                            anchor: '100%',
                                                            readOnly:true
                                                        }]
                                                    },

                                                ]
                                            }],
                                            buttons: [{
                                                text: 'Cerrar',
                                                formBind: true,
                                                handler: function(){
                                                    win.destroy();
                                                }
                                            }]
                                        });

                                        var win = Ext.create('Ext.window.Window', {
                                            title: 'Ver Configuracion Interface',
                                            modal: true,
                                            width: 630,
                                            height: 550,
                                            closable: false,
                                            layout: 'fit',
                                            items: [formPanel1]
                                        }).show();
                                    }//cierre else
                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                            }
                        });
                        win.destroy();
                    }
                    else{
                        Ext.Msg.alert("Failed","Favor Revise los campos", function(btn){
                                if(btn=='ok'){
                                }
                        });
                    }

                }
            },{
                text: 'Cancelar',
                handler: function(){
                    Ext.get(gridServicios.getId()).unmask();
                    win.destroy();
                }
            }]
        });
        
        var win = Ext.create('Ext.window.Window', {
            title: 'Ver Configuracion Interface',
            modal: true,
            width: 300,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();
        
    }
    else{
        //SIN CODIFICACION
        Ext.Ajax.request({
            url: scriptsDslam+action,
            method: 'post',
            waitMsg: 'Esperando Respuesta del Dslam',
            timeout: 400000,
            params: { modelo: data.modeloElemento,
                      idElemento: data.elementoId,
                      interfaceElemento: data.interfaceElementoNombre
                    },
            success: function(response){

                    var variable = response.responseText.split("&");
                    var resp = variable[0];
                    var script = variable[1];

                    if(script=="NO EXISTE RELACION TAREA - ACCION"){
                        Ext.Msg.alert('Error ','No Existe la Relacion Tarea - Accion');
                        Ext.get(gridServicios.getId()).unmask();
                    }
                    else{
                        var ejecucion = Ext.JSON.decode(resp);
                        Ext.get(gridServicios.getId()).unmask();
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
                                    xtype: 'fieldset',
                                    title: 'Script',
                                    defaultType: 'textfield',
                                    defaults: {
                                        width: 550,
                                        height: 70
                                    },
                                    items: [

                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'hbox',
                                                pack: 'left'
                                            },
                                            items: [{
                                                xtype: 'textareafield',
                                                id:'script',
                                                name: 'script',
                                                fieldLabel: 'Script',
                                                value: script,
                                                cols: 75,
                                                rows: 3,
                                                anchor: '100%',
                                                readOnly:true
                                            }]
                                        },

                                    ]
                                },
            //                    {
            //                        xtype: 'component',
            //                        html: 'Comando: '+json.encontrados[0].script
            //                    }
                                ,{
                                xtype: 'fieldset',
                                title: 'Configuracion',
                                defaultType: 'textfield',
                                defaults: {
                                    width: 550,
                                    height: 325
                                },
                                items: [

                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'hbox',
                                            pack: 'left'
                                        },
                                        items: [{
                                            xtype: 'textareafield',
                                            id:'mensaje',
                                            name: 'mensaje',
                                            fieldLabel: 'Configuracion',
                                            value: ejecucion.mensaje,
                                            cols: 75,
                                            rows: 19,
                                            anchor: '100%',
                                            readOnly:true
                                        }]
                                    },

                                ]
                            }],
                            buttons: [{
                                text: 'Cerrar',
                                formBind: true,
                                handler: function(){
                                    win.destroy();
                                }
                            }]
                        });

                        var win = Ext.create('Ext.window.Window', {
                            title: 'Ver Configuracion',
                            modal: true,
                            width: 630,
                            height: 550,
                            closable: true,
                            layout: 'fit',
                            items: [formPanel]
                        }).show();

                    }//cierre else

            },
            failure: function(result)
            {
                Ext.Msg.alert('Error ','Error: ' + result.statusText);
            }
        }); 
    }
    
       
}

/**
 * verDetallePaqueteHorasSoporte
 *
 * Función encargada de presentar todos los datos de un paquete de soporte de horas
 * 
 *
 * @return json con resultado del proceso
 *
 * @author Liseth Candelario <lcandelario@telconet.ec>
 * @version 1.0 21-11-2022
 *
 *
 */
 function verDetallePaqueteHorasSoporte(data){

    var intIdServicio               = data.idServicio;
    var intPersonaEmpresaRolId      = data.idPersonaEmpresaRol;
    var strUuIdPaquete              = data.strUuIdPaquete;
    var strNombreProducto           = data.nombreProducto;
    var strValorProductoPaqHorasRec = data.strValorProductoPaqHorasRec;

    //Grid posterior
    storeDatosTecnicosServicios = new Ext.data.Store({
        total: 'total',
        //autoLoad:true,
        proxy: {
            type: 'ajax',
            url: urlAjaxGetHorasSoporte,
            extraParams: {
                uuid_paquete            : strUuIdPaquete,
                persona_empresa_rol_id  : intPersonaEmpresaRolId,
                servicio_paquete_id     : intIdServicio
            },
            reader: {
                type: 'json',
                // totalProperty: 'total',
                root: 'servicios'
            }
        },
        fields:
            [
                //name: es el nombre del campo y mapping es lo que viene del store
                {name:'login_punto',                mapping: 'login_punto'},
                {name:'producto',                   mapping: 'producto'},
                {name:'permite_activar_paquete',    mapping: 'permite_activar_paquete'},
                {name:'login_auxiliar',             mapping: 'login_auxiliar'},
                {name:'usuario_creacion',           mapping: 'usuario_creacion'},
                {name:'fecha_creacion',             mapping: 'fecha_creacion'},
            ]
    });
    storeDatosTecnicosServicios.load();
    Ext.onReady(function() {

        Ext.define('User', {
            extend: 'Ext.data.Model',
            fields: [{
                name: 'value',
                type: 'string'
            }, {
                name: 'tag',
                type: 'string'
            }]
        });
    
        //Grid posterior
        gridHorasSoporte = Ext.create('Ext.grid.Panel', {
            width  : 950,
            height : 400,
            store : storeDatosTecnicosServicios,
            columns : [ 
                {
                    header : 'Login punto',
                    dataIndex : 'login_punto',
                    sortable: true,
                    width : '18%'
                },
                {
                    header : 'Login auxiliar',
                    dataIndex : 'login_auxiliar',
                    sortable: true,
                    width : '18%'
                },
                {
                    header : 'Producto',
                    dataIndex : 'producto',
                    sortable: true,
                    width : '28%'
                },
                {
                    header : 'Activa paquete de horas de soporte',
                    dataIndex : 'permite_activar_paquete',
                    sortable: true,
                    width : '15%'
                },
                {
                    header : 'Usuario creación',
                    dataIndex : 'usuario_creacion',
                    sortable: true
                },
                {
                    header : 'Fecha creación',
                    dataIndex : 'fecha_creacion',
                    sortable: true,
                    width : '10%'
                }
            ],

            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeDatosTecnicosServicios,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos para mostrar"
            }),
            multiSelect: false,
            viewConfig: {
                emptyText: 'No hay datos para mostrar'
            },
            listeners:{
                    itemdblclick: function( view, record, item, index, eventobj, obj ){
                        var position = view.getPositionByEvent(eventobj),
                        data = record.data,
                        value = data[this.columns[position.column].dataIndex];
                        Ext.Msg.show({
                            title:'Copiar texto?',
                            msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                            buttons: Ext.Msg.OK,
                            icon: Ext.Msg.INFORMATION
                        });
                    }
            }
    });
});

    Ext.Ajax.request({
        url: urlAjaxGetHorasSoporte,
        method: 'post',
        timeout: 400000,
        params: { 
            uuid_paquete            : strUuIdPaquete,
            persona_empresa_rol_id  : intPersonaEmpresaRolId,
            servicio_paquete_id     : intIdServicio
        },
        type: 'ajax',

        reader: {
            type: 'json',
            totalProperty: 'total',
            root: 'encontrados'
        },
        success: function(response){
            Ext.get(gridServicios.getId()).unmask();        
            var json = Ext.JSON.decode(response.responseText);
            var status = json.status
            var mensaje = json.mensaje
            if (status==500) {
                window.alert(mensaje);
            }

            //Grid superior
            var formPanel = Ext.create('Ext.panel.Panel', {
                title: 'Informaciòn en general del paquete de soporte',
                width: 950,
                height: 400,
                renderTo: Ext.getBody(),
                layout: {
                    type: 'vbox',       // Arrange child items vertically
                    align: 'stretch',    // Each takes up full width
                    padding: 5
                },
                items: [
                        // Cuadrícula de resultados 
                        {
                            bodyPadding: 2,
                            waitMsgTarget: true,
                            layout: {
                                type: 'hbox',
                                width: 280,
                                align: 'stretch'
                            },
                            store : json,
                            items: [
                                        {
                                            xtype: 'fieldcontainer',
                                            flex: 2,
                                            height: 140,
                                            items: [
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: '<b>Login principal</b>',
                                                    id : 'login',
                                                    dataIndex : 'login',
                                                    readOnly: true,
                                                    fieldStyle: 'background:none',
                                                    value: json.login_punto
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: '<b>Login auxiliar</b>',
                                                    id: 'login_auxiliar',
                                                    emptyText: 'Login aux',
                                                    readOnly: true,
                                                    value: json.login_auxiliar
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: '<b>Minutos contratados</b>',
                                                    id: 'minutos_acumulados',
                                                    readOnly: true,
                                                    value: strNombreProducto === strValorProductoPaqHorasRec ? 
                                                            json.minutos_contratados : json.minutos_totales
                                                    //Si el producto es recarga muestra el valor de recarga
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: '<b>Minutos restantes</b>',
                                                    id: 'minutos_vigentes',
                                                    readOnly: true,
                                                    value: strNombreProducto === strValorProductoPaqHorasRec ? 
                                                            json.minutos_restantes : json.minutos_vigentes
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'fieldcontainer',
                                            flex: 2,
                                            height: 140,
                                            items: [
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: '<b>Acumulación</b>',
                                                    id: 'acumula_tiempo',
                                                    readOnly: true,
                                                    value: json.acumula_tiempo
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: '<b>Forma de Soporte</b>',
                                                    id: 'forma_de_soporte',
                                                    readOnly: true,
                                                    value: json.forma_de_soporte
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: '<b>Acceso soporte</b>',
                                                    id: 'acceso_de_soporte',
                                                    readOnly: true,
                                                    value: json.acceso_de_soporte
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: '<b>Embebido</b>',
                                                    id: 'embebido',
                                                    readOnly: true,
                                                    value: json.embebido
                                                }
                                            ]
                                        },
                                        {
                                            xtype: 'fieldcontainer',
                                            flex: 2,
                                            height: 140,
                                            items: [
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: '<b>Fecha de contratación</b>',
                                                    id: 'feInicio',
                                                    readOnly: true,
                                                    value: json.fecha_inicio,
                                                    format: 'd/m/Y'
                                                },
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: '<b>Fecha de expiración</b>',
                                                    id: 'feFin',
                                                    readOnly: true,
                                                    value: json.fecha_fin,
                                                    format: 'd/m/Y'
                                                }
                                            ]
                                        }
                                    ]
                        }, 
                        {
                            xtype: 'splitter'   // Un divisor entre los dos elementos secundarios
                        }, 
                        { 
                            // Panel de detalles especificado como un objeto de configuración (ningún tipo predeterminado es 'panel').
                            title: 'Informaciòn de login y productos',
                            bodyPadding: 2,
                            items: [ gridHorasSoporte ], 
                            flex: 2             
                        }
                    ],
                    buttons: [
                        {
                            text: 'Cerrar',
                            handler: function(){ win.destroy();  }
                        }
                    ]
            });

            // presenta ambos grid
            var win = Ext.create('Ext.window.Window', {
                title : 'DETALLE DE '+ strNombreProducto,
                modal : true,
                width : 1000,
                height : 700,
                resizable : false,
                layout : 'fit',
                items : [ formPanel ],
                buttonAlign : 'center'
            }).show();

        },
        failure: function(result)
        {
            var mensajeText ='No hay data que mostrar';
            Ext.get(gridServicios.getId()).unmask();
            Ext.Msg.alert('Error ','Error: ' + result.statusText + mensajeText);
        }

    });   
}

/**
 * paqueteHorasSoporte
 *
 * Función que muestra un modal para seleccionar a que login se le realizarà el soporte y así mismo seleccionar el tipo de soporte
 *
 * @author Liseth Candelario <lcandelario@telconet.ec>
 * @version 1.0 21-11-2022
 *
 *
 */
function paqueteHorasSoporte(data){
    
    var intIdServicio           =  data.idServicio;
    var intPersonaEmpresaRolId  =  data.idPersonaEmpresaRol;
    var strUuIdPaquete          =  data.strUuIdPaquete;
    var storeTipoSoporte = Ext.create('Ext.data.Store', {
        fields: ['tipo_soporte', 'name'],
        data : [
            {"tipo_soporte":"Caso",          "name":"Caso"},
            {"tipo_soporte":"Requerimiento", "name":"Requerimiento"}
        ]
    });

    var storeServiciosSoporte = new Ext.data.Store({ 
        total: 'total',
        pageSize: 100,
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url: urlAjaxGetServiciosSoporte,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                servicio_id             : intIdServicio,
                uuid_paquete            : strUuIdPaquete,
                persona_empresa_rol_id  : intPersonaEmpresaRolId
            }
        },
        fields:
            [
                {name: 'descripcion_producto', mapping: 'descripcion_producto'},
                {name: 'id_producto',          mapping: 'id_producto'}
            ]

    });

    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        layout: 'hbox',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            msgTarget: 'side'
        },
        items: [            
        {
            xtype: 'fieldset',
            title: '',
            defaultType: 'textfield',
            items: [
                {
                    xtype: 'fieldset',
                    title: '', 
                    defaultType: 'textfield',
                    defaults: {
                        width : 600
                    },
                    //los campos para seleccionar
                    items: [
                        {
                            xtype: 'combobox',
                            fieldLabel: 'Tipo soporte',
                            store: storeTipoSoporte,
                            emptyText: 'Seleccione',
                            id : 'tipo_soporte',
                            dataIndex : 'tipo_soporte',
                            valueField: 'tipo_soporte',
                            editable: false,
                            displayField: 'tipo_soporte',
                            forceSelection: true
                        }
                        ,
                        {
                            xtype: 'combobox',
                            id: 'descripcion_producto',
                            fieldLabel: 'Servicio afectado',
                            store: storeServiciosSoporte ? storeServiciosSoporte : 'No hay servicio asociados al punto',
                            valueField: 'id_producto',
                            dataIndex : 'descripcion_producto',
                            editable: false,
                            emptyText: 'Seleccione',
                            displayField: 'descripcion_producto',
                            forceSelection: true
                        }
                    ]
                }
            ]
        }
        ],
        buttons: [
            {
                text: 'Enviar',
                handler: function(){ enviarValores();  
                    winSeleccion.destroy();}           
            },
            {
                text: 'Cancelar',
                handler: function(){ winSeleccion.destroy();  }
            }
        ]   
    });

    var winSeleccion = Ext.create('Ext.window.Window', {
        title : 'Registrar soportes',
        modal : true,
        width : 700,
        height : 250,
        resizable : false,
        layout : 'fit',
        items : [ formPanel ],
        buttonAlign : 'center'
    }).show();
}

/**
 * function enviarValores()
 *
 * Función se encarga de redirigir a el módulo soporte con los valores seleccionado de la función :paqueteHorasSoporte()
 *
 * @author Liseth Candelario <lcandelario@telconet.ec>
 * @version 1.0 21-11-2022
 *
 *
 */
function enviarValores()
{
    strTipoSoporte   = Ext.getCmp('tipo_soporte').value
    stringProducto   = Ext.getCmp('descripcion_producto').value //es el login del producto seleccionado

    if (strTipoSoporte === 'Caso')
    {
       let ventana = window.open('/soporte/info_caso/new');
       ventana.addEventListener('DOMContentLoaded',function()
        {
            console.log ('ventana de soporte abierta')      
            ventana.document.getElementById('tipo_soporte').value = strTipoSoporte
            ventana.document.getElementById('descripcion_producto').value   = stringProducto
            ventana.document.getElementById('bool_paqueteSoporte').value = 'S'
        }
       )
    }
    else if (strTipoSoporte === 'Requerimiento') 
    {
        let ventana = window.open('/soporte/call_activity/new');
        ventana.addEventListener('DOMContentLoaded',function()
         {
            console.log ('ventana de soporte abierta')      
            ventana.document.getElementById('tipo_soporte').value = strTipoSoporte
            ventana.document.getElementById('descripcion_producto').value   = stringProducto
            ventana.document.getElementById('bool_paqueteSoporte').value = 'S'
         }
        )
    }
}

function enviarCorreoResumenCompra(data){
       Ext.Msg.confirm('Alerta', ' Desea reenviar el correo Resumen Compra?', function (btn) {
        if (btn == 'yes')
        {
            Ext.MessageBox.wait("Enviando correo...");	
            Ext.Ajax.request({ 
                url: setCorreoResumenCompra,
                method: 'post',
                waitMsg: 'Enviando Correo Resumen Compra',
                timeout: 400000,
                params: { idServicio: data.idServicio
                        },
                success: function(result){
                        Ext.Msg.alert('Correo',result.responseText);
        
                },
                failure: function(result) 
                {
                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                }
            }); 
            
        }                
    });
}

function enviarCorreoCambioPlan(data){
       Ext.Msg.confirm('Alerta', ' Desea reenviar el correo Cambio de plan?', function (btn) {
        if (btn == 'yes')
        {
            Ext.MessageBox.wait("Enviando correo...");	
            Ext.Ajax.request({ 
                url: setCambioPlan,
                method: 'post',
                waitMsg: 'Enviando Correo Cambio de plan',
                timeout: 400000,
                params: { idServicio: data.idServicio
                        },
                success: function(result){
                        Ext.Msg.alert('Correo',result.responseText);
        
                },
                failure: function(result) 
                {
                    Ext.Msg.alert('Error ','Error: ' + result.statusText);
                }
            }); 
            
        }                
    });
}
