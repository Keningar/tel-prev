/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function() {  
    Ext.tip.QuickTipManager.init();                
    
    store = new Ext.data.Store({
        pageSize: 14,
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url: url_ajaxGrid,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            },
            extraParams: {
                fechaDesdePlanif: '',
                fechaHastaPlanif: '',
                fechaDesdeIngOrd: '',
                fechaHastaIngOrd: '',
                login2: '',
                descripcionPunto: '',
                vendedor: '',
                ciudad: '',
                numOrdenServicio: '',
                estado: 'Todos',
                ultimaMilla: '',
                id_jurisdiccion: '',
                limite:true
            }
        },
        fields:
            [
                {name: 'id_factibilidad', mapping: 'id_factibilidad'},
                {name: 'id_servicio', mapping: 'id_servicio'},
                {name: 'tipo_orden', mapping: 'tipo_orden'},
                {name: 'id_punto', mapping: 'id_punto'},
                {name: 'observacion', mapping: 'observacion'},
                {name: 'id_orden_trabajo', mapping: 'id_orden_trabajo'},
                {name: 'ultimaMilla', mapping: 'ultimaMilla'},
                {name: 'tipoFactibilidadUM', mapping: 'tipoFactibilidadUM'},
                {name: 'cliente', mapping: 'cliente'},
                {name: 'esRecontratacion', mapping: 'esRecontratacion'},
                {name: 'vendedor', mapping: 'vendedor'},
                {name: 'login2', mapping: 'login2'},
                {name: 'intIdPersonaEmpresaRol', mapping: 'intIdPersonaEmpresaRol'},
                {name: 'producto', mapping: 'producto'},
                {name: 'coordenadas', mapping: 'coordenadas'},
                {name: 'direccion', mapping: 'direccion'},
                {name: 'ciudad', mapping: 'ciudad'},
                {name: 'id_jurisdiccion', mapping: 'id_jurisdiccion'},
                {name: 'jurisdiccion', mapping: 'jurisdiccion'},
                {name: 'nombreSector', mapping: 'nombreSector'},
                {name: 'fePlanificacion', mapping: 'fePlanificacion'},
                {name: 'rutaCroquis', mapping: 'rutaCroquis'},
                {name: 'latitud', mapping: 'latitud'},
                {name: 'longitud', mapping: 'longitud'},
                {name: 'strTipoEnlace', mapping: 'strTipoEnlace'},
                {name: 'esPseudoPe', mapping: 'esPseudoPe'},
                {name: 'estado', mapping: 'estado'},
                {name: 'action1', mapping: 'action1'},
                {name: 'action2', mapping: 'action2'},
                {name: 'action3', mapping: 'action3'},
                {name: 'action4', mapping: 'action4'},
                {name: 'action5', mapping: 'action5'}
            ],
            autoLoad : true
    });   

    storeJurisdicciones = new Ext.data.Store({
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url: url_comboJurisdicciones,

            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            },
            extraParams: {
                fechaDesdePlanif: '',
                fechaHastaPlanif: '',
                fechaDesdeIngOrd: '',
                fechaHastaIngOrd: '',
                login2: '',
                descripcionPunto: '',
                vendedor: '',
                ciudad: '',
                numOrdenServicio: '',
                estado: 'Todos',
                ultimaMilla: '',
                limite:false
            }
        },
        fields:
            [
                {name: 'id_jurisdiccion', mapping: 'id_jurisdiccion'},
                {name: 'jurisdiccion', mapping: 'jurisdiccion'}
                
            ],
        listeners: {
            load: function(store) {
                // using a map of already used names
                const hits = {}
                store.filterBy(record => {
                    const name = record.get('id_jurisdiccion')
                    if (hits[name]) {
                        return false
                    } else {
                        hits[name] = true
                        return true
                    }
                });

            }
        },
        autoLoad: true
    });
    
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    });


    grid = Ext.create('Ext.grid.Panel', {
        width: 1230,
        height: 500,
        store: store,
        loadMask: true,
        frame: false,
        columns: [
            {
                id: 'id_factibilidad',
                header: 'IdFactibilidad',
                dataIndex: 'id_factibilidad',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_servicio',
                header: 'IdServicio',
                dataIndex: 'id_servicio',
                hidden: true,
                hideable: false
            },
            {
                id: 'tipo_orden',
                header: 'tipo_orden',
                dataIndex: 'tipo_orden',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_punto',
                header: 'IdPunto',
                dataIndex: 'id_punto',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_orden_trabajo',
                header: 'IdOrdenTrabajo',
                dataIndex: 'id_orden_trabajo',
                hidden: true,
                hideable: false
            },
            {
                id: 'esRecontratacion',
                header: 'esRecontratacion',
                dataIndex: 'esRecontratacion',
                hidden: true,
                hideable: false
            },
            {
                id: 'ultimaMilla',
                header: 'ultimaMilla',
                dataIndex: 'ultimaMilla',
                hidden: true,
                hideable: false
            },
            {
                id: 'cliente',
                header: 'Cliente',
                dataIndex: 'cliente',
                width: 140,
                sortable: true
            },
            {
                id: 'vendedor',
                header: 'Vendedor',
                dataIndex: 'vendedor',
                width: 115,
                sortable: true
            },
            {
                id: 'login2',
                header: 'Login',
                dataIndex: 'login2',
                width: 105,
                sortable: true
            },            
            {
                id: 'producto',
                header: 'Producto',
                dataIndex: 'producto',
                width: 120,
                sortable: true
            },
            {
                id: 'ultimaMilla',
                header: 'Ultima Milla',
                dataIndex: 'ultimaMilla',
                width: 80,                
                sortable: true,
                renderer:function(val){
                    return "<label style='font-weight: bold;color:green;'>"+val+"</label>";
                }
                
            },
            {
                id: 'jurisdiccion',
                header: 'Jurisdiccion',
                dataIndex: 'jurisdiccion',
                width: 80,
                sortable: true
            },
            {
                id: 'ciudad',
                header: 'Ciudad',
                dataIndex: 'ciudad',
                width: 65,
                sortable: true
            },
            {
                id: 'coordenadas',
                header: 'Coordenadas',
                dataIndex: 'coordenadas',
                width: 95,
                sortable: true
            },
            {
                id: 'direccion',
                header: 'Direccion',
                dataIndex: 'direccion',
                width: 120,
                sortable: true
            },
            {
                id: 'fePlanificacion',
                header: 'F. Sol. Planifica',
                dataIndex: 'fePlanificacion',
                width: 95,
                sortable: true
            },
            {
                id: 'intIdPersonaEmpresaRol',
                header: 'intIdPersonaEmpresaRol',
                dataIndex: 'intIdPersonaEmpresaRol',
                hidden: true,
                hideable: false
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 130,
                items: [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            return rec.get('action1')
                        },
                        tooltip: 'Ver Mapa',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            if (rec.get("latitud") != 0 && rec.get("longitud") != 0)
                                showVerMapa(rec);
                            else
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'Las coordenadas son incorrectas',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                        }
                    },                   
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var permiso = $("#ROLE_353-2717");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            
                            if (!boolPermiso) 
                            {
                                rec.data.action5 = "icon-invisible";
                            }
                            
                            return rec.get('action5');
                        },
                        tooltip: 'Ingresar Factibilidad',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);
                            
                            var permiso = $("#ROLE_353-2717");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            
                            if (!boolPermiso) 
                            {
                                rec.data.action5 = "icon-invisible";
                            }

                            if (rec.get('action5') !== "icon-invisible")
                            {
                                if (rec.get('ultimaMilla') === "Radio")
                                {
                                    ingresarFactibilidadUMRadio(grid.getStore().getAt(rowIndex).data);
                                }
                                else if (rec.get('ultimaMilla') === "Fibra Optica" && rec.get('tipoFactibilidadUM') === "RUTA")
                                {
                                    ingresarFactibilidadUM(grid.getStore().getAt(rowIndex).data);
                                }
                                else //Para UTP y FIBRA DIRECTA
                                {
                                    ingresarFactibilidadUMUtpFibraDirecto(grid.getStore().getAt(rowIndex).data);
                                }
                            }
                            else
                            {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'No tiene permisos para realizar esta accion',
                                    buttons: Ext.MessageBox.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        }
                    }
                ]
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    
    if(prefijoEmpresa === "TN")
    {
        grid.headerCt.insert(
                                    10,
                                    {
                                        text: 'T. Enlace',
                                        width: 80,
                                        dataIndex: 'strTipoEnlace',
                                        sortable: true
                                    }
                                );
    }    
    
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7,
        border: false,        
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'left'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: true,
        width: 1230,
        title: 'Criterios de busqueda',
        buttons: [
            {
                text: 'Buscar',
                iconCls: "icon_search",
                handler: function() {
                    buscar();
                }
            },
            {
                text: 'Limpiar',
                iconCls: "icon_limpiar",
                handler: function() {
                    limpiar();
                }
            }

        ],
        items:
            [
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'fieldcontainer',
                    fieldLabel: 'Fecha Solicitada',
                    items: [
                        {
                            xtype: 'datefield',
                            width: 290,
                            id: 'fechaDesdePlanif',
                            name: 'fechaDesdePlanif',
                            fieldLabel: 'Desde:',
                            format: 'Y-m-d',
                            editable: false
                        },
                        {
                            xtype: 'datefield',
                            width: 290,
                            id: 'fechaHastaPlanif',
                            name: 'fechaHastaPlanif',
                            fieldLabel: 'Hasta:',
                            format: 'Y-m-d',
                            editable: false
                        }
                    ]
                },
                {html: "&nbsp;", border: false, width: 150},
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    hideTrigger: true,
                    id: 'txtLogin',
                    fieldLabel: 'Login',
                    value: '',
                    width: 425
                },
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'textfield',
                    hideTrigger: true,
                    id: 'txtCiudad',
                    fieldLabel: 'Ciudad',
                    value: '',
                    width: 425
                },
                {html: "&nbsp;", border: false, width: 250},
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Ultima Milla',
                    id: 'sltUM',
                    name: 'sltUM',
                    value:'',
                    store: [					
                        ['Fibra Optica','Fibra Optica'],
                        ['Radio','Radio'],
                        ['UTP','UTP']
                    ],
                    width: 400
                },
                {html: "&nbsp;", border: false, width: 150},
                {
                    xtype: 'combobox',
                    fieldLabel: 'Jurisdicción:',
                    id: 'comboJurisdiccion',
                    name: 'comboJurisdiccion',
                    store: storeJurisdicciones,
                    displayField: 'jurisdiccion',
                    valueField: 'id_jurisdiccion',
                    queryMode: "remote",
                    emptyText: '',
                    listeners: {
                        
                    },
                    forceSelection: true
                }
            ],
        renderTo: 'filtro'
    });

});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    var boolError = false;
    
    if(( Ext.getCmp('fechaDesdePlanif').getValue()!==null)&&(Ext.getCmp('fechaHastaPlanif').getValue()!==null) )
    {
        if(Ext.getCmp('fechaDesdePlanif').getValue() > Ext.getCmp('fechaHastaPlanif').getValue())
        {
            boolError = true;
            
            Ext.Msg.show({
                title:'Error en Busqueda',
                msg: 'Por Favor para realizar la busqueda Fecha Desde Planificacion debe ser fecha menor a Fecha Hasta Planificacion.',
                buttons: Ext.Msg.OK,
                animEl: 'elId',
                icon: Ext.MessageBox.ERROR
            });	
        }
    }        
    
    if(!boolError)
    {
        store.removeAll();
        store.currentPage = 1;
        store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
        store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;       
        store.getProxy().extraParams.login            = Ext.getCmp('txtLogin').value;                
        store.getProxy().extraParams.ciudad           = Ext.getCmp('txtCiudad').value;          
        store.getProxy().extraParams.ultimaMilla      = Ext.getCmp('sltUM').value; 
        store.getProxy().extraParams.id_jurisdiccion   = Ext.getCmp('comboJurisdiccion').value.toString();
        if (store.getProxy().extraParams.id_jurisdiccion == '-1')
        {
            limpiar();
        }

        store.load();
    }          
}

