function seguridadSdwan(data)
{   
    var seguridadSdwan = "";
    Ext.Ajax.request({
            url: urlAjaxGetEquipoSdwan,
            method: 'post',
            timeout: 120000000,
            params:
                {
                    idServicio : data.idServicio,
                    idEmpresa  : data.idEmpresa,
                    idPunto    : data.idPunto
                },
            success: function (response) {
                seguridadSdwan = response.responseText;
                if( seguridadSdwan == "undefined" || seguridadSdwan === "null" )
                {
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: 'No existe un Servicio SDWAN Activo',
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.ERROR
                    }); 
                    return;
                }
            },
            failure: function (response)
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: response.responseText,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        });     
    if(data.elementoPadre=="No definido")
    {
        Ext.MessageBox.show({
            title: 'Error',
            msg: 'No existe un Servicio SDWAN Activo',
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR
        }); 
        return;
    }
    else
    {
        var storeProtocolosEnrutamiento = new Ext.data.Store({
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlAjaxGetEquipoSdwan,
                timeout: 120000000,
                reader: {
                    type: 'json',
                    root: 'data'
                },
                extraParams:{
                    idServicio : data.idServicio,
                    idEmpresa  : data.idEmpresa,
                    idPunto    : data.idPunto
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                }
            },
            fields:
                [
                    {name: 'serie', mapping: 'serie'},
                    {name: 'servicioSdwan', mapping: 'servicioSdwan'}
                ]
        });
        
        
        var formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 80,
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
                        title: 'Enlace Seguridad con Equipo',
                        defaultType: 'textfield',
                        defaults: {
                            width: 250
                        },
                        items: [
                            {
                                xtype: 'combobox',
                                id:   'cmb_serie',
                                name: 'cmb_serie',
                                displayField: "serie",
                                valueField: "servicioSdwan",
                                fieldLabel: "Serie",
                                queryMode:'local',
                                emptyText: 'Seleccione',
                                store:storeProtocolosEnrutamiento  
                            },{
                                xtype: 'datefield',
                                fieldLabel: 'Fecha Caducidad',
                                name: 'fechaCad',
                                id: 'fechaCad',
                                value:'',
                                minValue: new Date(),
                                format: 'd-m-Y'
                                //hideTrigger: boolHideNumberTrigger
                            }
                        ]
                    }

                ]
            }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    formBind: true,
                    handler: function(){
                        
                        var serie_equipo  = Ext.getCmp('cmb_serie').getValue();
                        var fecha_caducidad    = Ext.getCmp('fechaCad').getValue();
                        
                        if(serie_equipo==null)
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Por favor seleccione un Equipo',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            }); 
                            return;
                        }  
                        
                        if(Ext.isEmpty(fecha_caducidad))
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Por favor escoger una Fecha',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            }); 
                            return;
                        }
                        
                        Ext.Msg.confirm({
                            title:'Confirmar',
                            msg: 'Esta seguro de Activar el Servicio?',
                            buttons: Ext.Msg.YESNO,
                            icon: Ext.MessageBox.QUESTION,
                            buttonText: {
                                yes: 'si', no: 'no'
                            },
                            fn: function(btn){
                                if(btn=='yes'){
                                    Ext.MessageBox.wait('Activando Servicio...');
                                    
                                    Ext.Ajax.request({
                                        url: urlGuardarEnlaceSeguridad,
                                        method: 'post',
                                        timeout: 900000,
                                        params: { 
                                            idServicio:             data.idServicio,
                                            serie:                  serie_equipo,
                                            fechaCad:               fecha_caducidad
                                        },
                                        success: function(response){
                                            Ext.MessageBox.hide();
                                            win.hide();
                                            
                                            Ext.Msg.alert('Mensaje',response.responseText, function(btn){
                                                if(btn=='ok' || btn=='cancel')
                                                {
                                                        win.destroy();
                                                    store.load();
                                                }
                                            });
                                        },
                                        failure: function(response)
                                        {
                                            Ext.MessageBox.hide();
                                            win.hide();
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: response.responseText,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR,
                                                fn: function(btn){
                                                    if(btn=='ok')
                                                    {
                                                        win.show();    
                                                    }    
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
                    handler: function(){
                        win.destroy();
                    }
                }]
        });

        var win = Ext.create('Ext.window.Window', {
            title: 'Seguridad Sdwan',
            modal: true,
            closable: true,
            layout: 'fit',
            width: 340,
            items: [formPanel]
        }).show();
        
        win.center();
    }
}

function seguridadCpe(data)
{   
    var seguridadSdwan = "";
    Ext.Ajax.request({
            url: urlAjaxGetEquipoCpe,
            method: 'post',
            timeout: 120000000,
            params:
                {
                    idServicio : data.idServicio,
                    idEmpresa  : data.idEmpresa,
                    idPunto    : data.idPunto
                },
            success: function (response) {
                var objData    = Ext.JSON.decode(response.responseText);
                var strStatus  = objData.status;
                var strMensaje = objData.mensaje;
                
                if (strStatus == 'ERROR')
                {
                    Ext.MessageBox.show({
                        title  : 'Error',
                        msg    : strMensaje,
                        buttons: Ext.MessageBox.OK,
                        icon   : Ext.MessageBox.ERROR
                    }); 
                    return;
                }
                else
                {
                    seguridadSdwan = response.responseText;
                    if( seguridadSdwan == "undefined" || seguridadSdwan === "null" )
                    {
                        Ext.MessageBox.show({
                            title  : 'Error',
                            msg    : 'No existe un Servicio Internet/Datos MPLS Activo',
                            buttons: Ext.MessageBox.OK,
                            icon   : Ext.MessageBox.ERROR
                        }); 
                        return;
                    }
                }
            },
            failure: function (response)
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: response.responseText,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        });     
    if(data.elementoPadre=="No definido")
    {
        Ext.MessageBox.show({
            title: 'Error',
            msg: 'No existe un Servicio Internet/Datos MPLS Activo',
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR
        }); 
        return;
    }
    else
    {
        var storeProtocolosEnrutamiento = new Ext.data.Store({
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlAjaxGetEquipoCpe,
                timeout: 120000000,
                reader: {
                    type: 'json',
                    root: 'data'
                },
                extraParams:{
                    idServicio : data.idServicio,
                    idEmpresa  : data.idEmpresa,
                    idPunto    : data.idPunto
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                }
            },
            fields:
                [
                    {name: 'serie', mapping: 'serie'},
                    {name: 'servicioSdwan', mapping: 'servicioSdwan'}
                ]
        });
        
        var storeCombosEquipos = new Ext.data.Store({
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlAjaxOpcionesCpe,
                timeout: 120000000,
                reader: {
                    type: 'json',
                    root: 'data'
                },
                extraParams:{
                    idEmpresa  : data.idEmpresa
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                }
            },
            fields:
                [
                    {name: 'opcion', mapping: 'opcion'},
                    {name: 'boolOpcion', mapping: 'boolOpcion'}
                ]
        });
        
        var formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 2,
            waitMsgTarget: true,
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
                        title: 'Enlace Seguridad con Equipo',
                        defaultType: 'textfield',
                        defaults: {
                            width: 250
                        },
                        items: [
                            {
                                xtype: 'combobox',
                                id:   'cmb_serie',
                                name: 'cmb_serie',
                                displayField: "serie",
                                valueField: "servicioSdwan",
                                fieldLabel: "Serie",
                                queryMode:'local',
                                emptyText: 'Seleccione',
                                store:storeProtocolosEnrutamiento  
                            },
                            {
                                xtype: 'combobox',
                                id:   'cmb_fortianalyzer',
                                name: 'cmb_fortianalyzer',
                                displayField: "opcion",
                                valueField: "boolOpcion",
                                fieldLabel: "Es Fortianalyzer",
                                queryMode:'local',
                                emptyText: 'Seleccione',
                                store:storeCombosEquipos  
                            },
                            {
                                xtype: 'combobox',
                                id:   'cmb_fortianalyzer2',
                                name: 'cmb_fortianalyzer2',
                                displayField: "opcion",
                                valueField: "boolOpcion",
                                fieldLabel: "Es Fortianalyzer 2",
                                queryMode:'local',
                                emptyText: 'Seleccione',
                                store:storeCombosEquipos  
                            },
                            {
                                xtype: 'combobox',
                                id:   'cmb_fortimanager',
                                name: 'cmb_fortimanager',
                                displayField: "opcion",
                                valueField: "boolOpcion",
                                fieldLabel: "Es Fortimanager",
                                queryMode:'local',
                                emptyText: 'Seleccione',
                                store:storeCombosEquipos  
                            },
                            {
                                xtype: 'combobox',
                                id:   'cmb_syslog',
                                name: 'cmb_syslog',
                                displayField: "opcion",
                                valueField: "boolOpcion",
                                fieldLabel: "Es Syslog",
                                queryMode:'local',
                                emptyText: 'Seleccione',
                                store:storeCombosEquipos  
                            }
                        ]
                    }
                ]
            }
            ],
            buttons: [
                {
                    text: 'Guardar',
                    formBind: true,
                    handler: function(){
                        
                        var servicioTradicional = Ext.getCmp('cmb_serie').getValue();
                        var serie_equipo        = Ext.getCmp('cmb_serie').getRawValue();
                        var fortianalyzer       = Ext.getCmp('cmb_fortianalyzer').getValue();
                        var fortianalyzer2      = Ext.getCmp('cmb_fortianalyzer2').getValue();
                        var fortimanager        = Ext.getCmp('cmb_fortimanager').getValue();
                        var syslog              = Ext.getCmp('cmb_syslog').getValue();
                                                
                        if(serie_equipo==null)
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Por favor seleccione un Equipo',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            }); 
                            return;
                        }  
                        
                        if(fortianalyzer==null)
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Por favor seleccione una opción en Es Fortianalyzer',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            }); 
                            return;
                        }
                        
                        if(fortianalyzer2==null)
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Por favor seleccione una opción en Es Fortianalyzer2',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            }); 
                            return;
                        }
                        
                        if(fortimanager==null)
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Por favor seleccione una opción en Es Fortimanager',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            }); 
                            return;
                        }
                        
                        if(syslog==null)
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Por favor seleccione una opción en Es Syslog',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            }); 
                            return;
                        }
                        Ext.Msg.confirm({
                            title:'Confirmar',
                            msg: 'Esta seguro de Activar el Servicio?',
                            buttons: Ext.Msg.YESNO,
                            icon: Ext.MessageBox.QUESTION,
                            buttonText: {
                                yes: 'si', no: 'no'
                            },
                            fn: function(btn){
                                if(btn=='yes'){
                                    Ext.MessageBox.wait('Activando Servicio...');
                                    
                                    Ext.Ajax.request({
                                        url: urlGuardarEnlaceSeguridadCpe,
                                        method: 'post',
                                        timeout: 900000,
                                        params: { 
                                            idServicio:             data.idServicio,
                                            idServicioTradicional:  servicioTradicional, 
                                            serie:                  serie_equipo,
                                            fortianalyzer:          fortianalyzer,
                                            fortianalyzer2:         fortianalyzer2,
                                            fortimanager:           fortimanager,
                                            syslog:                 syslog
                                        },
                                        success: function(response){
                                            Ext.MessageBox.hide();
                                            win.hide();
                                            
                                            Ext.Msg.alert('Mensaje',response.responseText, function(btn){
                                                if(btn=='ok' || btn=='cancel')
                                                {
                                                        win.destroy();
                                                    store.load();
                                                }
                                            });
                                        },
                                        failure: function(response)
                                        {
                                            Ext.MessageBox.hide();
                                            win.hide();
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: response.responseText,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR,
                                                fn: function(btn){
                                                    if(btn=='ok')
                                                    {
                                                        win.show();    
                                                    }    
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
                    handler: function(){
                        win.destroy();
                    }
                }]
        });

        var win = Ext.create('Ext.window.Window', {
            title: 'SECURE CPE',
            modal: true,
            closable: true,
            layout: 'fit',
            width: 340,
            items: [formPanel]
        }).show();
        
        win.center();
    }
}

function migracionCpeSecureNg(data)
{   
    var seguridadSdwan = "";
    Ext.Ajax.request({
            url: urlAjaxGetMigrarEquipoCpe,
            method: 'post',
            timeout: 120000000,
            params:
                {
                    idServicio : data.idServicio,
                    idEmpresa  : data.idEmpresa,
                    idPunto    : data.idPunto
                },
            success: function (response) {
                var objData    = Ext.JSON.decode(response.responseText);
                var strStatus  = objData.status;
                var strMensaje = objData.mensaje;
                
                if (strStatus == 'ERROR')
                {
                    Ext.MessageBox.show({
                        title  : 'Error',
                        msg    : strMensaje,
                        buttons: Ext.MessageBox.OK,
                        icon   : Ext.MessageBox.ERROR
                    }); 
                    return;
                }
                else
                {
                    seguridadSdwan = response.responseText;
                    if( seguridadSdwan == "undefined" || seguridadSdwan === "null" )
                    {
                        Ext.MessageBox.show({
                            title  : 'Error',
                            msg    : 'No existe un Servicio Internet/Datos MPLS Activo',
                            buttons: Ext.MessageBox.OK,
                            icon   : Ext.MessageBox.ERROR
                        }); 
                        return;
                    }
                }
            },
            failure: function (response)
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: response.responseText,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        });     
    if(data.elementoPadre=="No definido")
    {
        Ext.MessageBox.show({
            title: 'Error',
            msg: 'No existe un Servicio Internet/Datos MPLS Activo',
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR
        }); 
        return;
    }
    else
    {
        var storeProtocolosEnrutamiento = new Ext.data.Store({
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlAjaxGetMigrarEquipoCpe,
                timeout: 120000000,
                reader: {
                    type: 'json',
                    root: 'data'
                },
                extraParams:{
                    idServicio : data.idServicio,
                    idEmpresa  : data.idEmpresa,
                    idPunto    : data.idPunto
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                }
            },
            fields:
                [
                    {name: 'serie', mapping: 'serie'},
                    {name: 'servicioSdwan', mapping: 'servicioSdwan'}
                ]
        });
        
        
        var formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 2,
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 80,
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
                        title: 'Migración Secure NG Firewall',
                        defaultType: 'textfield',
                        defaults: {
                            width: 250
                        },
                        items: [
                            {
                                xtype: 'combobox',
                                id:   'cmb_serie',
                                name: 'cmb_serie',
                                displayField: "serie",
                                valueField: "servicioSdwan",
                                fieldLabel: "Serie",
                                queryMode:'local',
                                emptyText: 'Seleccione',
                                store:storeProtocolosEnrutamiento,
                                listeners: {
                                    select:
                                        function(e) {
                                            var strSerieTrad    = Ext.getCmp('cmb_serie').getRawValue();
                                            var strServicioTrad = Ext.getCmp('cmb_serie').getValue();
                                            ventanaModelo(strSerieTrad,strServicioTrad);
                                        }
                                }
                            },
                            {
                                xtype: 'textfield',
                                id: 'mac',
                                name: 'mac',
                                fieldLabel: 'Mac',
                                displayField: '',
                                valueField: '',
                                width: '25%'
                            }
                        ]
                    }

                ]
            }
            ],
            buttons: [
                {
                    text: 'Migrar',
                    formBind: true,
                    handler: function(){
                        
                        var idServicioTradicional    = Ext.getCmp('cmb_serie').getValue();
                        var serie_equipo             = Ext.getCmp('cmb_serie').getRawValue();
                        var macIpFija                = Ext.getCmp('mac').getValue();
                                                                        
                        if(serie_equipo==null)
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Por favor seleccione un Equipo a migrar',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            }); 
                            return;
                        } 
                        
                        var regex = /^([0-9a-f]{2}([:-]|$)){6}$|([0-9a-f]{4}([.]|$)){3}$/i;
                        macIpFija = macIpFija.replace(/\s/g, '');
                        if (macIpFija == "" || !macIpFija.match(regex)) 
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Formato de Mac Incorrecta, favor ingrese con el formato (aaaa.bbbb.cccc)',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            }); 
                            Ext.getCmp('mac').value="";
                            Ext.getCmp('mac').setRawValue("");
                            return;
                        }
                        
                        Ext.Msg.confirm({
                            title:'Confirmar',
                            msg: 'Esta seguro de migrar equipo Secure NG Firewall?',
                            buttons: Ext.Msg.YESNO,
                            icon: Ext.MessageBox.QUESTION,
                            buttonText: {
                                yes: 'si', no: 'no'
                            },
                            fn: function(btn){
                                if(btn=='yes'){
                                    Ext.MessageBox.wait('Migrando Servicio...');
                                    
                                    Ext.Ajax.request({
                                        url: urlMigrarSecureNgFirewall,
                                        method: 'post',
                                        timeout: 900000,
                                        params: { 
                                            idServicio:             data.idServicio,
                                            idServicioTradicional:  idServicioTradicional,
                                            serie:                  serie_equipo,
                                            mac:                    macIpFija
                                        },
                                        success: function(response){
                                            Ext.MessageBox.hide();
                                            win.hide();
                                            
                                            Ext.Msg.alert('Mensaje',response.responseText, function(btn){
                                                if(btn=='ok' || btn=='cancel')
                                                {
                                                        win.destroy();
                                                    store.load();
                                                }
                                            });
                                        },
                                        failure: function(response)
                                        {
                                            Ext.MessageBox.hide();
                                            win.hide();
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: response.responseText,
                                                buttons: Ext.MessageBox.OK,
                                                icon: Ext.MessageBox.ERROR,
                                                fn: function(btn){
                                                    if(btn=='ok')
                                                    {
                                                        win.show();    
                                                    }    
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
                    handler: function(){
                        win.destroy();
                    }
                }]
        });

        var win = Ext.create('Ext.window.Window', {
            title: 'MIGRACIÓN EQUIPO SECURE NG FIREWALL',
            modal: true,
            closable: true,
            layout: 'fit',
            width: 340,
            items: [formPanel]
        }).show();
        
        win.center();
    }
}

function ventanaModelo(strSerie, strServicioTrad) 
{
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: [
                    {name:'loginAuxiliar', type: 'string'},
                    {name:'productoAsociado', type: 'string'},
                    {name:'mac', type: 'string'},
                    {name:'puerto', type: 'string'}
                ]
    });
    
    storeGrid = Ext.create('Ext.data.JsonStore', {
        model: 'ListaDetalleModel',
        proxy: {
            type: 'ajax',
            url: gridTradicional,
            timeout: 120000000,
            reader: {
                type: 'json',
                root: 'encontrados',
                totalProperty: 'total'
            },
            extraParams:{
                    serie         : strSerie,
                    servicioTrad  : strServicioTrad,
                },
            simpleSortMode: true
        },
        autoLoad: true
    });
          
    var listView = Ext.create('Ext.grid.Panel', {
        collapsible:false,
        store: storeGrid,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        columns: [
            new Ext.grid.RowNumberer(),  
            {
                text: 'Login Auxiliar',
                width: 150,
                dataIndex: 'loginAuxiliar'
            },{
                text: 'Producto Asociado',
                width: 150,
                dataIndex: 'productoAsociado'
            },{
                text: 'Mac',
                width: 120,
                dataIndex: 'mac'
            },{
                text: 'Puerto',
                dataIndex: 'puerto',
                align: 'right',
                width: 100			
            }
        ],
        buttons: [
            {
                text: 'Cerrar',
                handler: function(){
                    win.destroy();
                }
            }]
        });

        var win = Ext.create('Ext.window.Window', {
            title: 'Servicios Asociados al Equipo a migrar',
            modal: true,
            closable: true,
            layout: 'fit',
            width: 600,
            height:300,
            items: [listView]
        }).show();
        
        win.center();
}