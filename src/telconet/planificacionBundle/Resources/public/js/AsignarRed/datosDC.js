
var idPersonaEmpresaRol = 0;
var idPe                = 0;
var storeVlansLan       = null;
var storeVlansWan       = null;
var json                = {};

function showRecursosRedDatosDC(data)
{
    idPersonaEmpresaRol = data.get('id_persona_empresa_rol');
    
    Ext.get(grid.getId()).mask('Consultando Datos...');

    Ext.Ajax.request({ 
        url: getDatosFactibilidad,
        method: 'post',
        timeout: 400000,
        params: { 
            idServicio   : data.get('id_servicio'),                
            tipoSolicitud: data.get('descripcionSolicitud'),
            idSolicitud  : data.get('id_factibilidad'),
            esPseudoPe   : '',
            ultimaMilla  : data.get('ultimaMilla'),
            esDataCenter : 'SI',
            ciudad       : data.get('ciudad').toUpperCase()
        },
        success: function(response)
        {
            Ext.get(grid.getId()).unmask();

            json = Ext.JSON.decode(response.responseText);

            if(json.status==="OK")
            {
               idPe = json.idElementoPadre;
               
               storeVlansLan = new Ext.data.Store({
                    total: 'total',                    
                    proxy: {
                        type: 'ajax',
                        method: 'post',
                        url: urlAjaxGetVlansDisponibles,
                        timeout: 400000,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'data'
                        },
                        actionMethods: {
                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                        },
                        extraParams: 
                        {
                            idPersonaEmpresaRol   : idPersonaEmpresaRol,
                            nombreElemento        : json.nombreElementoPadre,
                            tipoVlan              : 'LAN'
                        }
                    },
                    fields:
                        [
                            {name: 'id'  , mapping: 'id'},
                            {name: 'vlan', mapping: 'vlan'}
                        ]
                });  
                
                storeVlansWan = new Ext.data.Store({
                    total: 'total',                    
                    proxy: {
                        type: 'ajax',
                        method: 'POST',
                        url: urlAjaxGetVlansDisponibles,
                        timeout: 400000,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'data'
                        },
                        actionMethods: {
                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                        },
                        extraParams: 
                        {
                            idPersonaEmpresaRol   : idPersonaEmpresaRol,
                            nombreElemento        : json.nombreElementoPadre,
                            tipoVlan              : 'WAN'
                        }
                    },
                    fields:
                        [
                            {name: 'id'  , mapping: 'id'},
                            {name: 'vlan', mapping: 'vlan'}
                        ]
                }); 
                
                var storeSubredesExistentes = new Ext.data.Store({
                    total: 'total',                    
                    proxy: {
                        type: 'ajax',
                        method: 'POST',
                        url: urlAjaxGetSubredesDisponiblesIntDc,
                        timeout: 400000,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'data'
                        },
                        extraParams: 
                        {
                            idPersonaRol   : idPersonaEmpresaRol,
                            uso            : 'RUTASINTERNETDC'
                        }
                    },
                    fields:
                        [
                            {name: 'idSubred'  , mapping: 'idSubred'},
                            {name: 'subred'    , mapping: 'subred'}
                        ]
                }); 
                
                var storeMascaras = new Ext.data.Store({
                    fields: ['display','value'],
                    data: [                        
                        {
                            "display": "/28",
                            "value": "255.255.255.240"
                        },
                        {
                            "display": "/29",
                            "value": "255.255.255.248"
                        }
                    ]
                });
                
                Ext.define('serviciosRelacionadosModel', {
                    extend: 'Ext.data.Model',
                    fields: [
                        {name: 'identificador', type: 'string'},                        
                        {name: 'servicio',    type: 'string'},  
                        {name: 'tipoSolucion',type: 'string'},
                        {name: 'nombreTecnico',type: 'string'}
                    ]
                });    
                
                var storeServiciosRelacionados = new Ext.data.Store({
                    pageSize: 5,
                    autoDestroy: true,
                    model: 'serviciosRelacionadosModel',
                    proxy: {
                        type: 'memory'
                    }
                });
                
                $.each(json.arrayServiciosRelacionados,function(i , item)
                {
                    var recordParamDet = Ext.create('serviciosRelacionadosModel', {
                        identificador: item.identificador,                        
                        servicio     : item.servicio,
                        tipoSolucion : item.tipoSolucion,
                        nombreTecnico: item.nombreTecnico
                    });

                    storeServiciosRelacionados.insert(i, recordParamDet);
                });
                
                var storeSubredesPublicasCompartidas = new Ext.data.Store({
                    fields: ['idSubred','subred'],
                    data: json.arraySubredesPublicasCompartidas
                });
                
                var storeFirewallsDC = new Ext.data.Store({
                    fields: ['id','valor2'],
                    data: json.arrayFirewallsDC
                });
                
                var htmlButtonVlanLan = '<div class="content-vlans" id="content-vlan-lan" onclick="reservarVlans(\'lan\',\'dedicado\')"\n\
                                              style="cursor:pointer;" title="Resevar VLAN LAN">\n\
                                              <i class="fa fa-toggle-off fa-2x" aria-hidden="true"></i>\n\
                                        </div>';
                
                var htmlButtonVlanWan = '<div class="content-vlans" id="content-vlan-wan" onclick="reservarVlans(\'wan\',\'dedicado\')" \n\
                                              style="cursor:pointer;" title="Resevar VLAN WAN">\n\
                                              <i class="fa fa-toggle-off fa-2x" aria-hidden="true"></i>\n\
                                         </div>';
                
                var objHtmlButtonLan = Ext.create('Ext.Component', {
                    html: htmlButtonVlanLan,
                    padding: 1,
                    layout: 'anchor'
                });
                
                var objHtmlButtonWan = Ext.create('Ext.Component', {
                    html: htmlButtonVlanWan,
                    padding: 1,
                    layout: 'anchor'
                });
                
                //Store de VRFs
                storeVrfsDisponibles = new Ext.data.Store({
                    total: 'total',                    
                    timeout:600000,
                    listeners: {
                        load: function(store,records,options) {
                            if(store.totalCount===0)
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: "No tiene asignado Vrfs para el servicio [ " + json.nombreElementoPadre + " ]",
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR,
                                    fn: function(btn){
                                        win.destroy();
                                    }
                                });
                            }
                        }
                    },
                    proxy: {
                        type: 'ajax',
                        timeout: 600000,
                        url: urlAjaxGetVrfsDisponibles,
                        extraParams: {
                            idPersonaEmpresaRol : data.get('id_persona_empresa_rol'),
                            idServicio:           data.get('id_servicio')
                        },
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'data'
                        },
                        actionMethods: {
                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                        }
                    },
                    fields:
                        [
                            {name: 'id_vrf' , mapping: 'id_vrf'},
                            {name: 'vrf', mapping: 'vrf'}
                        ]
                });
                
                //Store Protocolos
                storeProtocolosEnrutamiento = new Ext.data.Store({
                    proxy: {
                        type: 'ajax',
                        url: urlAjaxGetProtocolosEnrutamiento,
                        timeout: 600000,
                        reader: {
                            type: 'json',
                            root: 'data'
                        },                            
                    },
                    fields:
                        [
                            {name: 'descripcion', mapping: 'descripcion'}
                        ]
                });
                
                //Subredes Existentes
                storeSubredesL3mplsDisponibles = new Ext.data.Store({
                    total   :'total',
                    timeout :600000,
                    listeners: {
                        load: function(store,records,options) {
                            if(store.totalCount===0)
                            {
                                Ext.getCmp('rbRecursosExistentes').setDisabled(true);
                                Ext.getCmp('rbRecursosNuevos').checked = true;
                            }
                        }
                    },
                    proxy: {
                        type: 'ajax',
                        url: urlAjaxGetSubredesL3mplsDisponibles,
                        timeout: 600000,
                        reader: {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'data'
                        },
                        actionMethods: {
                            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                        },
                        extraParams: {
                            nombreElemento      : json.nombreElementoPadre,
                            idPersonaEmpresaRol : data.get('id_persona_empresa_rol'),
                            nombreTecnico       : data.get('nombreTecnico'),
                            tipoEnlace          : data.get('tipo_enlace'),
                            anillo              : '',
                            idServicio          :data.get('id_servicio')
                        }
                    },
                    fields:
                        [
                            {name: 'id'  ,       mapping: 'id'},
                            {name: 'subred',     mapping: 'subred'},
                            {name: 'idServicio', mapping: 'idServicio'}
                        ]
                });             
                
                //Cargar informacion de AsPrivado
                Ext.Ajax.request({
                    url: urlAjaxGetAsPrivado,
                    timeout: 600000,
                    method: 'post',
                    params: { idPersonaEmpresaRol : data.get('id_persona_empresa_rol') },
                    success: function(response){
                        var asPrivado = response.responseText;

                        Ext.getCmp('asPrivado').setDisabled(true);

                        if(asPrivado>0)
                        {
                            Ext.getCmp('asPrivado').setValue(asPrivado);
                            Ext.getCmp('asPrivado').setReadOnly(true);
                        }
                        else
                        {
                            Ext.getCmp('asPrivado').setReadOnly(false);
                        }
                    },
                    failure: function(response)
                    {
                        Ext.getCmp('asPrivado').setVisible(false);

                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: response.responseText,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        }); 
                    }
                });
                
                //Grid servicios relacionados
                var gridRelacionados = Ext.create('Ext.grid.Panel', {
                    width: 750,
                    id:'gridServiciosRelacionados',
                    height: 110,                    
                    store: storeServiciosRelacionados,
                    loadMask: true,
                    frame: false,
                    columns: [    
                        {
                            id: 'identificador',
                            header: '',
                            dataIndex: 'identificador',
                            width: 80,
                            sortable: true,
                            align:'center'
                        },
                        {
                            id: 'servicio',
                            header: 'Servicio',
                            dataIndex: 'servicio',
                            width: 300,
                            sortable: true
                        },
                        {
                            id: 'tipoSolucion',
                            header: 'Tipo de Sub Solución',
                            dataIndex: 'tipoSolucion',
                            width: 200,
                            sortable: true
                        },
                        {
                            id: 'nombreTecnicoSubSol',
                            header: 'Descripción',
                            dataIndex: 'nombreTecnico',
                            width: 150,
                            sortable: true
                        }
                    ]
                });

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
                        columns: 2
                    },
                    defaults: {
                        bodyStyle: 'padding:20px'
                    },
                    items: [
                        //informacion del servicio
                        {
                            colspan: 1,
                            rowspan: 2,
                            xtype: 'panel',
                            title: 'Información del Servicio',
                            defaults: { 
                                height: 75
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

                                        {   width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoOrden',
                                            fieldLabel: 'Tipo Orden',
                                            displayField: data.get("tipo_orden"),
                                            value: data.get("tipo_orden"),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'producto',
                                            fieldLabel: 'Producto',
                                            displayField: data.get("producto"),
                                            value: data.get("producto"),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '10%', border: false},
                                        //---------------------------------------------

                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad1',
                                            fieldLabel: 'Capacidad1',
                                            displayField: json.capacidad1,
                                            value: json.capacidad1,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'capacidad2',
                                            fieldLabel: 'Capacidad2',
                                            displayField: json.capacidad2,
                                            value: json.capacidad2,
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '10%', border: false},
                                        //---------------------------------------------

                                        {width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'tipoEnlace',
                                            fieldLabel: 'Tipo Enlace',
                                            displayField: data.get('tipo_enlace'),
                                            value: data.get('tipo_enlace'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        {width: '15%', border: false},
                                        {width: '15%', border: false},
                                        {width: '10%', border: false}
                                    ]
                                }
                            ]
                        },//cierre de la informacion del cliente
                        //Información del concentrador
                        {
                            colspan: 1,
                            rowspan: 2,
                            title: 'Información del Cliente',
                            xtype: 'panel',
                            defaults: { 
                                height: 75
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
                                            name: 'cliente',
                                            fieldLabel: 'Cliente',
                                            displayField: data.get('cliente'),
                                            value: data.get('cliente'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'login',
                                            fieldLabel: 'Login',
                                            displayField: data.get('login2'),
                                            value: data.get('login2'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'ciudad',
                                            fieldLabel: 'Ciudad',
                                            displayField: data.get('ciudad'),
                                            value: data.get('ciudad'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'direccion',
                                            fieldLabel: 'Direccion',
                                            displayField: data.get('direccion'),
                                            value: data.get('direccion'),
                                            readOnly: true,
                                            width: '30%'
                                        },
                                        { width: '10%', border: false},

                                        //---------------------------------------------

                                        { width: '10%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'sector',
                                            fieldLabel: 'Sector',
                                            displayField: data.get('nombreSector'),
                                            value: data.get('nombreSector'),
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '15%', border: false},
                                        {
                                            xtype: 'textfield',
                                            name: 'esRecontratacion',
                                            fieldLabel: 'Es Recontratacion',
                                            displayField: data.get("esRecontratacion"),
                                            value: data.get("esRecontratacion"),
                                            readOnly: true,
                                            width: '35%'
                                        },
                                        { width: '10%', border: false}
                                    ]
                                }
                            ]                               
                        },// cierre de información del concentrador  
                        //Informacion de relacion del enlace con las subsoluciones
                        {
                            colspan: 2,
                            xtype: 'panel',
                            collapsible: true,
                            collapsed: true,
                            id:'solucionesAsociadas',
                            title: 'Soluciones asociadas al Enlace',
                            defaults: {
                                bodyStyle: 'align:center;'
                            },
                            items: 
                            [
                                gridRelacionados
                            ]
                        },                    
                        //informacion de los elementos del cliente
                        {
                            colspan: 2,
                            xtype: 'panel',
                            title: 'Información de Backbone',
                            items: [
                                //grupo de radio botones
                                {
                                    xtype: 'radiogroup',
                                    fieldLabel: '<b>Recursos</b>',
                                    columns: 1,
                                    items: [
                                        {
                                            boxLabel: 'Nuevos', 
                                            id: 'rbRecursosNuevos', 
                                            name: 'rbRecursos', 
                                            inputValue: "nuevo", 
                                            listeners: 
                                            {
                                                change: function (cb, nv, ov) 
                                                {
                                                    if (nv)
                                                    {                                                        
                                                        Ext.getCmp('containerServicioDedicado').setVisible(true);
                                                        Ext.getCmp('containerServicioCompartido').setVisible(false);
                                                        win.center();
                                                        
                                                        //Stores
                                                        storeProtocolosEnrutamiento.load({});                                                  
                                                    }
                                                }
                                            }
                                        },

                                        {
                                            boxLabel: 'Existentes', 
                                            id: 'rbRecursosExistentes', 
                                            name: 'rbRecursos', 
                                            inputValue: "existente",
                                            listeners: 
                                            {
                                                change: function (cb, nv, ov) 
                                                {
                                                    if (nv)
                                                    {                                                        
                                                        Ext.getCmp('containerServicioDedicado').setVisible(false);
                                                        Ext.getCmp('containerServicioCompartido').setVisible(true);
                                                        win.center();
                                                    }
                                                }
                                            }
                                        }
                                    ]//items
                                },
                                
                                //Datos de Factibilidad
                                {
                                    id: 'containerRecursosObligatorios',
                                    xtype: 'fieldset',
                                    title: '<i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp;<b>Datos Factibilidad</b>',
                                    defaultType: 'textfield',
                                    items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 8,
                                            align: 'stretch'
                                        },
                                        items: [
                                            {
                                                xtype:          'textfield',
                                                id:             'nombrePe',
                                                name:           'nombrePe',
                                                fieldLabel:     '<i class="fa fa-server" aria-hidden="true"></i>&nbsp;<b>Pe</b>',
                                                readOnly:       true,
                                                displayField:   json.nombreElementoPadre,
                                                value:          json.nombreElementoPadre,
                                                width:          300
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'textfield',
                                                id:             'nombreSwitch',
                                                name:           'nombreSwitch',
                                                fieldLabel:     '<i class="fa fa-server" aria-hidden="true"></i>&nbsp;<b>Switch</b>',
                                                readOnly:       true,
                                                displayField:   json.nombreElemento,
                                                value:          json.nombreElemento,
                                                width:          300
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'textfield',
                                                id:             'nombreInterface',
                                                name:           'nombreInterface',
                                                fieldLabel:     '<i class="fa fa-plug" aria-hidden="true"></i>&nbsp;<b>Puerto</b>',
                                                readOnly:       true,
                                                displayField:   json.nombreInterfaceElemento,
                                                value:          json.nombreInterfaceElemento,
                                                width:          300
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   }
                                            //---------------------------------------
                                        ]
                                    }]
                                },                                    

                                //containerNuevosDatos
                                {
                                    id: 'containerServicioDedicado',
                                    xtype: 'fieldset',
                                    title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Recursos Nuevos</b>',
                                    defaultType: 'textfield',
                                    items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 6,
                                            align: 'stretch'
                                        },
                                        items: [
                                            //---------------------------------------
                                            {
                                                xtype:           'combobox',
                                                name:            'cmbFirewallsDC',
                                                id:              'cmbFirewallsDC',
                                                fieldLabel:      '<i class="fa fa-th" aria-hidden="true"></i>&nbsp;<b>Firewall DC</b>',
                                                displayField:    'valor2',                                                
                                                valueField:      'valor2',
                                                store:           storeFirewallsDC,    
                                                editable:        false,
                                                width:           250
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                queryMode:      'local',
                                                xtype:          'textfield',
                                                id:             'redPrivadaDedicado',
                                                name:           'redPrivadaDedicado',
                                                emptyText:      'XXX.XXX.XXX.XXX/XX',
                                                fieldLabel:     '<i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<b>Subred Lan (Cliente)</b>',
                                                width:          250
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //--------------------------------------
                                            {
                                                xtype:           'combobox',
                                                name:            'cmbVlanLanDedicado',
                                                id:              'cmbVlanLanDedicado',
                                                fieldLabel:      '<i class="fa fa-gg" aria-hidden="true"></i>&nbsp;<b>Vlan LAN</b>',
                                                displayField:    'vlan',                                                
                                                valueField:      'id',
                                                store:           storeVlansLan,    
                                                editable:        false,
                                                width:           250
                                            },
                                            {   width: 25,border: false   },
                                            objHtmlButtonLan,
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //--------------------------------------
                                            {
                                                xtype:           'combobox',
                                                name:            'cmbVlanWanDedicado',
                                                id:              'cmbVlanWanDedicado',
                                                fieldLabel:      '<i class="fa fa-gg" aria-hidden="true"></i>&nbsp;<b>Vlan WAN</b>',
                                                displayField:    'vlan',                                                
                                                valueField:      'id',
                                                store:           storeVlansWan,    
                                                editable:        false,
                                                width:           250
                                            },
                                            {   width: 25,border: false   },
                                            objHtmlButtonWan,
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //---------------------------------------
                                            {
                                                queryMode:      'local',
                                                xtype:          'combobox',
                                                id:             'cmbVrfsDisponibles',
                                                name:           'cmbVrfsDisponibles',
                                                fieldLabel:     '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Vrf</b>',
                                                displayField:   'vrf',
                                                valueField:     'id_vrf',
                                                store:          storeVrfsDisponibles,
                                                width:          250
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:           'combobox',
                                                name:            'cmbMascara',
                                                id:              'cmbMascara',
                                                fieldLabel:      '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Máscara</b>',
                                                displayField:    'display',                                                
                                                valueField:      'value',
                                                store:           storeMascaras,    
                                                editable:        false,
                                                width:           200                                                
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //---------------------------------------
                                            {
                                                queryMode:      'local',
                                                xtype:          'combobox',
                                                id:             'cmbProtocolosEnrutamiento',
                                                name:           'cmbProtocolosEnrutamiento',
                                                fieldLabel:     '<b>Protocolo</b>',
                                                displayField:   'descripcion',
                                                valueField:     'descripcion',
                                                store:          storeProtocolosEnrutamiento,
                                                width:          250,
                                                listeners: 
                                                {
                                                    select: function(combo)
                                                    {
                                                        //asprivado
                                                        if(combo.getValue()!=="STANDARD")
                                                        {
                                                            Ext.getCmp('asPrivado').setDisabled(false);
                                                        }
                                                        else
                                                        {
                                                            Ext.getCmp('asPrivado').setDisabled(true);
                                                        }    
                                                        
                                                        //defaultgateway
                                                        if(combo.getValue()==="BGP")
                                                        {
                                                            Ext.getCmp('defaultGateway').setDisabled(false);
                                                        }
                                                        else
                                                        {
                                                            Ext.getCmp('defaultGateway').setDisabled(true);
                                                        }
                                                    }
                                                }
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'numberfield',
                                                id:             'asPrivado',
                                                name:           'asPrivado',
                                                fieldLabel:     '<b>As Privado</b>',
                                                allowDecimals:   false,
                                                allowNegative:   false,
                                                maxLength:       8,
                                                width:           200
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //----------------------------------
                                            {
                                                xtype: 'checkboxfield',
                                                id: 'defaultGateway',
                                                name: 'defaultGateway',
                                                boxLabel: 'Neighbor Default Gateway'
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   }
                                        ]//items fielset
                                    }]
                                },
                                
                                //container Mismos recursos
                                {
                                    id: 'containerServicioCompartido',
                                    xtype: 'fieldset',
                                    title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Recursos Existentes</b>',
                                    defaultType: 'textfield',
                                    items: [
                                    {
                                        xtype: 'container',
                                        layout: {
                                            type: 'table',
                                            columns: 6,
                                            align: 'stretch'
                                        },
                                        items: 
                                        [
                                            //---------------------------------------
                                            {
                                                xtype:           'combobox',
                                                name:            'cmbFirewallsDCExistentes',
                                                id:              'cmbFirewallsDCExistentes',
                                                fieldLabel:      '<i class="fa fa-th" aria-hidden="true"></i>&nbsp;<b>Firewall DC</b>',
                                                displayField:    'valor2',                                                
                                                valueField:      'valor2',
//                                                allowBlank:      false,
//                                                forceSelection:  true,
                                                store:           storeFirewallsDC,    
                                                editable:        false,
                                                width:           270
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                queryMode:      'local',
                                                xtype:          'textfield',
                                                id:             'redPrivadaDedicadoExistentes',
                                                name:           'redPrivadaDedicadoExistentes',
                                                emptyText:      'XXX.XXX.XXX.XXX/XX',
                                                fieldLabel:     '<i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<b>Subred Lan (Cliente)</b>',
                                                width:          270
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //-------------------------------------------------------------
                                            {
                                                queryMode:      'local',
                                                xtype:          'combobox',
                                                id:             'cmbSubredesDisponibles',
                                                name:           'cmbSubredesDisponibles',
                                                fieldLabel:     '<i class="fa fa-link" aria-hidden="true"></i>&nbsp;<b>Subred</b>',
                                                displayField:   'subred',
                                                valueField:     'id',
                                                store:           storeSubredesL3mplsDisponibles,
                                                width:           270,
                                                listeners: {
                                                    select: function(combo)
                                                    {
                                                        Ext.get("containerServicioCompartido").mask('Cargando datos de Factibilidad...');
                                                        var raw = combo.valueModels[0].raw;
                                                        Ext.Ajax.request({
                                                            url: urlAjaxGetInfoBackboneSubredL3mpls,
                                                            timeout: 600000,
                                                            method: 'post',
                                                            params: { idServicio : raw.idServicio },
                                                            success: function(response)
                                                            {
                                                                Ext.get("containerServicioCompartido").unmask();
                                                                var jsonDatosSubred = Ext.JSON.decode(response.responseText);

                                                                Ext.getCmp('mismaVlanLan').setValue(jsonDatosSubred.vlanLan);
                                                                Ext.getCmp('mismaVlanWan').setValue(jsonDatosSubred.vlanWan);
                                                                Ext.getCmp('mismaVrf').setValue(jsonDatosSubred.vrf);
                                                                Ext.getCmp('mismoProtocolo').setValue(jsonDatosSubred.protocolos);

                                                                if(jsonDatosSubred.protocolos !== "STANDARD")
                                                                {
                                                                    Ext.getCmp('mismoAsPrivado').setValue(jsonDatosSubred.asPrivado);
                                                                    Ext.getCmp('mismoAsPrivado').setDisabled(false);
                                                                }
                                                                
                                                                Ext.getCmp('cmbVlanLanDedicado').setRawValue(jsonDatosSubred.idVlanLan);
                                                                Ext.getCmp('cmbVlanWanDedicado').setRawValue(jsonDatosSubred.idVlanWan);
                                                                Ext.getCmp('cmbVrfsDisponibles').setRawValue(jsonDatosSubred.idVrf);
                                                                Ext.getCmp('cmbProtocolosEnrutamiento').setValue(jsonDatosSubred.protocolos);

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
                                                    }
                                                }
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //-------------------------------------                                            
                                            {
                                                xtype:          'textfield',
                                                id:             'mismaVrf',
                                                name:           'mismaVrf',
                                                fieldLabel:     '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Vrf</b>',
                                                readOnly:       true,
                                                width:          270
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //---------------------------------------
                                            {
                                                xtype:          'textfield',
                                                id:             'mismaVlanLan',
                                                name:           'mismaVlanLan',
                                                fieldLabel:     '<i class="fa fa-gg" aria-hidden="true"></i>&nbsp;<b>Vlan LAN</b>',
                                                readOnly:       true,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //---------------------------------------
                                            {
                                                xtype:          'textfield',
                                                id:             'mismaVlanWan',
                                                name:           'mismaVlanWan',
                                                fieldLabel:     '<i class="fa fa-gg" aria-hidden="true"></i>&nbsp;<b>Vlan WAN</b>',
                                                readOnly:       true,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            //---------------------------------------
                                            {
                                                xtype:          'textfield',
                                                id:             'mismoProtocolo',
                                                name:           'mismoProtocolo',
                                                fieldLabel:     '<b>Protocolo</b>',
                                                readOnly:       true,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {
                                                xtype:          'textfield',
                                                id:             'mismoAsPrivado',                                                
                                                name:           'mismoAsPrivado',
                                                fieldLabel:     '<b>As Privado</b>',
                                                readOnly:       true,
                                                width:          180
                                            },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   },
                                            {   width: 25,border: false   }
                                        ]
                                    }]
                                }//containerMismosDatos
                                ]
                        }//cierre informacion de los elementos del cliente
                    ],
                    buttons: 
                    [{
                        text: 'Guardar',
                        handler: function()
                        {
                            var ultimaMilla  = data.get('ultimaMilla');             
                            var flagRecursos = Ext.ComponentQuery.query('[name=rbRecursos]')[0].getGroupValue();
                            var boolContinua = true;
                            
                            //Obtener Datos Recursos Nuevos
                            var firewallDC         = Ext.getCmp('cmbFirewallsDC').getValue();
                            var firewallDCExist    = Ext.getCmp('cmbFirewallsDCExistentes').getValue();
                            var vlanLanDedicado    = Ext.getCmp('cmbVlanLanDedicado').getValue();
                            var vlanWanDedicado    = Ext.getCmp('cmbVlanWanDedicado').getValue();
                            var redPrivadaDedicado = Ext.getCmp('redPrivadaDedicado').getValue();
                            var redPrivadaDedExist = Ext.getCmp('redPrivadaDedicadoExistentes').getValue();
                            //--
                            var vrfDisponibles     = Ext.getCmp('cmbVrfsDisponibles').getValue();
                            var mascaraPublica     = Ext.getCmp('cmbMascara').getValue();
                            var protocolo          = Ext.getCmp('cmbProtocolosEnrutamiento').getValue();
                            var asPrivado          = Ext.getCmp('asPrivado').getValue();                            
                            var defaultGateway     = Ext.getCmp('defaultGateway').getValue();
                            
                            //Obtener Datos Existentes
                            var subredesExistentes = Ext.getCmp('cmbSubredesDisponibles').getValue();
                            
                            var mensajeAlerta = '';
                            
                            //Validacion de valores de recursos nuevos
                            if(flagRecursos === 'nuevo')
                            {
                                if (Ext.isEmpty(mascaraPublica))//mascara
                                {
                                    mensajeAlerta = 'Por favor escoja la Máscara para la Subred Pública';
                                    boolContinua  = false;
                                }
                                else if(Ext.isEmpty(firewallDC))//firewall
                                {
                                    mensajeAlerta = 'Debe agregar la información del Firewall';
                                    boolContinua  = false;
                                }
                                else if(Ext.isEmpty(redPrivadaDedicado))//subred lan cliente
                                {
                                    mensajeAlerta = 'Debe agregar la información de la Red Privada';
                                    boolContinua  = false;
                                }
                                else if(Ext.isEmpty(vlanWanDedicado))//vlan wan
                                {
                                    mensajeAlerta = 'Debe agregar la información de la Vlan Wan';
                                    boolContinua  = false;
                                }
                                else if(Ext.isEmpty(vrfDisponibles))//vrf
                                {
                                    mensajeAlerta = 'Debe agregar la información de la VRF';
                                    boolContinua  = false;
                                }
                                else if(Ext.isEmpty(protocolo))//protocolo
                                {
                                    mensajeAlerta = 'Debe agregar la información del Protocolo';
                                    boolContinua  = false;
                                }
                            }
                            else//compartido
                            {
                                if(Ext.isEmpty(subredesExistentes))
                                {
                                    mensajeAlerta = 'Debe Escoger la subred existente';
                                    boolContinua  = false;
                                }
                                else if(Ext.isEmpty(firewallDCExist))//firewall
                                {
                                    mensajeAlerta = 'Debe agregar la información del Firewall';
                                    boolContinua  = false;
                                }
                                else if(Ext.isEmpty(redPrivadaDedExist))//subred lan cliente
                                {
                                    mensajeAlerta = 'Debe agregar la información de la Red Privada';
                                    boolContinua  = false;
                                }
                            }
                            
                            if(!boolContinua)
                            {
                                Ext.MessageBox.show({
                                    title  : 'Error',
                                    msg    : mensajeAlerta,
                                    buttons: Ext.MessageBox.OK,
                                    icon   : Ext.MessageBox.ERROR
                                }); 
                                return false;
                            }
                            else
                            {
                                var jsonRecursos = {};
                                var arrayJson    = [];
                                
                                var vlanLan = flagRecursos === 'nuevo'?Ext.getCmp('cmbVlanLanDedicado').getRawValue():
                                                                       Ext.getCmp('mismaVlanLan').getValue();
                                                                   
                                var vlanWan = flagRecursos === 'nuevo'?Ext.getCmp('cmbVlanWanDedicado').getRawValue():
                                                                       Ext.getCmp('mismaVlanWan').getValue();
                                
                                jsonRecursos['firewallDC']         = flagRecursos === 'nuevo'?firewallDC:firewallDCExist;
                                jsonRecursos['redPrivada']         = flagRecursos === 'nuevo'?redPrivadaDedicado:redPrivadaDedExist;
                                jsonRecursos['vlanLan']            = vlanLanDedicado;
                                jsonRecursos['vlanWan']            = vlanWanDedicado;
                                jsonRecursos['valorVlanLan']       = vlanLan;
                                jsonRecursos['valorVlanWan']       = vlanWan;
                                jsonRecursos['vrf']                = vrfDisponibles;
                                jsonRecursos['mascara']            = mascaraPublica;
                                jsonRecursos['protocolo']          = protocolo;
                                jsonRecursos['asPrivado']          = flagRecursos === 'nuevo'?asPrivado:Ext.getCmp('mismoAsPrivado').getValue();
                                jsonRecursos['idSubred']           = subredesExistentes;
                                jsonRecursos['defaultGateway']     = defaultGateway;
                                
                                arrayJson.push(jsonRecursos);
                                                                
                                guardarRecursosRedDatos(data,arrayJson,flagRecursos,ultimaMilla,win,formPanel);
                            }
                        }//handler
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                            win.destroy();
                        }
                    }]
                });
                
                Ext.getCmp('containerServicioDedicado').setVisible(false);
                Ext.getCmp('containerServicioCompartido').setVisible(false);
                Ext.getCmp('asPrivado').setDisabled(true);
                Ext.getCmp('mismoAsPrivado').setDisabled(true);
                Ext.getCmp('defaultGateway').setDisabled(true);
                
                storeSubredesL3mplsDisponibles.load({
                    callback: function() {
                        storeVrfsDisponibles.load({});
                    }
                });
                
                var win = Ext.create('Ext.window.Window', {
                    title: 'Asignar Recurso de Red',
                    id:'winRecursosRed',
                    modal: true,
                    width: 1100,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
            }
            else
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: json.msg,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });   
            }    
        },//cierre response
        failure: function(result) {
            Ext.MessageBox.show({
                title: 'Error',
                msg: result.responseText,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });   
}

