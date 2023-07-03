/**
 * Funcion que sirve para actualizar
 * el indice del cliente en la base de datos.
 * Se lo utiliza para regularizar data sobre los
 * cambios de linea pon.
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 28-05-2014
 * */
function updateIndiceCliente(data)
{
    var formPanel = Ext.create('Ext.form.Panel', 
    {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: 
        {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        layout: 
        {
            type: 'table',
            // The total column count must be specified here
            columns: 2
        },
        items: 
        [
            {
                xtype: 'fieldset',
                title: 'Indice Cliente',
                defaultType: 'textfield',
                defaults: 
                {
                    width: 250
                },
                items: 
                [
                    {
                        xtype: 'container',
                        layout: 
                        {
                            type: 'table',
                            columns: 5,
                            align: 'stretch'
                        },
                        items: 
                        [
                            { width: '10%', border: false},
                            {
                                xtype: 'textfield',
                                id:'indice',
                                name: 'indice',
                                fieldLabel: 'Indice Cliente',
                                displayField: '',
                                valueField: '',
                                width: '25%'
                            },
                            { width: '10%', border: false},
                            { width: '10%', border: false},
                            { width: '10%', border: false}
                        ]
                    }
                ]
            }
        ],
        buttons: 
        [
            {
                text: 'Ejecutar',
                formBind: true,
                handler: function()
                {
                    var flag=true;
                    var indice = Ext.getCmp('indice').value;
                    if(indice<1 || indice>64)
                    {
                        //fuera de rango
                        flag=false;
                    }
                    
                    if(isNaN(indice))
                    {
                        //no es numero
                        flag=false;
                    }
                    
                    if(flag)
                    {
                        Ext.get(gridServicios.getId()).mask('Loading...');
                        Ext.Ajax.request
                        ({
                            url: updateIndiceClienteBoton,
                            method: 'post',
                            waitMsg: 'Esperando Respuesta del Elemento',
                            timeout: 400000,
                            params: 
                            {
                                idServicio: data.idServicio,
                                indiceCliente: Ext.getCmp('indice').value
                            },
                            success: function(response)
                            {
                                var respuesta = response.responseText;

                                if(respuesta=="ERROR, NO EXISTE RELACION TAREA - ACCION")
                                {
                                    Ext.Msg.alert('Error ','No Existe la Relacion Tarea - Accion');
                                    Ext.get(gridServicios.getId()).unmask();
                                }
                                else if(respuesta.indexOf("El host no es alcanzable a nivel de red")!=-1)
                                {
                                    Ext.Msg.alert('Error ','No se puede Conectar al Elemento <br>Favor revisar con el Departamento Tecnico');
                                    Ext.get(gridServicios.getId()).unmask();
                                }
                                else if(respuesta=="ERROR GENERAL")
                                {
                                    Ext.Msg.alert('Error ','Se presentaron problemas al procesar la transaccion, favor notificar a Sistemas.');
                                    Ext.get(gridServicios.getId()).unmask();
                                }
                                else if(respuesta=="ERROR INDICE")
                                {
                                    Ext.Msg.alert('Error ','El indice ingresado ya se encuentra utilizado.');
                                    Ext.get(gridServicios.getId()).unmask();
                                }
                                else
                                {
                                    Ext.Msg.alert('MENSAJE ','Se actualizo el indice del cliente');
                                    Ext.get(gridServicios.getId()).unmask();
                                }//cierre else
                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ','Error: ' + result.statusText);
                            }
                        });
                        win.destroy();
                    }
                    else
                    {
                        Ext.Msg.alert("Failed","Indice Fuera de rango, Favor Revisar!", function(btn)
                        {
                            if(btn=='ok')
                            {
                            }
                        });
                    }
                }
            },
            {
                text: 'Cancelar',
                handler: function()
                {
                    win.destroy();
                }
            }
        ]
    });
        
    var win = Ext.create('Ext.window.Window', 
    {
        title: 'Actualizar Indice Cliente',
        modal: true,
        width: 300,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function editarInformactionTecnicaCompleta(data, gridIndex){
    Ext.get(gridServicios.getId()).mask('Consultando Info Tecnica...');
    Ext.Ajax.request({
        url: getDatosTecnicos,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio: data.idServicio,
            accion: "editar"
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
                    sortable: true,
                    editor: {
                        //id:'searchTipo_cmp',
                        queryMode: 'local',
                        xtype: 'combobox',
                        displayField:'tipo',
                        valueField: 'tipo',
                        loadingText: 'Buscando...',
                        store: comboCaracteristica
                    }
                },{
                    header: 'Ip',
                    dataIndex: 'ip',
                    width: 150,
                    editor: {
                        id:'ip',
                        name:'ip',
                        xtype: 'textfield',
                        valueField: ''
                    }
                },
                {
                    header: 'Mascara',
                    dataIndex: 'mascara',
                    width: 150,
                    editor: {
                        id:'mascara',
                        name:'mascara',
                        xtype: 'textfield',
                        valueField: ''
                    }
                },
                {
                    header: 'Gateway',
                    dataIndex: 'gateway',
                    width: 150,
                    editor: {
                        id:'gateway',
                        name:'gateway',
                        xtype: 'textfield',
                        valueField: ''
                    }
                }],
                selModel: selIpPublica,
                viewConfig:{
                    stripeRows:true
                },

                // inline buttons
                dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                        itemId: 'removeButton',
                        text:'Eliminar',
                        tooltip:'Elimina el item seleccionado',
                        iconCls:'remove',
                        disabled: true,
                        handler : function(){eliminarSeleccion(gridIpPublica);}
                    }, '-', {
                        text:'Agregar',
                        tooltip:'Agrega un item a la lista',
                        iconCls:'add',
                        handler : function(){
                            // Create a model instance
                            var r = Ext.create('IpPublica', { 
                                ip: '',
                                mascara: '',
                                gateway: '',
                                tipo: ''

                            });
                            if(!existeRecordIpPublica(r, gridIpPublica))
                            {
                                storeIpPublica.insert(0, r);
                                cellEditing.startEditByPosition({row: 0, column: 1});
                            }
                            else
                            {
                              alert('Ya existe un registro vacio.');
                            }
                        }
                    }]
                }],
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
                            height: 100
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
                                        fieldLabel: 'Dslam',
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
                                        fieldLabel: 'Ip Dslam',
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
                                        fieldLabel: 'Modelo Dslam',
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
                                        fieldLabel: 'Perfil Dslam',
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
                                        fieldLabel: 'Interface Dslam',
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
                                        displayField: datosTecnicos[0].tipoMedio,
                                        value: datosTecnicos[0].tipoMedio,
                                        readOnly: true,
                                        width: '30%'
                                    },
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
                            height: 100
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
                            height: 200
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
                                        id:'ipCpe',
                                        name: 'ipCpe',
                                        fieldLabel: '* Ip Cpe',
                                        displayField: datosTecnicos[0].ipCpe,
                                        value: datosTecnicos[0].ipCpe,
                                        width: '30%'
                                    },
                                    {
                                        xtype: 'hidden',
                                        id:'jsonCaracteristicas',
                                        name: 'jsonCaracteristicas',
                                        displayField: '',
                                        valueField: '',
                                        width: '10%'
                                    },

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
                                        id:'numPc',
                                        name: 'numPc',
                                        fieldLabel: '* Numero PC',
                                        displayField: datosTecnicos[0].numPc,
                                        value: datosTecnicos[0].numPc,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------
                                    
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'ssid',
                                        name: 'ssid',
                                        fieldLabel: '* SSID',
                                        displayField: datosTecnicos[0].ssid,
                                        value: datosTecnicos[0].ssid,
                                        width: '30%'
                                    },
                                    { width: '15%', border: false},
                                    {
                                        xtype: 'textfield',
                                        id:'passSsid',
                                        name: 'passSsid',
                                        fieldLabel: '* Password',
                                        displayField: datosTecnicos[0].passSsid,
                                        value: datosTecnicos[0].passSsid,
                                        width: '30%'
                                    },
                                    { width: '10%', border: false},

                                    //---------------------------------------------
                                    
                                    { width: '10%', border: false},
                                    {
                                        xtype: 'textareafield',
                                        id:'observacionCliente',
                                        name: 'observacionCliente',
                                        fieldLabel: '* Observacion',
                                        displayField: datosTecnicos[0].observacion,
                                        labelPad: -57,
                                        //html: '4,1', 
                                        colspan: 4,
                                        value: datosTecnicos[0].observacion,
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
                            height: 200
                        },
                        items: [

                            gridIpPublica

                        ]
                    },//cierre interfaces cpe
                ],//cierre items
                buttons: [{
                    text: 'Grabar',
                    formBind: true,
                    handler: function(){
                        obtenerDatosCaracteristicas();
                        var jsonCaracteristicas = Ext.getCmp('jsonCaracteristicas').getRawValue();
                        var ipCpe = Ext.getCmp('ipCpe').getValue();
                        var numPc = Ext.getCmp('numPc').getValue();
                        var ssid = Ext.getCmp('ssid').getValue();
                        var password = Ext.getCmp('passSsid').getValue();
                        var observacion = Ext.getCmp('observacionCliente').getValue();

                        var validacion=true;
                        if(validacion){
                            Ext.get(formPanel.getId()).mask('Guardando datos y Ejecutando Scripts de Comprobacion!');


                            Ext.Ajax.request({
                                url: editarInformacionTecnica,
                                method: 'post',
                                timeout: 400000,
                                params: { 
                                    idServicio: data.idServicio,
                                    productoId: data.productoId,
                                    ipCpe: ipCpe,
                                    numPc: numPc,
                                    ssid: ssid,
                                    password: password,
                                    jsonCaracteristicas: jsonCaracteristicas,
                                    observacionCliente: observacion
                                },
                                success: function(response){
                                    Ext.get(formPanel.getId()).unmask();
                                    if(response.responseText == "OK"){
                        //                Ext.Msg.alert('Mensaje ','Se Activo el Cliente' );
                                        Ext.Msg.alert('Mensaje','Se Edito la informacion Tecnica', function(btn){
                                            if(btn=='ok'){
                                                win.destroy();
                                                store.load();
                                            }
                                        });
                                    }
                                    else{
                                        Ext.Msg.alert('Mensaje ','No se puede Editar la Informacion Tecnica' );
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
                            Ext.Msg.alert("Failed","Favor Revise los campos", function(btn){
                                    if(btn=='ok'){
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
                title: 'Editar Informacion Tecnica',
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