function limpiar()
{
    Ext.getCmp('fechaDesdePlanif').setRawValue("");
    Ext.getCmp('fechaHastaPlanif').setRawValue("");   
    
    Ext.getCmp('txtLogin').value="";
    Ext.getCmp('txtLogin').setRawValue("");        
    
    Ext.getCmp('txtCiudad').value="";
    Ext.getCmp('txtCiudad').setRawValue("");
    
    Ext.getCmp('sltUM').value="";
    Ext.getCmp('sltUM').setRawValue("");

    Ext.getCmp('comboJurisdiccion').value = "";
    Ext.getCmp('comboJurisdiccion').setRawValue("");    
          
    store.getProxy().extraParams.fechaDesdePlanif = Ext.getCmp('fechaDesdePlanif').value;
    store.getProxy().extraParams.fechaHastaPlanif = Ext.getCmp('fechaHastaPlanif').value;
    store.getProxy().extraParams.login            = Ext.getCmp('txtLogin').value;    
    store.getProxy().extraParams.ciudad           = Ext.getCmp('txtCiudad').value; 
    store.getProxy().extraParams.ultimaMilla      = Ext.getCmp('sltUM').value;
    store.getProxy().extraParams.id_jurisdiccion   = Ext.getCmp('comboJurisdiccion').value.toString(); 
    store.load();
}


