function protocolosEnrutamiento(data){
    if(data.elementoPadre=="No definido")
    {
        Ext.MessageBox.show({
            title: 'Error',
            msg: 'No existe PE asociado, Imposible realizar Cambio de Protocolo de Enrutamiento',
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR
        }); 
        return;
    }
    else
    {
    
        storeProtocolosEnrutamiento = new Ext.data.Store({
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlAjaxGetProtocolosEnrutamiento,
                timeout: 120000,
                reader: {
                    type: 'json',
                    root: 'data'
                },
                extraParams:{
                    nombreTecnico : data.descripcionProducto
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                }
            },
            fields:
                [
                    {name: 'descripcion', mapping: 'descripcion'}
                ]
        });
        
        var formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 2,
            buttonAlign: 'center',
            waitMsgTarget: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 85,
                msgTarget: 'side'
            },
            items: [
                {
                    xtype: 'fieldset',
                    title: 'Informacion de Protocolo',
                    defaultType: 'textfield',
                    defaults: {
                        width: 200
                    },
                    items: [
                        {
                            xtype: 'combobox',
                            id:   'cmb_protocolo',
                            name: 'cmb_protocolo',
                            displayField: "descripcion",
                            valueField: "descripcion",
                            fieldLabel: "Protocolo",
                            queryMode:'local',
                            emptyText: 'Seleccione',
                            store:storeProtocolosEnrutamiento  
                        },{
                            xtype: 'numberfield',
                            fieldLabel: 'As Privado',
                            name: 'as_privado',
                            id: 'as_privado',
                            value:'',
                        }
                    ]
                }
            ],
            buttons: [{
                    text: 'Agregar',
                    formBind: true,
                    handler: function(){
                        var expRegNumero = /^[0-9]{1,5}$/;
                        
                        var protocolo     = Ext.getCmp('cmb_protocolo').getValue();
                        var as_privado    = Ext.getCmp('as_privado').getValue();
                        
                        if(protocolo==null)
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Por favor seleccione un Protocolo',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            }); 
                            return;
                        }                                                  
                        
                        Ext.Msg.show({
                            title:'Confirmar',
                            msg: 'Esta seguro de agregar Protocolo de Enrutamiento?',
                            buttons: Ext.Msg.YESNOCANCEL,
                            icon: Ext.MessageBox.QUESTION,
                            buttonText: {
                                yes: 'si', no: 'no', cancel: 'cancelar'
                            },
                            fn: function(btn){
                                if(btn=='yes'){
                                    Ext.MessageBox.wait('Creando Protocolo de Enrutamiento...');
                                    
                                    Ext.Ajax.request({
                                        url: urlCrearProtocoloEnrutamiento,
                                        method: 'post',
                                        timeout: 400000,
                                        params: { 
                                            idServicio:    data.idServicio,
                                            idProducto:    data.productoId,
                                            nombreTecnico: data.descripcionProducto,
                                            idElemento:    data.elementoId,
                                            vrf:           data.vrf,
                                            pe:            data.elementoPadre,
                                            vlan:          data.vlan,
                                            gateway:       data.gwSubredServicio,
                                            ip:            data.ipServicio,
                                            loginAux:      data.loginAux,
                                            tipoEnlace:    data.tipoEnlace,
                                            protocolo:     protocolo,
                                            asPrivado:     as_privado
                                        },
                                        success: function(response){
                                            Ext.MessageBox.hide();
                                            win.destroy();
                                            
                                            Ext.Msg.alert('Mensaje',response.responseText, function(btn){
                                                if(btn=='ok'){
                                                    store.load();
                                                    win.destroy();
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
                },{
                text: 'Cancelar',
                handler: function(){
                    win.destroy();
                }
            }]
        });
        
        Ext.Ajax.request({
            url: urlAjaxGetAsPrivado,
            method: 'post',
            params: { idPersonaEmpresaRol : data.idPersonaEmpresaRol },
            success: function(response){
                var asPrivado = response.responseText;
                
                if(asPrivado>0)
                {
                    Ext.getCmp('as_privado').setValue(asPrivado);
                    Ext.getCmp('as_privado').setReadOnly(true);
                }
                else
                {
                    Ext.getCmp('as_privado').setReadOnly(false);
                }
            },
            failure: function(response)
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: response.responseText,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                }); 
            }
        });

        var win = Ext.create('Ext.window.Window', {
            title: 'Agregar Protocolo de Enrutamiento',
            modal: true,
            width: 350,
            closable: true,
            layout: 'fit',
            items: [formPanel]
        }).show();

    }    
}

function verProtocolosEnrutamiento(data)
{
    var titulo = "Protocolos Enrutamiento";

    if (typeof data.strTipoRed !== "undefined"){
        titulo = "Protocolo Enrutamiento - " + data.strTipoRed;
    }

    if(data.elementoPadre=="No definido")
    {
        Ext.MessageBox.show({
            title: 'Error',
            msg: 'No existe PE asociado, Imposible realizar Cambio de Protocolo de Enrutamiento',
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
                url: urlAjaxGetProtocolosEnrutamiento,
                timeout: 120000,
                reader: {
                    type: 'json',
                    root: 'data'
                },
                extraParams:{
                    nombreTecnico       : data.descripcionProducto,
                    strTipoRed          : data.strTipoRed
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                }
            },
            fields:
                [
                    {name: 'descripcion', mapping: 'descripcion'}
                ]
        });
        
        var storeProtocolosEnrutamientoServicio = new Ext.data.Store({ 
            autoLoad:true,
            proxy: {
                type: 'ajax',
                url : urlGetProtocolosEnrutamiento,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'data'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    idServicio: data.idServicio
                }
            },
            fields:
                [
                    {name:'id',            mapping:'id'           },
                    {name:'nombre',        mapping:'nombre'       },
                    {name:'feCreacion',    mapping:'feCreacion'   },
                    {name:'usrCreacion',   mapping:'usrCreacion'  }
                ]
        });
        
        var boolHideNumberTrigger = true;
        
        if(data.descripcionProducto === 'L3MPLS')
        {
            boolHideNumberTrigger = false;
        }
        
        Ext.Ajax.request({
            url: urlAjaxGetAsPrivado,
            method: 'post',
            params:
                {
                    idPersonaEmpresaRol: data.idPersonaEmpresaRol,
                    producto: data.descripcionProducto,
                    idServicio: data.idServicio
                },
            success: function (response) {
                var asPrivado = response.responseText;

                if (asPrivado > 0)
                {
                    Ext.getCmp('as_privado').setValue(asPrivado);
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
        
        gridProtocolosEnrutamiento = Ext.create('Ext.grid.Panel', {
            id:'gridProtocolosEnrutamiento',
            store: storeProtocolosEnrutamientoServicio,
            columnLines: true,
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeProtocolosEnrutamientoServicio,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
                }),
            columns: [
                    Ext.create('Ext.grid.RowNumberer'),
                    {
                        header: 'Nombre',
                        dataIndex: 'nombre',
                        sortable: true
                    },
                    {
                        header: 'Fecha Creacion',
                        dataIndex: 'feCreacion',
                        sortable: true,
                        width: 130
                    },
                    {
                        header: 'Usr Creacion',
                        dataIndex: 'usrCreacion',
                        sortable: true,
                        width: 130
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        hidden:true,
                        items: 
                        [
                            {
                                getClass: function(v, meta, rec) {
                                    
                                    if (!puedeEliminarProtocoloEnrutamiento) {
                                        return "icon-invisible";
                                    }
                                    
                                    return "button-grid-delete";
                                },
                                tooltip: 'Eliminar',
                                handler: function(grid, rowIndex, colIndex) {
                                    var rec = storeProtocolosEnrutamientoServicio.getAt(rowIndex);
                                    
                                    if(puedeEliminarProtocoloEnrutamiento)
                                    {
                                        Ext.Msg.show({
                                            title:'Confirmar',
                                            msg: 'Esta seguro de eliminar el Protocolo de Enrutamiento?',
                                            buttons: Ext.Msg.YESNOCANCEL,
                                            icon: Ext.MessageBox.QUESTION,
                                            buttonText: {
                                                yes: 'si', no: 'no', cancel: 'cancelar'
                                            },
                                            fn:  function(btn){
                                                if(btn=='yes'){
                                                    Ext.MessageBox.wait('Eliminando Protocolo de Enrutamiento...');
                                                    var configurar_PE = Ext.getCmp('chkConfigurarPE').getValue();
                                                    Ext.Ajax.request({
                                                        url: urlEliminarProtocoloEnrutamiento,
                                                        method: 'post',                                                        
                                                        params: { 
                                                            id : rec.get('id'),
                                                            idServicio:    data.idServicio,
                                                            idProducto:    data.productoId,
                                                            nombreTecnico: data.descripcionProducto,
                                                            idElemento:    data.elementoId,
                                                            vrf:           data.vrf,
                                                            pe:            data.elementoPadre,                                                            
                                                            vlan:          data.vlan,
                                                            gateway:       data.gwSubredServicio,
                                                            ip:            data.ipServicio,
                                                            loginAux:      data.loginAux,
                                                            tipoEnlace:    data.tipoEnlace,
                                                            protocolo:     rec.get('nombre'),
                                                            asPrivado:     data.asPrivado,
                                                            configurarPE:  configurar_PE
                                                        },
                                                        success: function(response){
                                                            Ext.MessageBox.hide();
                                                            win.hide();
                                                            Ext.Msg.alert('Mensaje', response.responseText, function(btn) {
                                                                if (btn == 'ok') {
                                                                    win.show();    
                                                                    storeProtocolosEnrutamientoServicio.load();
                                                                }
                                                            });
                                                        },
                                                        failure: function(response)
                                                        {
                                                            Ext.MessageBox.hide();
                                                            win.hide();
                                                            Ext.Msg.alert('Error', response.responseText, function(btn) {
                                                                if (btn == 'ok') {
                                                                    win.show();
                                                                    storeProtocolosEnrutamientoServicio.load();
                                                                }
                                                            });
                                                        }
                                                    });
                                                }
                                            }
                                        });
                                    } 
                                    else
                                        Ext.MessageBox.show({
                                            title: 'Error',
                                            msg: 'No tiene permisos para realizar esta accion',
                                            buttons: Ext.MessageBox.OK,
                                            icon: Ext.MessageBox.ERROR
                                        });
                            }
                        }
                    ]
                }
            ],        
            viewConfig:{
                stripeRows:true,
                enableTextSelection: true
            },
            height: 190,
            frame: true
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
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Listado',
                        items: [
                            gridProtocolosEnrutamiento
                         ]
                    },   
                    {
                        xtype: 'fieldset',
                        title: 'Agregar Protocolo de Enrutamiento',
                        defaultType: 'textfield',
                        defaults: {
                            width: 200
                        },
                        items: [
                            {
                                xtype: 'combobox',
                                id:   'cmb_protocolo',
                                name: 'cmb_protocolo',
                                displayField: "descripcion",
                                valueField: "descripcion",
                                fieldLabel: "Protocolo",
                                queryMode:'local',
                                emptyText: 'Seleccione',
                                store:storeProtocolosEnrutamiento  
                            },{
                                xtype: boolHideNumberTrigger?'textfield':'numberfield',
                                fieldLabel: 'As Privado',
                                name: 'as_privado',
                                id: 'as_privado',
                                value:'',
                                maskRe: /([0-9]+)/i,
                                hideTrigger: boolHideNumberTrigger
                            }, {
                                xtype: 'checkboxfield',
                                fieldLabel: 'Configurar en el PE',
                                name: 'chkConfigurarPE',
                                id: 'chkConfigurarPE',
                                checked: true,
                                inputValue: '1'
                            }
                        ]
                    }

                ]
            }
            ],
            buttons: [
                {
                    text: 'Agregar',
                    formBind: true,
                    handler: function(){
                        var expRegNumero = /^[0-9]{1,5}$/;
                        
                        var protocolo     = Ext.getCmp('cmb_protocolo').getValue();
                        var as_privado    = Ext.getCmp('as_privado').getValue();
                        var configurar_PE = Ext.getCmp('chkConfigurarPE').getValue();
                        
                        if(protocolo==null)
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Por favor seleccione un Protocolo',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            }); 
                            return;
                        }  
                        
                        if(Ext.isEmpty(as_privado))
                        {
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: 'Por favor ingrese un As Privado',
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.ERROR
                            }); 
                            return;
                        }
                        
                        Ext.Msg.confirm({
                            title:'Confirmar',
                            msg: 'Esta seguro de agregar Protocolo de Enrutamiento?',
                            buttons: Ext.Msg.YESNO,
                            icon: Ext.MessageBox.QUESTION,
                            buttonText: {
                                yes: 'si', no: 'no'
                            },
                            fn: function(btn){
                                if(btn=='yes'){
                                    Ext.MessageBox.wait('Creando Protocolo de Enrutamiento...');
                                    
                                    Ext.Ajax.request({
                                        url: urlCrearProtocoloEnrutamiento,
                                        method: 'post',
                                        timeout: 900000,
                                        params: { 
                                            idServicio:             data.idServicio,
                                            idProducto:             data.productoId,
                                            idPersonaEmpresaRol:    data.idPersonaEmpresaRol,
                                            nombreTecnico:          data.descripcionProducto,
                                            idElemento:             data.elementoId,
                                            vrf:                    data.vrf,
                                            sw:                     data.elementoNombre,
                                            pe:                     data.elementoPadre,
                                            vlan:                   data.vlan,
                                            gateway:                data.gwSubredServicio,
                                            ip:                     data.ipServicio,
                                            loginAux:               data.loginAux,
                                            tipoEnlace:             data.tipoEnlace,
                                            protocolo:              protocolo,
                                            asPrivado:              as_privado,
                                            configurarPE:           configurar_PE
                                        },
                                        success: function(response){
                                            Ext.MessageBox.hide();
                                            win.hide();
                                            
                                            Ext.Msg.alert('Mensaje',response.responseText, function(btn){
                                                if(btn=='ok' || btn=='cancel')
                                                {
                                                    //Si el servicio es L3MPLS seguir mostrando ya que puede crearse otro protocolo
                                                    //Ocultar para INTERNET dado que solo requiere creacion de BGP una sóla vez
                                                    if(!boolHideNumberTrigger)
                                                    {
                                                        win.show();
                                                    }
                                                    else
                                                    {
                                                        win.destroy();
                                                    }
                                                    
                                                    storeProtocolosEnrutamientoServicio.load();
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
            title: titulo,
            modal: true,
            closable: true,
            layout: 'fit',
            width: 510,
            items: [formPanel]
        }).show();
        
        win.center();
    }
}