function guardarRecursosRedDatos(data,arrayJsonDatos,flagRecursos,ultimaMilla,objWin,formPanel)
{
    Ext.Msg.confirm('Mensaje','Esta seguro de asignar los recursos de red ?', function(btn){
        if(btn==='yes')
        {
            Ext.get(formPanel.getId()).mask('Guardando datos...');
            
            Ext.Ajax.request({
                url: urlAjaxAsignarRedDatosDC,
                method: 'post',
                timeout: 1000000,
                params: 
                { 
                    idPersonaEmpresaRol:    data.get('id_persona_empresa_rol') ,
                    idServicio:             data.get('id_servicio'),
                    idDetalleSolicitud:     data.get('id_factibilidad'),
                    tipoSolicitud:          data.get('descripcionSolicitud'),
                    tipoRecursos:           flagRecursos,
                    ultimaMilla:            ultimaMilla,
                    jsonData:               Ext.JSON.encode(arrayJsonDatos),
                    idElementoPadre:        idPe,
                    ciudad:                 data.get('ciudad').toUpperCase(),
                    nombrePe:               json.nombreElementoPadre,
                    nombreSwitch:           json.nombreElemento,
                    nombrePuerto:           json.nombreInterfaceElemento,
                    capacidad1:             json.capacidad1,
                    capacidad2:             json.capacidad2,
                    tipoSolucion:           json.productoDCReferenteSubGrupo
                },
                success: function(response)
                {
                    Ext.get(formPanel.getId()).unmask();

                    var jsonGuardar = Ext.JSON.decode(response.responseText);

                    if(jsonGuardar.status === "OK")
                    {
                        Ext.Msg.alert('Mensaje','Se Asignaron los Recursos de Red', function(btn){
                            if(btn==='ok')
                            {
                                objWin.destroy();
                                store.load();
                            }
                        });
                    }
                    else
                    {
                        Ext.MessageBox.show({
                            title  : 'Error',
                            msg    : jsonGuardar.mensaje,
                            buttons: Ext.MessageBox.OK,
                            icon   : Ext.MessageBox.ERROR
                        });
                    }
                },
                failure: function(result)
                {
                    Ext.get(formPanel.getId()).unmask();
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: result.statusText,
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.ERROR
                    });

                }
            });
        }
    });
}