function ingresarFactibilidadUM(data)
{
    var storeElementos = new Ext.data.Store({
            total: 'total',
            listeners: {
                load: function() {
                }
            },
            proxy: {
                type: 'ajax',
                url: urlComboCajas,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                listeners: {
                    exception: function(proxy, response, options) {
                        Ext.MessageBox.alert('Error', "Favor ingrese un nombre de caja");
                    }
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    nombre: '',
                    intIdPunto: data.id_punto
                }
            },
            fields:
                [
                    {name: 'intIdElemento', mapping: 'intIdElemento'},
                    {name: 'strNombreElemento', mapping: 'strNombreElemento'}
                ]
        });

      var storeElementosByPadre = new Ext.data.Store({
            total: 'total',
            pageSize: 10000,
            proxy: {
                type: 'ajax',
                url: urlComboElementosByPadre,
                timeout: 120000,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                actionMethods: {
                    create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
                },
                extraParams: {
                    popId: '',
                    elemento: 'CASSETTE'
                }
            },
            fields:
                [
                    {name: 'idElemento', mapping: 'idElemento'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'}
                ]
        });
        
    var storeHilosDisponibles = new Ext.data.Store({
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: getHilosDisponibles,
                extraParams: {
                    idElemento: '',
                    estadoInterface: 'connected',
                    estadoInterfaceNotConect: 'not connect',
                    estadoInterfaceReserved: 'not connect',
                    strBuscaHilosServicios: 'BUSCA_HILOS_SERVICIOS',
                    intIdPunto: data.id_punto
                },
                reader: {
                    type: 'json',
                    root: 'encontrados'
                }
            },
            fields:
                [
                    {name: 'idInterfaceElemento', mapping: 'idInterfaceElemento'},
                    {name: 'idInterfaceElementoOut', mapping: 'idInterfaceElementoOut'},
                    {name: 'colorHilo', mapping: 'colorHilo'},
                    {name: 'numeroHilo', mapping: 'numeroHilo'},
                    {name: 'numeroColorHilo', mapping: 'numeroColorHilo'}
                ]
        });

        cbxPuertos = Ext.create('Ext.form.ComboBox', {
            id: 'cbxPuertos',
            name: 'cbxPuertos',
            store: storeHilosDisponibles,
            queryMode: 'local',
            fieldLabel: '* HILOS DISPONIBLES',
            displayField: 'numeroColorHilo',
            valueField: 'idInterfaceElementoOut',
            labelStyle: "color:red;",
            editable: false,
            hidden: true,
            listeners:
                {
                    select: function(combo)
                    {                    
                        var conn = new Ext.data.Connection({
                        listeners: {
                            'beforerequest': {
                                fn: function(con, opt) {
                                    Ext.get(formPanel.getId()).mask('Cargando Información de Backbone Nueva...');
                                },
                                scope: this
                            },
                            'requestcomplete': {
                                fn: function(con, res, opt) {
                                    Ext.get(formPanel.getId()).unmask();
                                },
                                scope: this
                            },
                            'requestexception': {
                                fn: function(con, res, opt) {
                                    Ext.get(formPanel.getId()).unmask();
                                },
                                scope: this
                            }
                        }
                    });
                    conn.request({
                        url: ajaxGetInfoBackboneUM,
                        method: 'post',
                        async: false,                        
                        params: {idInterfaceElementoConector: combo.getValue(),
                                 tipoBackbone               : 'RUTA',
                                 idServicio                 : data.id_servicio,
                                 ultimaMilla                : 'Fibra Optica'},
                        success: function(response)
                        {
                            var json = Ext.JSON.decode(response.responseText);

                            if (json.status === "OK")
                            {
                                Ext.getCmp('elementoPadreNuevo').setValue(json.result.nombreElementoPadre);
                                Ext.getCmp('idElementoPadreNuevo').setValue(json.result.idElementoPadre);
                                Ext.getCmp('elementoNuevo').setValue(json.result.nombreElemento);
                                Ext.getCmp('idElementoNuevo').setValue(json.result.idElemento);
                                Ext.getCmp('interfaceNueva').setValue(json.result.nombreInterfaceElemento);
                                Ext.getCmp('idInterfaceNueva').setValue(json.result.idInterfaceElemento);
                                Ext.getCmp('anilloNuevo').setValue(json.result.anillo);
                                Ext.getCmp('elementoConetenedorNuevo').setValue(Ext.getCmp('cbxIdElementoCaja').getRawValue());
                                Ext.getCmp('elementoConectorNuevo').setValue(Ext.getCmp('cbxElementoPNivel').getRawValue());
                            }
                            else
                            {
                                Ext.Msg.alert('Error', json.msg);
                                Ext.getCmp('elementoPadreNuevo').setValue("");
                                Ext.getCmp('idElementoPadreNuevo').setValue("");
                                Ext.getCmp('elementoNuevo').setValue("");
                                Ext.getCmp('idElementoNuevo').setValue("");
                                Ext.getCmp('interfaceNueva').setValue("");
                                Ext.getCmp('idInterfaceNueva').setValue("");
                                Ext.getCmp('anilloNuevo').setValue("");
                                Ext.getCmp('elementoConetenedorNuevo').setValue("");
                                Ext.getCmp('elementoConectorNuevo').setValue("");
                            }
                        },
                        failure: function(result)
                        {
                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                        }
                    });
                }
            }
    });
    //******** html campos requeridos...
    var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
    CamposRequeridos = Ext.create('Ext.Component', {
        html: iniHtmlCamposRequeridos,
        padding: 1,
        layout: 'anchor',
        style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
    });
    Ext.get(grid.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDatosBackbone,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.id_servicio,
            tipoElementoPadre: 'ROUTER'
        },
        success: function(response) {
            Ext.get(grid.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);
            var datosBackbone = json.encontrados[0];
            
            if(datosBackbone.idElementoPadre == 0)
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: datosBackbone.nombreElementoPadre,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
            else
            {               
                formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding: 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side'
                    },
                    items: [{
                            xtype: 'fieldset',
                            defaultType: 'textfield',
                            defaults: {
                                width: 855
                            },
                            items: [
                                CamposRequeridos,
                                //Información del cliente
                                {
                                    xtype: 'fieldset',
                                    title: 'Información de Servicio',
                                    defaultType: 'textfield',
                                    items: [
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'table',
                                                columns: 7,
                                                align: 'stretch'
                                            },
                                            items: [
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'nombreCompleto',
                                                    fieldLabel: 'Cliente',
                                                    displayField: data.cliente,
                                                    value: data.cliente,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'login',
                                                    fieldLabel: 'Login',
                                                    displayField: data.login2,
                                                    value: data.login2,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'producto',
                                                    fieldLabel: 'Producto',
                                                    displayField: data.producto,
                                                    value: data.producto,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 10, border: false},
                                                
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'tipoOrden',
                                                    fieldLabel: 'Tipo Orden',
                                                    displayField: data.tipo_orden,
                                                    value: data.tipo_orden,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},                                               
                                                {
                                                    xtype: 'textfield',
                                                    name: 'capacidadUno',
                                                    fieldLabel: 'Capacidad Uno',
                                                    displayField: datosBackbone.capacidadUno,
                                                    value: datosBackbone.capacidadUno,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'capacidadDos',
                                                    fieldLabel: 'Capacidad Dos',
                                                    displayField: datosBackbone.capacidadDos,
                                                    value: datosBackbone.capacidadDos,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false}
                                            ]
                                        }

                                    ]
                                }, //cierre de la Información del cliente

                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'table',
                                        columns: 5,
                                        align: 'stretch'
                                    },
                                    items: [
                                        //Información del servicio/producto
                                        {
                                            xtype: 'fieldset',
                                            title: '<b>Información de Backbone Nuevo</b>',
                                            defaultType: 'textfield',
                                            defaults: {
                                                width: 400,
                                                height: 220
                                            },
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoPadreNuevo',
                                                            id: 'elementoPadreNuevo',
                                                            fieldLabel: 'PE',                                                          
                                                            readOnly: true,
                                                            fieldStyle: 'color: blue;',
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoNuevo',
                                                            id: 'elementoNuevo',
                                                            fieldLabel: 'Switch',
                                                            fieldStyle: 'color: blue;',
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'interfaceNueva',
                                                            id: 'interfaceNueva',
                                                            fieldLabel: 'Interface',
                                                            fieldStyle: 'color: blue;',
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoContenedorNuevo',
                                                            id:'elementoConetenedorNuevo',
                                                            fieldStyle: 'color: blue;',
                                                            fieldLabel: 'Caja',
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoConectorNuevo',
                                                            id:'elementoConectorNuevo',
                                                            fieldStyle: 'color: blue;',
                                                            fieldLabel: 'Cassette',
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'anilloNuevo',
                                                            id:'anilloNuevo',
                                                            fieldStyle: 'color: blue;',
                                                            fieldLabel: 'Anillo',
                                                            readOnly: true,
                                                            width: 400
                                                        },                                                        
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'idElementoPadreNuevo',
                                                            id: 'idElementoPadreNuevo',                                                            
                                                            readOnly: true,
                                                            hidden: true,
                                                            width: 400
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'idElementoNuevo',
                                                            id: 'idElementoNuevo',                                                            
                                                            readOnly: true,
                                                            hidden: true,
                                                            width: 400
                                                        },
                                                         {
                                                            xtype: 'textfield',
                                                            name: 'idInterfaceNueva',
                                                            id: 'idInterfaceNueva',                                                            
                                                            readOnly: true,
                                                            hidden: true,
                                                            width: 400
                                                        }
                                                    ]
                                                }
                                            ]
                                        }, //cierre de la Información servicio/producto
                                        {width: 10, border: false},
                                        {
                                            xtype: 'fieldset',
                                            title: '<b>Información de Backbone Anterior</b>',
                                            defaultType: 'textfield',
                                            defaults: {
                                                width: 400,
                                                height: 220
                                            },
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoPadre',
                                                            fieldLabel: 'PE',
                                                            displayField: datosBackbone.nombreElementoPadre,
                                                            value: datosBackbone.nombreElementoPadre,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elemento',
                                                            fieldLabel: 'Switch',
                                                            displayField: datosBackbone.nombreElemento,
                                                            value: datosBackbone.nombreElemento,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'interface',
                                                            fieldLabel: 'Interface',
                                                            displayField: datosBackbone.nombreInterfaceElemento,
                                                            value: datosBackbone.nombreInterfaceElemento,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoContenedor',
                                                            fieldLabel: 'Caja',
                                                            displayField: datosBackbone.nombreCaja,
                                                            value: datosBackbone.nombreCaja,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoConector',
                                                            fieldLabel: 'Cassette',
                                                            displayField: datosBackbone.nombreSplitter,
                                                            value: datosBackbone.nombreSplitter,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'anillo',
                                                            fieldLabel: 'Anillo',
                                                            displayField: datosBackbone.anillo,
                                                            value: datosBackbone.anillo,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'hilo',
                                                            fieldLabel: 'Hilo',
                                                            displayField: datosBackbone.colorHilo,
                                                            value: datosBackbone.colorHilo,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'tipoEnlace',
                                                            fieldLabel: 'Tipo Enlace',
                                                            displayField: data.strTipoEnlace,
                                                            value: data.strTipoEnlace,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: '15%', border: false}
                                                    ]
                                                }
                                            ]
                                        }

                                    ]
                                },                                                                
                                {
                                    xtype: 'fieldset',
                                    title: '<b>Datos de Factibilidad<b>',
                                    defaultType: 'textfield',
                                    style: "font-weight:bold; margin-bottom: 5px;",
                                    layout: {
                                        tdAttrs: {style: 'padding: 5px;'},
                                        type: 'table',
                                        columns: 2,
                                        pack: 'center'
                                    },
                                    items: [
                                        {
                                            xtype: 'fieldset',
                                            style: "border:0",
                                            items: [
                                                {
                                                    xtype: 'combobox',
                                                    id: 'cbxIdElementoCaja',
                                                    name: 'cbxElementoCaja',
                                                    fieldLabel: '* CAJA',
                                                    typeAhead: true,
                                                    triggerAction: 'all',
                                                    displayField: 'strNombreElemento',
                                                    queryMode: "remote",
                                                    valueField: 'intIdElemento',
                                                    selectOnTab: true,
                                                    store: storeElementos,
                                                    width: 470,
                                                    lazyRender: true,
                                                    listClass: 'x-combo-list-small',
                                                    labelStyle: "color:red;",
                                                    forceSelection: true,
                                                    emptyText: 'Ingrese un nombre de Caja..',
                                                    minChars: 3,
                                                    listeners: {
                                                        select: {fn: function(combo, value) {
                                                                Ext.getCmp('cbxElementoPNivel').reset();
                                                                Ext.getCmp('cbxElementoPNivel').setDisabled(false);
                                                                Ext.getCmp('cbxPuertos').setValue("");
                                                                Ext.getCmp('cbxPuertos').setVisible(false);

                                                                storeElementosByPadre.proxy.extraParams = {
                                                                    popId: combo.getValue(),
                                                                    elemento: 'CASSETTE',
                                                                    estado: 'Activo'
                                                                };
                                                                storeElementosByPadre.load({params: {}});
                                                            }}
                                                    }
                                                },
                                                {
                                                    xtype: 'combobox',
                                                    id: 'cbxElementoPNivel',
                                                    name: 'cbxElementoPNivel',
                                                    fieldLabel: '* CASSETTE',
                                                    typeAhead: true,
                                                    width: 470,
                                                    queryMode: "local",
                                                    triggerAction: 'all',
                                                    displayField: 'nombreElemento',
                                                    valueField: 'idElemento',
                                                    selectOnTab: true,
                                                    store: storeElementosByPadre,
                                                    lazyRender: true,
                                                    listClass: 'x-combo-list-small',
                                                    emptyText: 'Seleccione un CASSETTE',
                                                    labelStyle: "color:red;",
                                                    disabled: true,
                                                    editable: false,
                                                    listeners: {
                                                        select: {fn: function(combo, value) {

                                                                Ext.getCmp('cbxPuertos').setValue("");
                                                                Ext.getCmp('cbxPuertos').setVisible(false);

                                                                var arrayParamInfoElemDist = [];

                                                                arrayParamInfoElemDist['strIdElementoDistribucion'] = combo.getValue();
                                                                arrayParamInfoElemDist['strUrlCalculaMetraje'] = urlCalculaMetraje;
                                                                arrayParamInfoElemDist['intIdPunto'] = data.id_punto;
                                                                arrayParamInfoElemDist['storeHilosDisponibles'] = storeHilosDisponibles;

                                                                var objResponseHiloMetraje = buscaHiloCalculaMetraje(arrayParamInfoElemDist);
                                                                if ("100" !== objResponseHiloMetraje.strStatus) {
                                                                    strErrorMetraje = objResponseHiloMetraje.strMessageStatus;
                                                                }

                                                            }
                                                        }
                                                    }
                                                },
                                                cbxPuertos,
                                                {
                                                    xtype: 'textfield',
                                                    fieldLabel: 'Metraje',
                                                    name: 'txtNamefloatMetraje',
                                                    hidden: true,
                                                    id: 'txtIdfloatMetraje',
                                                    regex: /^(?:\d*\.\d{1,2}|\d+)$/,
                                                    value: 0,
                                                    readOnly: false
                                                }
                                            ]
                                        }
                                    ]
                                }

                            ]
                        }],
                    buttons: [{
                            text: 'Ejecutar',
                            formBind: true,
                            handler: function() 
                            {   
                                var swAnterior = datosBackbone.idElemento;
                                var swNuevo    = Ext.getCmp('idElementoNuevo').getValue();
                                
                                var peAnterior = datosBackbone.idElementoPadre;
                                var peNuevo    = Ext.getCmp('idElementoPadreNuevo').getValue();
                                
                                var anilloAnt  = datosBackbone.anillo;
                                var anilloNue  = Ext.getCmp('anilloNuevo').getValue();
                                
                                //Validar Tipo de Cambio UM a Realizar
                                var tipoCambioUM = "";
                                
                                //Se verifica si es mismo SW
                                if(parseInt(swAnterior) === parseInt(swNuevo))
                                {
                                    tipoCambioUM = "MISMO_SWITCH";
                                }
                                //Se verifica si cambie de PE 
                                else if(parseInt(peAnterior) !== parseInt(peNuevo))
                                {
                                    tipoCambioUM = "DIFERENTE_PE";
                                }
                                else
                                {
                                    //Se verifica si es mismo anillo mismo pe
                                    if(parseInt(anilloAnt) === parseInt(anilloNue))
                                    {
                                        tipoCambioUM = "MISMO_PE_MISMO_ANILLO";
                                    }
                                    //Se verifica si es diferente anillo mismo pe
                                    else
                                    {
                                        tipoCambioUM = "MISMO_PE_DIFERENTE_ANILLO";
                                    }
                                }                                                                                     
                                
                                Ext.get(formPanel.getId()).mask('Generando Factibilidad...');
                                Ext.Ajax.request({
                                    url:  url_ajaxGenerarFactibilidad,
                                    method: 'post',
                                    timeout: 400000,
                                    params: {                                        
                                        idSolicitud: data.id_factibilidad,
                                        idSwitch   : swNuevo,
                                        idInterface: Ext.getCmp('idInterfaceNueva').getValue(),
                                        idCaja     : Ext.getCmp('cbxIdElementoCaja').getValue(),
                                        idCassette : Ext.getCmp('cbxElementoPNivel').getValue(),
                                        idInterfaceElementoConector : Ext.getCmp('cbxPuertos').getValue(),
                                        tipoCambio : tipoCambioUM
                                    },
                                    success: function(response) {
                                        Ext.get(formPanel.getId()).unmask();                                                                                
                                        
                                        Ext.Msg.alert('Mensaje', response.responseText, function(btn) {
                                            if (btn == 'ok') {
                                                store.load();
                                                win.destroy();
                                            }
                                        });                                       
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });                                    
                            }
                        }, {
                            text: 'Cancelar',
                            handler: function() {
                                win.destroy();
                            }
                        }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title: 'Factibilidad Ultima Milla',
                    modal: true,
                    width: 900,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
            }
        }//cierre response
    }); 
}

function ingresarFactibilidadUMUtpFibraDirecto(data)
{    
    var storeSwitch = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url: getElementoSwitch,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombreElemento: '',
                marcaElemento: '',
                modeloElemento: '',
                canton: '',
                jurisdiccion: '',
                tipoElemento: 'SWITCH',
                estado: 'Todos',
                procesoBusqueda: 'limitado',
                esPseudoPe : data.esPseudoPe
            }
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'},
                {name: 'ipElemento', mapping: 'ipElemento'},
                {name: 'cantonNombre', mapping: 'cantonNombre'},
                {name: 'jurisdiccionNombre', mapping: 'jurisdiccionNombre'},
                {name: 'marcaElemento', mapping: 'marcaElemento'},
                {name: 'modeloElemento', mapping: 'modeloElemento'},
                {name: 'longitud', mapping: 'longitud'},
                {name: 'latitud', mapping: 'latitud'},
                {name: 'estado', mapping: 'estado'},
                {name: 'action1', mapping: 'action1'},
                {name: 'action2', mapping: 'action2'},
                {name: 'action3', mapping: 'action3'}
            ]
    });

    var storeInterfacesElemento = new Ext.data.Store({
        pageSize: 100,
        proxy: {
            type: 'ajax',
            timeout: 400000,
            url: getInterfacesPorElemento,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idInterface', mapping: 'idInterface'},
                {name: 'nombreInterface', mapping: 'nombreInterface'},
                {name: 'nombreEstadoInterface', mapping: 'nombreEstadoInterface'}
            ]
    });        
    
    //******** html campos requeridos...
    var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
    CamposRequeridos = Ext.create('Ext.Component', {
        html: iniHtmlCamposRequeridos,
        padding: 1,
        layout: 'anchor',
        style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
    });
    Ext.get(grid.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDatosBackbone,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.id_servicio,
            tipoElementoPadre: 'ROUTER'
        },
        success: function(response) {
            Ext.get(grid.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);
            var datosBackbone = json.encontrados[0];
            
            if(datosBackbone.idElementoPadre == 0)
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: datosBackbone.nombreElementoPadre,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
            else
            {               
                formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding: 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side'
                    },
                    items: [{
                            xtype: 'fieldset',
                            defaultType: 'textfield',
                            defaults: {
                                width: 855
                            },
                            items: [
                                CamposRequeridos,
                                //Información del cliente
                                {
                                    xtype: 'fieldset',
                                    title: 'Información de Servicio',
                                    defaultType: 'textfield',
                                    items: [
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'table',
                                                columns: 7,
                                                align: 'stretch'
                                            },
                                            items: [
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'nombreCompleto',
                                                    fieldLabel: 'Cliente',
                                                    displayField: data.cliente,
                                                    value: data.cliente,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'login',
                                                    fieldLabel: 'Login',
                                                    displayField: data.login2,
                                                    value: data.login2,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'producto',
                                                    fieldLabel: 'Producto',
                                                    displayField: data.producto,
                                                    value: data.producto,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 10, border: false},
                                                
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'tipoOrden',
                                                    fieldLabel: 'Tipo Orden',
                                                    displayField: data.tipo_orden,
                                                    value: data.tipo_orden,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},                                               
                                                {
                                                    xtype: 'textfield',
                                                    name: 'capacidadUno',
                                                    fieldLabel: 'Capacidad Uno',
                                                    displayField: datosBackbone.capacidadUno,
                                                    value: datosBackbone.capacidadUno,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'capacidadDos',
                                                    fieldLabel: 'Capacidad Dos',
                                                    displayField: datosBackbone.capacidadDos,
                                                    value: datosBackbone.capacidadDos,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false}
                                            ]
                                        }

                                    ]
                                }, //cierre de la Información del cliente

                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'table',
                                        columns: 5,
                                        align: 'stretch'
                                    },
                                    items: [
                                        //Información del servicio/producto
                                        {
                                            xtype: 'fieldset',
                                            title: '<b>Información de Backbone Nuevo</b>',
                                            defaultType: 'textfield',
                                            defaults: {
                                                width: 400,
                                                height: 130
                                            },
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoPadreNuevo',
                                                            id: 'elementoPadreNuevo',
                                                            fieldLabel: 'PE',                                                          
                                                            readOnly: true,
                                                            fieldStyle: 'color: blue;',
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoNuevo',
                                                            id: 'elementoNuevo',
                                                            fieldLabel: 'Switch',
                                                            fieldStyle: 'color: blue;',
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'interfaceNueva',
                                                            id: 'interfaceNueva',
                                                            fieldLabel: 'Interface',
                                                            fieldStyle: 'color: blue;',
                                                            readOnly: true,
                                                            width: 400
                                                        },                                                        
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'anilloNuevo',
                                                            id:'anilloNuevo',
                                                            fieldStyle: 'color: blue;',
                                                            fieldLabel: 'Anillo',
                                                            readOnly: true,
                                                            width: 400
                                                        },                                                        
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'idElementoPadreNuevo',
                                                            id: 'idElementoPadreNuevo',                                                            
                                                            readOnly: true,
                                                            hidden: true,
                                                            width: 400
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'idElementoNuevo',
                                                            id: 'idElementoNuevo',                                                            
                                                            readOnly: true,
                                                            hidden: true,
                                                            width: 400
                                                        },
                                                         {
                                                            xtype: 'textfield',
                                                            name: 'idInterfaceNueva',
                                                            id: 'idInterfaceNueva',                                                            
                                                            readOnly: true,
                                                            hidden: true,
                                                            width: 400
                                                        }
                                                    ]
                                                }
                                            ]
                                        }, //cierre de la Información servicio/producto
                                        {width: 10, border: false},
                                        {
                                            xtype: 'fieldset',
                                            title: '<b>Información de Backbone Anterior</b>',
                                            defaultType: 'textfield',
                                            defaults: {
                                                width: 400,
                                                height: 130
                                            },
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoPadre',
                                                            fieldLabel: 'PE',
                                                            displayField: datosBackbone.nombreElementoPadre,
                                                            value: datosBackbone.nombreElementoPadre,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elemento',
                                                            fieldLabel: 'Switch',
                                                            displayField: datosBackbone.nombreElemento,
                                                            value: datosBackbone.nombreElemento,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'interface',
                                                            fieldLabel: 'Interface',
                                                            displayField: datosBackbone.nombreInterfaceElemento,
                                                            value: datosBackbone.nombreInterfaceElemento,
                                                            readOnly: true,
                                                            width: 400
                                                        },                                                        
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'anillo',
                                                            fieldLabel: 'Anillo',
                                                            displayField: datosBackbone.anillo,
                                                            value: datosBackbone.anillo,
                                                            readOnly: true,
                                                            width: 400
                                                        },                                                       
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'tipoEnlace',
                                                            fieldLabel: 'Tipo Enlace',
                                                            displayField: data.strTipoEnlace,
                                                            value: data.strTipoEnlace,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: '15%', border: false}
                                                    ]
                                                }
                                            ]
                                        }

                                    ]
                                },  
                                
                                {
                                    xtype: 'fieldset',
                                    title: '<b>Información de Última Milla</b>',
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
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'um',
                                                    id:'um',
                                                    fieldLabel: 'Ultima Milla',
                                                    displayField: data.ultimaMilla,
                                                    value: data.ultimaMilla,
                                                    fieldStyle: 'color: green;',
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'tbb',
                                                    id: 'tbb',
                                                    fieldLabel: 'Tipo Backbone',
                                                    displayField: data.tipoFactibilidadUM,
                                                    value: data.tipoFactibilidadUM,
                                                    fieldStyle: 'color: green;',
                                                    readOnly: true,
                                                    width: 250
                                                },
                                               {width: 5, border: false}
                                            ]
                                        }

                                    ]
                                },
                                
                                {
                                    xtype: 'fieldset',
                                    title: '<b>Datos de Factibilidad<b>',
                                    defaultType: 'textfield',
                                    style: "font-weight:bold; margin-bottom: 5px;",
                                    layout: {
                                        tdAttrs: {style: 'padding: 5px;'},
                                        type: 'table',
                                        columns: 2,
                                        pack: 'center'
                                    },
                                    items: [
                                        {
                                            xtype: 'fieldset',
                                            style: "border:0",
                                            items: [
                                                {   
                                                    loadingText: 'Buscando ...',
                                                    xtype: 'combobox',
                                                    name: 'cmdElementoDirecto',
                                                    id: 'cmdElementoDirecto',
                                                    fieldLabel: '* ELEMENTO',
                                                    displayField: 'nombreElemento',
                                                    queryMode: "remote",
                                                    valueField: 'idElemento',
                                                    labelStyle: "color:red;",
                                                    store: storeSwitch,
                                                    lazyRender: true,
                                                    forceSelection: true,
                                                    width: 400,
                                                    minChars: 3,                                                   
                                                    listeners: {
                                                        select: function(combo) 
                                                        {
                                                            Ext.getCmp('cmbInterfaceDirecto').setValue("");
                                                            Ext.getCmp('cmbInterfaceDirecto').setRawValue("");
                                                            storeInterfacesElemento.proxy.extraParams = {intIdCliente: data.intIdPersonaEmpresaRol, 
                                                                intIdInterfaceAnterior: datosBackbone.idInterfaceElemento, idElemento: combo.getValue(), 
                                                                estado: 'Todos'};
                                                            storeInterfacesElemento.load({params: {}});
                                                            Ext.getCmp('cmbInterfaceDirecto').setDisabled(false);
                                                        }
                                                    },
                                                },
                                                {
                                                    queryMode: 'local',
                                                    xtype: 'combobox',
                                                    id: 'cmbInterfaceDirecto',
                                                    name: 'cmbInterfaceDirecto',
                                                    fieldLabel: '* PUERTO ELEMENTO',
                                                    disabled: true,
                                                    displayField: 'nombreEstadoInterface',
                                                    valueField: 'idInterface',
                                                    labelStyle: "color:red;",
                                                    loadingText: 'Buscando...',
                                                    store: storeInterfacesElemento,
                                                    width: 400,
                                                    listeners:{
                                                        select: function(combo)
                                                        {                                                                                                                        
                                                            Ext.MessageBox.wait('Cargando Información de Backbone Nueva...');
                                                            Ext.Ajax.request({
                                                                url: ajaxGetInfoBackboneUM,
                                                                method: 'post',
                                                                async: false,
                                                                params: {
                                                                         idInterfaceElementoConector: combo.getValue(),
                                                                         idServicio                 : data.id_servicio,
                                                                         tipoBackbone               : Ext.getCmp('tbb').getValue(),
                                                                         ultimaMilla                : Ext.getCmp('um').getValue(),
                                                                         elemento                   : Ext.getCmp('cmdElementoDirecto').getValue()
                                                                        },
                                                                success: function(response)
                                                                {
                                                                    Ext.MessageBox.hide();
                                                                    var json = Ext.JSON.decode(response.responseText);

                                                                    if (json.status === "OK")
                                                                    {
                                                                        Ext.getCmp('elementoPadreNuevo').setValue(json.result.nombreElementoPadre);
                                                                        Ext.getCmp('idElementoPadreNuevo').setValue(json.result.idElementoPadre);
                                                                        Ext.getCmp('elementoNuevo').setValue(json.result.nombreElemento);
                                                                        Ext.getCmp('idElementoNuevo').setValue(json.result.idElemento);
                                                                        Ext.getCmp('interfaceNueva').setValue(json.result.nombreInterfaceElemento);
                                                                        Ext.getCmp('idInterfaceNueva').setValue(json.result.idInterfaceElemento);
                                                                        Ext.getCmp('anilloNuevo').setValue(json.result.anillo);                                                                        
                                                                    }
                                                                    else
                                                                    {
                                                                        Ext.Msg.alert('Error', json.msg);
                                                                        Ext.getCmp('elementoPadreNuevo').setValue("");
                                                                        Ext.getCmp('idElementoPadreNuevo').setValue("");
                                                                        Ext.getCmp('elementoNuevo').setValue("");
                                                                        Ext.getCmp('idElementoNuevo').setValue("");
                                                                        Ext.getCmp('interfaceNueva').setValue("");
                                                                        Ext.getCmp('idInterfaceNueva').setValue("");
                                                                        Ext.getCmp('anilloNuevo').setValue("");                                                                        
                                                                    }
                                                                },
                                                                failure: function(result)
                                                                {
                                                                    Ext.MessageBox.hide();
                                                                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                                                }
                                                            });
                                                        }
                                                    }
                                                }
                                            ]
                                        }
                                    ]
                                }

                            ]
                        }],
                    buttons: [{
                            text: 'Ejecutar',
                            formBind: true,
                            handler: function() 
                            {   
                                var swAnterior = datosBackbone.idElemento;
                                var swNuevo    = Ext.getCmp('idElementoNuevo').getValue();
                                
                                var peAnterior = datosBackbone.idElementoPadre;
                                var peNuevo    = Ext.getCmp('idElementoPadreNuevo').getValue();
                                
                                var anilloAnt  = datosBackbone.anillo;
                                var anilloNue  = Ext.getCmp('anilloNuevo').getValue();
                                
                                //Validar Tipo de Cambio UM a Realizar
                                var tipoCambioUM = "";
                                
                                //Se verifica si es mismo SW
                                if(parseInt(swAnterior) === parseInt(swNuevo))
                                {
                                    tipoCambioUM = "MISMO_SWITCH";
                                }
                                //Se verifica si cambie de PE 
                                else if(parseInt(peAnterior) !== parseInt(peNuevo))
                                {
                                    tipoCambioUM = "DIFERENTE_PE";
                                }
                                else
                                {
                                    //Se verifica si es mismo anillo mismo pe
                                    if(parseInt(anilloAnt) === parseInt(anilloNue))
                                    {
                                        tipoCambioUM = "MISMO_PE_MISMO_ANILLO";
                                    }
                                    //Se verifica si es diferente anillo mismo pe
                                    else
                                    {
                                        tipoCambioUM = "MISMO_PE_DIFERENTE_ANILLO";
                                    }
                                }                                                                                     
                                
                                Ext.get(formPanel.getId()).mask('Generando Factibilidad...');
                                Ext.Ajax.request({
                                    url:  url_ajaxGenerarFactibilidad,
                                    method: 'post',
                                    timeout: 400000,
                                    params: {                                        
                                        idSolicitud  : data.id_factibilidad,
                                        idSwitch     : swNuevo,
                                        idInterface  : Ext.getCmp('idInterfaceNueva').getValue(),                                                                               
                                        tipoCambio   : tipoCambioUM
                                    },
                                    success: function(response) {
                                        Ext.get(formPanel.getId()).unmask();                                                                                
                                        
                                        Ext.Msg.alert('Mensaje', response.responseText, function(btn) {
                                            if (btn == 'ok') {
                                                store.load();
                                                win.destroy();
                                            }
                                        });                                       
                                    },
                                    failure: function(result)
                                    {
                                        Ext.get(formPanel.getId()).unmask();
                                        Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                    }
                                });                                    
                            }
                        }, {
                            text: 'Cancelar',
                            handler: function() {
                                win.destroy();
                            }
                        }]
                });

                var win = Ext.create('Ext.window.Window', {
                    title: 'Factibilidad Ultima Milla - '+data.ultimaMilla+"/"+data.tipoFactibilidadUM,
                    modal: true,
                    width: 900,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();
            }
        }//cierre response
    }); 
}