function getInformacionRecursosRedPorTipo(tipo)
{
    var flagRecursos = Ext.ComponentQuery.query('[name=rbRecursos]')[0].getGroupValue();
    
    if(flagRecursos === 'dedicado')
    {
        Ext.getCmp('vlanLanDedicado').setDisabled(false);
        Ext.getCmp('vlanWanDedicado').setDisabled(false);        
    }
    else
    {
        Ext.getCmp('vlanLanDedicado').setDisabled(true);
        Ext.getCmp('vlanWanDedicado').setDisabled(true);        
    }
    
    if(tipo === 'N')
    {
        $(".content-vlans").show();
        $("#content-vlan-lan").find("i").remove();
        $("#content-vlan-wan").find("i").remove();
        $("#content-vlan-lan").prepend('<i class="fa fa-toggle-off fa-2x" aria-hidden="true"></i>');
        $("#content-vlan-wan").prepend('<i class="fa fa-toggle-off fa-2x" aria-hidden="true"></i>');
    }
    else
    {
        $(".content-vlans").hide();
    }    
}

function reservarVlans(tipo,recurso)
{
    var content = '';
    var content = '';
    
    if(tipo === 'lan')
    {
        content  = 'content-vlan-lan';
        
        if(recurso === 'compartido')
        {
            content = 'content-vlan-lan-c';
        }
    }
    else
    {
        content = 'content-vlan-wan';
    }
    
    $("#"+content).find("i").remove();
    $("#"+content).prepend('<i class="fa fa-toggle-on fa-2x" aria-hidden="true"></i>');
    
    Ext.get(Ext.get('winRecursosRed')).mask('Consultando Información para reserva de Vlan...');
    
    Ext.Ajax.request({
        url: urlAjaxGetInformacionVlansDC,
        method: 'post',
        params: {
            tipoVlan       : tipo.toUpperCase()
        },
        success: function(response) 
        {
            Ext.get(Ext.get('winRecursosRed')).unmask();
            
            var json = Ext.decode(response.responseText);
            
            var min = json.minRango;
            var max = json.maxRango;

            var formPanelReservaLans = Ext.create('Ext.form.Panel', {
                buttonAlign: 'center',
                BodyPadding: 10,
                bodyStyle: "background: white; padding: 5px; border: 0px none;",
                frame: true,
                items: [
                    {
                        xtype: 'fieldset',
                        title: '',
                        layout: {
                            tdAttrs: {style: 'padding: 5px;'},
                            type: 'table',
                            columns: 2,
                            pack: 'center'
                        },
                        items: [
                            {
                                xtype: 'fieldset',
                                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Información Vlan</b>',
                                defaultType: 'textfield',
                                style: "font-weight:bold; margin-bottom: 5px;",
                                layout: 'anchor',
                                defaults: {
                                    width: '350px'
                                },
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: '<b>Tipo Vlan a reservar</b>',
                                        value: tipo.toUpperCase(),
                                        width: 250,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: '<b>Rango Definido</b>',
                                        value: min + "-" + max,
                                        width: 250,
                                        readOnly: true
                                    },
                                    {
                                        xtype: 'numberfield',
                                        id: 'vlanSugerida',
                                        name: 'vlanSugerida',
                                        fieldLabel: '<b>Sugerir Vlan</b>',
                                        minValue: min,
                                        maxValue:max,
                                        width: 250
                                    }
                                ]
                            }
                        ]
                    }
                ],
                buttons: [
                    {
                        text: '<i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;<b>Guardar/Sugerir Vlan</b>',
                        handler: function()
                        {
                            var vlan = Ext.getCmp('vlanSugerida').getValue();
                            
                            if(!Ext.isEmpty(vlan) && (vlan < min || vlan > max))
                            {
                                Ext.Msg.alert('Alerta','La Vlan ingresada no se encuentra dentro del Rango permitido');
                            }
                            else
                            {
                                Ext.get(Ext.get('winReservaVlans')).mask('Reservando Vlan en el Cliente...');
                                
                                Ext.Ajax.request({
                                    url: urlAjaxGuardarVlanDC,
                                    method: 'post',
                                    params: 
                                    {
                                        tipoVlan            : tipo.toUpperCase(),
                                        vlanSugerida        : vlan,
                                        idPersonaEmpresaRol : idPersonaEmpresaRol,
                                        idPe                : idPe
                                    },
                                    success: function(response) 
                                    {
                                        Ext.get(Ext.get('winReservaVlans')).unmask();

                                        var json = Ext.decode(response.responseText);
                                        
                                        Ext.Msg.alert('Mensaje', json.mensaje);
                                        
                                        if(tipo.toUpperCase() === 'LAN')
                                        {
                                            storeVlansLan.load({params:{tipoVlan: 'LAN'}});
                                        }
                                        else
                                        {
                                            storeVlansWan.load({params:{tipoVlan: 'WAN'}});
                                        }
                                        
                                        winReservaVlans.close();
                                        winReservaVlans.destroy();
                                        $("#" + content).find("i").remove();
                                        $("#" + content).prepend('<i class="fa fa-toggle-off fa-2x" aria-hidden="true"></i>');
                                    },
                                    failure: function(result)
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });
                            }
                        }
                    },
                    {
                        text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;<b>Cerrar</b>',
                        handler: function()
                        {
                            winReservaVlans.close();
                            winReservaVlans.destroy();
                            $("#" + content).find("i").remove();
                            $("#" + content).prepend('<i class="fa fa-toggle-off fa-2x" aria-hidden="true"></i>');
                        }
                    }
                ]
            });

            var winReservaVlans = Ext.widget('window', {
                title: '<b>Reservación de VLANS</b>',
                layout: 'fit',
                id:'winReservaVlans',
                resizable: false,
                modal: true,
                closable: false,
                items: [formPanelReservaLans]
            });

            winReservaVlans.show();
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
}