function buscaHiloCalculaMetraje(arrayCalculaMetraje) {
    var objResponse;

    var conn = new Ext.data.Connection({
	  
	      listeners: {
	            'beforerequest': {
	                fn: function (con, opt) {
	                    Ext.get(formPanel.getId()).mask('Cargando Datos...');
	                },
	                scope: this
	            },
	            'requestcomplete': {
	                fn: function (con, res, opt) {
	                    Ext.get(formPanel.getId()).unmask();
	                },
	                scope: this
	            },
	            'requestexception': {
	                fn: function (con, res, opt) {
	                    Ext.get(formPanel.getId()).unmask();
	                },
	                scope: this
	            }
	        }
	});   
   conn.request({
        url: arrayCalculaMetraje['strUrlCalculaMetraje'],
        method: 'post',
        timeout: 120000,
        async: false,
        params: {
            intIdElemento: arrayCalculaMetraje['strIdElementoDistribucion'],
            intIdPunto: arrayCalculaMetraje['intIdPunto']
        },
        success: function(response) {
            
            objResponse = Ext.JSON.decode(response.responseText);
            Ext.getCmp('txtIdfloatMetraje').setVisible(true);
            Ext.getCmp('txtIdfloatMetraje').setValue(objResponse.registros);

            if ("100" !== objResponse.strStatus) {
                Ext.Msg.alert(Utils.arrayTituloMensajeBox[objResponse.strStatus], objResponse.strMessageStatus);
            }

            arrayCalculaMetraje['storeHilosDisponibles'].proxy.extraParams = {
                idElemento: Ext.getCmp('cbxElementoPNivel').value,
                estadoInterface: 'connected',
                estadoInterfaceNotConect: 'not connect',
                estadoInterfaceReserved: 'not connect',
                intIdPunto: arrayCalculaMetraje['intIdPunto'],
                strBuscaHilosServicios: 'BUSCA_HILOS_SERVICIOS'
            };
            arrayCalculaMetraje['storeHilosDisponibles'].load();            
        },
        failure: function(result)
        {
            Ext.MessageBox.show({
                title: 'Error',
                msg: result.statusText,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            });
        }
    });

    Ext.getCmp('cbxPuertos').setVisible(true);
    Ext.getCmp('cbxPuertos').reset();
    return objResponse;
}


function ingresarFactibilidadUMRadio(data)
{

    winIngFactUmRadio = "";

    Ext.define('esTercerizada', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'valor', type: 'string'}
        ]
    });

    storeTercerizada = new Ext.data.Store({
        model: 'esTercerizada',
        data: [
            {valor: 'N'},
            {valor: 'S'},
        ]
    });

    storeTercerizadoras = new Ext.data.Store({
        total: 'total',
        pageSize: 10000,
        proxy: {
            type: 'ajax',
            url: url_getEmpresasExternas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            },
            extraParams: {
            }
        },
        fields:
                [
                    {name: 'id_empresa_externa', mapping: 'id_empresa_externa'},
                    {name: 'nombre_empresa_externa', mapping: 'nombre_empresa_externa'}
                ],
        autoLoad: true
    });

    storeElementosRadio = new Ext.data.Store({
        total: 'total',
        pageSize: 10000,
        proxy: {
            timeout: 60000,
            type: 'ajax',
            url: url_ajaxComboElementos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            },
            extraParams: {
                nombre: this.nombreElemento,
                modelo: '',
                elemento: 'RADIO',
                tipoElementoRed: 'BACKBONE'
            }
        },
        fields:
                [
                    {name: 'idElemento', mapping: 'idElemento'},
                    {name: 'nombreElemento', mapping: 'nombreElemento'}
                ],
        autoLoad: true
    });




    //******** html campos requeridos...
    var iniHtmlCamposRequeridos = '<p style="text-align: right; color:red; font-weight: bold; border: 0 !important;">* Campos requeridos</p>';
    CamposRequeridos = Ext.create('Ext.Component', {
        html: iniHtmlCamposRequeridos,
        padding: 1,
        layout: 'anchor',
        style: {color: 'red', textAlign: 'right', fontWeight: 'bold', marginBottom: '5px', border: '0'}
    });
    Ext.get(grid.getId()).mask('Consultando Datos...');
    Ext.Ajax.request({
        url: getDatosBackbone,
        method: 'post',
        timeout: 400000,
        params: {
            idServicio: data.id_servicio,
            tipoElementoPadre: 'ROUTER'
        },
        success: function (response) {
            Ext.get(grid.getId()).unmask();

            var json = Ext.JSON.decode(response.responseText);
            var datosBackbone = json.encontrados[0];

            if (datosBackbone.idElementoPadre == 0)
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: datosBackbone.nombreElementoPadre,
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            } 
            else if (Ext.isEmpty(datosBackbone.nombreSplitter))
            {
                Ext.MessageBox.show({
                    title: 'Error',
                    msg: "Servicio con información incompleta, favor crear un Tarea con los datos requeridos para su regularización.",
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            } 
            else
            {
                formPanel = Ext.create('Ext.form.Panel', {
                    bodyPadding: 2,
                    waitMsgTarget: true,
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 85,
                        msgTarget: 'side'
                    },
                    items: [{
                            xtype: 'fieldset',
                            defaultType: 'textfield',
                            defaults: {
                                width: 855
                            },
                            items: [
                                CamposRequeridos,
                                //Información del cliente
                                {
                                    xtype: 'fieldset',
                                    title: 'Información de Servicio',
                                    defaultType: 'textfield',
                                    items: [
                                        {
                                            xtype: 'container',
                                            layout: {
                                                type: 'table',
                                                columns: 7,
                                                align: 'stretch'
                                            },
                                            items: [
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'nombreCompleto',
                                                    fieldLabel: 'Cliente',
                                                    displayField: data.cliente,
                                                    value: data.cliente,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'login',
                                                    fieldLabel: 'Login',
                                                    displayField: data.login2,
                                                    value: data.login2,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'producto',
                                                    fieldLabel: 'Producto',
                                                    displayField: data.producto,
                                                    value: data.producto,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 10, border: false},
                                                {width: 10, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'tipoOrden',
                                                    fieldLabel: 'Tipo Orden',
                                                    displayField: data.tipo_orden,
                                                    value: data.tipo_orden,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'capacidadUno',
                                                    fieldLabel: 'Capacidad Uno',
                                                    displayField: datosBackbone.capacidadUno,
                                                    value: datosBackbone.capacidadUno,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false},
                                                {
                                                    xtype: 'textfield',
                                                    name: 'capacidadDos',
                                                    fieldLabel: 'Capacidad Dos',
                                                    displayField: datosBackbone.capacidadDos,
                                                    value: datosBackbone.capacidadDos,
                                                    readOnly: true,
                                                    width: 250
                                                },
                                                {width: 5, border: false}
                                            ]
                                        }

                                    ]
                                }, //cierre de la Información del cliente

                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'table',
                                        columns: 5,
                                        align: 'stretch'
                                    },
                                    items: [
                                        //Información del servicio/producto
                                        {
                                            xtype: 'fieldset',
                                            title: '<b>Información de Backbone Nuevo</b>',
                                            defaultType: 'textfield',
                                            defaults: {
                                                width: 400,
                                                height: 150
                                            },
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoPadreNuevo',
                                                            id: 'elementoPadreNuevo',
                                                            fieldLabel: 'PE',
                                                            readOnly: true,
                                                            fieldStyle: 'color: blue;',
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'anilloNuevo',
                                                            id: 'anilloNuevo',
                                                            fieldStyle: 'color: blue;',
                                                            fieldLabel: 'Anillo',
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoNuevo',
                                                            id: 'elementoNuevo',
                                                            fieldLabel: 'Switch',
                                                            fieldStyle: 'color: blue;',
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'interfaceNueva',
                                                            id: 'interfaceNueva',
                                                            fieldLabel: 'Interface',
                                                            fieldStyle: 'color: blue;',
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoConectorNuevo',
                                                            id: 'elementoConectorNuevo',
                                                            fieldStyle: 'color: blue;',
                                                            fieldLabel: 'Radio Backbone',
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'idElementoPadreNuevo',
                                                            id: 'idElementoPadreNuevo',
                                                            readOnly: true,
                                                            hidden: true,
                                                            width: 400
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'idAnilloNuevo',
                                                            id: 'idAnilloNuevo',
                                                            readOnly: true,
                                                            hidden: true,
                                                            width: 400
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'idElementoNuevo',
                                                            id: 'idElementoNuevo',
                                                            readOnly: true,
                                                            hidden: true,
                                                            width: 400
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'idInterfaceNueva',
                                                            id: 'idInterfaceNueva',
                                                            readOnly: true,
                                                            hidden: true,
                                                            width: 400
                                                        },
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'idElementoConectorNuevo',
                                                            id: 'idElementoConectorNuevo',
                                                            readOnly: true,
                                                            hidden: true,
                                                            width: 400
                                                        }
                                                    ]
                                                }
                                            ]
                                        }, //cierre de la Información servicio/producto
                                        {width: 10, border: false},
                                        {
                                            xtype: 'fieldset',
                                            title: '<b>Información de Backbone Anterior</b>',
                                            defaultType: 'textfield',
                                            defaults: {
                                                width: 400,
                                                height: 150
                                            },
                                            items: [
                                                {
                                                    xtype: 'container',
                                                    layout: {
                                                        align: 'stretch'
                                                    },
                                                    items: [
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoPadre',
                                                            fieldLabel: 'PE',
                                                            displayField: datosBackbone.nombreElementoPadre,
                                                            value: datosBackbone.nombreElementoPadre,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'anillo',
                                                            fieldLabel: 'Anillo',
                                                            displayField: datosBackbone.anillo,
                                                            value: datosBackbone.anillo,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elemento',
                                                            fieldLabel: 'Switch',
                                                            displayField: datosBackbone.nombreElemento,
                                                            value: datosBackbone.nombreElemento,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'interface',
                                                            fieldLabel: 'Interface',
                                                            displayField: datosBackbone.nombreInterfaceElemento,
                                                            value: datosBackbone.nombreInterfaceElemento,
                                                            readOnly: true,
                                                            width: 400
                                                        },
                                                        {width: 10, border: false},
                                                        {
                                                            xtype: 'textfield',
                                                            name: 'elementoConector',
                                                            fieldLabel: 'Radio Backbone',
                                                            displayField: datosBackbone.nombreSplitter,
                                                            value: datosBackbone.nombreSplitter,
                                                            readOnly: true,
                                                            width: 400
                                                        }
                                                    ]
                                                }
                                            ]
                                        }

                                    ]
                                },
                                {
                                    xtype: 'container',
                                    layout: {
                                        type: 'table',
                                        columns: 5,
                                        align: 'stretch'
                                    },
                                    items: [
                                        {
                                            xtype: 'fieldset',
                                            title: '<b>Datos de Factibilidad<b>',
                                            defaultType: 'textfield',
                                            style: "font-weight:bold; margin-bottom: 5px;",
                                            layout: {
                                                tdAttrs: {style: 'padding: 5px;'},
                                                type: 'table',
                                                columns: 2,
                                                pack: 'center'
                                            },
                                            items:
                                                    [
                                                        {
                                                            xtype: 'fieldset',
                                                            style: "border:0",
                                                            items: [
                                                                {
                                                                    xtype: 'combobox',
                                                                    id: 'cmb_ESTERCERIZADA',
                                                                    name: 'cmb_ESTERCERIZADA',
                                                                    fieldLabel: '* Es Tercerizada',
                                                                    displayField: 'valor',
                                                                    valueField: 'valor',
                                                                    labelStyle: "color:red;",
                                                                    value: 'N',
                                                                    queryMode: 'local',
                                                                    store: storeTercerizada,
                                                                    width: '350px',
                                                                    listeners: {
                                                                        select: {
                                                                            fn: function (combo, value) {
                                                                                if (combo.value == "S") {
                                                                                    Ext.getCmp('cmb_TERCERIZADORA').setVisible(true);
                                                                                } else {
                                                                                    Ext.getCmp('cmb_TERCERIZADORA').setVisible(false);
                                                                                }
                                                                                Ext.getCmp('elementoPadreNuevo').setValue("");
                                                                                Ext.getCmp('anilloNuevo').setValue("");
                                                                                Ext.getCmp('elementoNuevo').setValue("");
                                                                                Ext.getCmp('interfaceNueva').setValue("");
                                                                                Ext.getCmp('elementoConectorNuevo').setValue("");
                                                                                Ext.getCmp('idElementoPadreNuevo').setValue("");
                                                                                Ext.getCmp('idAnilloNuevo').setValue("");
                                                                                Ext.getCmp('idElementoNuevo').setValue("");
                                                                                Ext.getCmp('idInterfaceNueva').setValue("");
                                                                                Ext.getCmp('idElementoConectorNuevo').setValue("");
                                                                                var esTercerizada = Ext.getCmp('cmb_ESTERCERIZADA').value;
                                                                                var modelo = '';

                                                                                if (esTercerizada == "S") {
                                                                                    modelo = 'TERCERIZADO';
                                                                                }

                                                                                storeElementosRadio.proxy.extraParams = {nombre: '',
                                                                                    elemento: 'RADIO',
                                                                                    modelo: modelo,
                                                                                    tipoElementoRed: 'BACKBONE'};
                                                                                storeElementosRadio.load({params: {}});
                                                                            }
                                                                        }
                                                                    }
                                                                },
                                                                {
                                                                    xtype: 'combobox',
                                                                    id: 'cmb_TERCERIZADORA',
                                                                    name: 'cmb_TERCERIZADORA',
                                                                    fieldLabel: '* Tercerizadora',
                                                                    hidden: true,
                                                                    typeAhead: true,
                                                                    triggerAction: 'all',
                                                                    displayField: 'nombre_empresa_externa',
                                                                    queryMode: "local",
                                                                    valueField: 'id_empresa_externa',
                                                                    selectOnTab: true,
                                                                    store: storeTercerizadoras,
                                                                    lazyRender: true,
                                                                    listClass: 'x-combo-list-small',
                                                                    labelStyle: "color:red;",
                                                                    forceSelection: true,
                                                                    emptyText: 'Seleccione..',
                                                                    minChars: 3,
                                                                    width: '350px',
                                                                },
                                                                {
                                                                    xtype: 'combobox',
                                                                    id: 'cmb_RADIO',
                                                                    name: 'cmb_RADIO',
                                                                    fieldLabel: '* RADIO',
                                                                    hidden: false,
                                                                    typeAhead: true,
                                                                    triggerAction: 'all',
                                                                    displayField: 'nombreElemento',
                                                                    queryMode: "local",
                                                                    valueField: 'idElemento',
                                                                    selectOnTab: true,
                                                                    store: storeElementosRadio,
                                                                    lazyRender: true,
                                                                    listClass: 'x-combo-list-small',
                                                                    labelStyle: "color:red;",
                                                                    forceSelection: true,
                                                                    emptyText: 'Seleccione..',
                                                                    minChars: 3,
                                                                    width: '350px',
                                                                    listeners: {
                                                                        select: {fn: function (combo, value) {
                                                                                var idRadio = Ext.getCmp('cmb_RADIO').value;
                                                                                var nombreRadio = Ext.getCmp('cmb_RADIO').getRawValue();
                                                                                Ext.MessageBox.wait("Cargando datos de Red...");
                                                                                Ext.Ajax.request({
                                                                                    url: urlInfoSwitch,
                                                                                    method: 'post',
                                                                                    async: false,
                                                                                    timeout: 120000,
                                                                                    params: {
                                                                                        intIdElementoRadio: idRadio
                                                                                    },
                                                                                    success: function (response) {
                                                                                        Ext.MessageBox.close();
                                                                                        var infoSwitch = Ext.JSON.decode(response.responseText);

                                                                                        if (infoSwitch.error) {
                                                                                            Ext.getCmp('elementoPadreNuevo').setValue("");
                                                                                            Ext.getCmp('anilloNuevo').setValue("");
                                                                                            Ext.getCmp('elementoNuevo').setValue("");
                                                                                            Ext.getCmp('interfaceNueva').setValue("");
                                                                                            Ext.getCmp('elementoConectorNuevo').setValue("");
                                                                                            Ext.getCmp('idElementoPadreNuevo').setValue("");
                                                                                            Ext.getCmp('idAnilloNuevo').setValue("");
                                                                                            Ext.getCmp('idElementoNuevo').setValue("");
                                                                                            Ext.getCmp('idInterfaceNueva').setValue("");
                                                                                            Ext.getCmp('idElementoConectorNuevo').setValue("");
                                                                                            cierraVentanaIngFactUmRadio(winIngFactUmRadio);
                                                                                            Ext.MessageBox.show({
                                                                                                title: 'Error',
                                                                                                msg: infoSwitch.msg,
                                                                                                buttons: Ext.MessageBox.OK,
                                                                                                icon: Ext.MessageBox.ERROR
                                                                                            });
                                                                                        } else {
                                                                                            Ext.getCmp('elementoPadreNuevo').setValue(infoSwitch.nombrePe);
                                                                                            Ext.getCmp('anilloNuevo').setValue(infoSwitch.anilloPe);
                                                                                            Ext.getCmp('elementoNuevo').setValue(infoSwitch.nombreElemento);
                                                                                            Ext.getCmp('interfaceNueva').setValue(infoSwitch.linea);
                                                                                            Ext.getCmp('elementoConectorNuevo').setValue(nombreRadio);
                                                                                            Ext.getCmp('idElementoPadreNuevo').setValue(infoSwitch.idPe);
                                                                                            Ext.getCmp('idAnilloNuevo').setValue(infoSwitch.anilloPe);
                                                                                            Ext.getCmp('idElementoNuevo').setValue(infoSwitch.idElemento);
                                                                                            Ext.getCmp('idInterfaceNueva').setValue(infoSwitch.idLinea);
                                                                                            Ext.getCmp('idElementoConectorNuevo').setValue(idRadio);
                                                                                        }
                                                                                    },
                                                                                    failure: function (result)
                                                                                    {
                                                                                        Ext.MessageBox.close();
                                                                                        Ext.getCmp('elementoPadreNuevo').setValue("");
                                                                                        Ext.getCmp('anilloNuevo').setValue("");
                                                                                        Ext.getCmp('elementoNuevo').setValue("");
                                                                                        Ext.getCmp('interfaceNueva').setValue("");
                                                                                        Ext.getCmp('elementoConectorNuevo').setValue("");
                                                                                        Ext.getCmp('idElementoPadreNuevo').setValue("");
                                                                                        Ext.getCmp('idAnilloNuevo').setValue("");
                                                                                        Ext.getCmp('idElementoNuevo').setValue("");
                                                                                        Ext.getCmp('idInterfaceNueva').setValue("");
                                                                                        Ext.getCmp('idElementoConectorNuevo').setValue("");
                                                                                        cierraVentanaIngFactUmRadio(winIngFactUmRadio);
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
                                                                    }
                                                                }
                                                            ]
                                                        }
                                                    ]
                                        },
                                        {width: 10, border: false},
                                        {width: 200, border: false},
                                        {width: 200, border: false}
                                    ]
                                }
                            ]
                        }],
                    buttons: [{
                            text: 'Ejecutar',
                            formBind: true,
                            handler: function ()
                            {
                                var swAnterior = datosBackbone.idElemento;
                                var swNuevo = Ext.getCmp('idElementoNuevo').getValue();

                                var peAnterior = datosBackbone.idElementoPadre;
                                var peNuevo = Ext.getCmp('idElementoPadreNuevo').getValue();

                                var anilloAnt = datosBackbone.anillo;
                                var anilloNue = Ext.getCmp('anilloNuevo').getValue();

                                var esTercerizada = Ext.getCmp('cmb_ESTERCERIZADA').value;
                                var tercerizadora = Ext.getCmp('cmb_TERCERIZADORA').value;

                                var validarTercerizadora = "true";
                                var macRadioClien = "";
                                var validaMac = "true";


                                //Validar Tipo de Cambio UM a Realizar
                                var tipoCambioUM = "";

                                //Se verifica si es mismo SW
                                if (parseInt(swAnterior) === parseInt(swNuevo))
                                {
                                    tipoCambioUM = "MISMO_SWITCH";
                                }
                                //Se verifica si cambie de PE 
                                else if (parseInt(peAnterior) !== parseInt(peNuevo))
                                {
                                    tipoCambioUM = "DIFERENTE_PE";
                                } else
                                {
                                    //Se verifica si es mismo anillo mismo pe
                                    if (parseInt(anilloAnt) === parseInt(anilloNue))
                                    {
                                        tipoCambioUM = "MISMO_PE_MISMO_ANILLO";
                                    }
                                    //Se verifica si es diferente anillo mismo pe
                                    else
                                    {
                                        tipoCambioUM = "MISMO_PE_DIFERENTE_ANILLO";
                                    }
                                }

                                //if (datosBackbone.idSplitter == null)
                                if (Ext.isEmpty(datosBackbone.idSplitter))
                                {
                                    macRadioClien = Ext.getCmp('macRadioCliente').getValue();
                                    if (macRadioClien.match("[a-fA-f0-9]{4}[\.]+[a-fA-f0-9]{4}[\.]+[a-fA-F0-9]{4}$"))
                                    {
                                        validaMac = "true";
                                    } else
                                    {
                                        validaMac = "false";
                                    }
                                }

                                //valida terciarizadora
                                if (esTercerizada == "S" && !tercerizadora)
                                {
                                    validarTercerizadora = "false";
                                }

                                if (validaMac == "true" && validarTercerizadora == "true")
                                {
                                    Ext.get(formPanel.getId()).mask('Generando Factibilidad...');
                                    Ext.Ajax.request({
                                        url: url_ajaxGenerarFactibilidad,
                                        method: 'post',
                                        timeout: 400000,
                                        params: {
                                            idSolicitud: data.id_factibilidad,
                                            idSwitch: swNuevo,
                                            idInterface: Ext.getCmp('idInterfaceNueva').getValue(),
                                            idRadioBb: Ext.getCmp('idElementoConectorNuevo').getValue(),
                                            tipoCambio: tipoCambioUM,
                                            esTercerizada: esTercerizada,
                                            tercerizadora: tercerizadora,
                                            macRadioCli: macRadioClien
                                        },
                                        success: function (response) {
                                            Ext.get(formPanel.getId()).unmask();

                                            Ext.Msg.alert('Mensaje', response.responseText, function (btn) {
                                                if (btn == 'ok') {
                                                    store.load();
                                                    winIngFactUmRadio.destroy();
                                                }
                                            });
                                        },
                                        failure: function (result)
                                        {
                                            Ext.get(formPanel.getId()).unmask();
                                            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                                        }
                                    });
                                } else
                                {
                                    if (validaMac == "false")
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + "Mac incorrecta");
                                    }

                                    if (validarTercerizadora == "false")
                                    {
                                        Ext.Msg.alert('Error ', 'Error: ' + "Por favor escoger una Tercializadora.");
                                    }
                                }

                            }
                        }, {
                            text: 'Cancelar',
                            handler: function () {
                                winIngFactUmRadio.destroy();
                            }
                        }]
                });

                winIngFactUmRadio = Ext.create('Ext.window.Window', {
                    title: 'Factibilidad Ultima Milla Radio',
                    modal: true,
                    width: 900,
                    closable: true,
                    layout: 'fit',
                    items: [formPanel]
                }).show();

            }
        }//cierre response
    });
}

function cierraVentanaIngFactUmRadio(winIngFactUmRadio) {
    winIngFactUmRadio.close();
    winIngFactUmRadio.destroy();
}
