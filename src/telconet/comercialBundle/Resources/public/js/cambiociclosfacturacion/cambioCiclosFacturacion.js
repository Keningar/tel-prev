Ext.require([
                '*',
                'Ext.ux.grid.plugin.PagingSelectionPersistence',
                'Ext.tip.QuickTipManager',
                'Ext.window.MessageBox'               
            ]);
var strIdentificacion;
var strCliente;
var idCicloFacturacion;
var cbxIdEstadoServicio;
var cbxIdPtoCobertura;
var idFormaPago;
var strEsCuentaTarjeta;
var idsTipoCuenta;
var idsBancos;
var message;
var itemsPerPage = 500;
var store = '';

Ext.onReady(function() {
    itemsFor            = [];    
    itemsBancos         = [];
    itemsTipoCuenta     = [];

    Ext.tip.QuickTipManager.init();
    
    var objScope =
        {
            extraParams:
                {
                    strAppendDatos: 'Todos',
                    strDisponiblesPersona: ''
                }
        };

    // Punto de Cobertura - Jurisdicción
    objStorePtosCobertura = function(objScope) {
        modelPtosCobertura = Ext.define('modelPtosCoberturaByEmpresa', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'intIdObj', type: 'int'},
                {name: 'strDescripcionObj', type: 'string'}
            ]
        });
        return new Ext.create('Ext.data.Store', {
            id: objScope.id,
            model: modelPtosCobertura,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlGetPtosCoberturaByEmpresa,
                timeout: 99999,
                reader: {
                    type: 'json',
                    root: 'registros'
                },
                extraParams: objScope.extraParams,
                simpleSortMode: true
            }
        });
    };
    var objClienteA                         = new Cliente();
    var objStorePtosCoberturaByEmpresa      = objStorePtosCobertura(objScope);
    objStorePtosCoberturaByEmpresa.objStore = objStorePtosCoberturaByEmpresa;
    objStorePtosCoberturaByEmpresa.strIdObj = 'cbxIdPtoCobertura';
    objStorePtosCoberturaByEmpresa.intWidth = 450;

    var cbxPtoCobertura     = objClienteA.objComboMultiSelectDatos(objStorePtosCoberturaByEmpresa, 'Jurisdicción');
    cbxPtoCobertura.colspan = 4;
    cbxPtoCobertura.setWidth(400);
            
    //Tipo de Cuenta     
    storeTipoCuenta = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: urlGetTipoCuenta,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields: [{
                name: 'intIdTipoCuenta',
                mapping: 'intIdTipoCuenta'
            }, {
                name: 'strDescripcionCuenta',
                mapping: 'strDescripcionCuenta'
            }],
        listeners: {
            load: function(t, records, options) {
                frameTipoCuenta.removeAll();
                var i = 0;
                Ext.getCmp('panelTipoCuenta').setVisible(false);
                if (records.length > 0)
                {
                    if (records[0].data.strDescripcionCuenta != "")
                    {
                        for (var i = 0; i < records.length; i++)
                        {
                            var cb = Ext.create('Ext.form.field.Checkbox',
                                {
                                    boxLabel: records[i].data.strDescripcionCuenta,
                                    inputValue: records[i].data.intIdTipoCuenta,
                                    id: 'idTipoCuenta_' + i,
                                    name: 'tipoCuenta'
                                });
                            frameTipoCuenta.add(cb);
                            itemsTipoCuenta[i] = cb;
                            Ext.getCmp('panelTipoCuenta').setVisible(true);
                        }
                    }
                    Ext.MessageBox.hide();
                }
                else
                {
                    Ext.getCmp('panelTipoCuenta').setVisible(false);
                    Ext.MessageBox.hide();
                }
            }
        }
    });
        
    // Bancos   
    storeBancos = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: urlGetBancos,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields: [{
                name: 'intIdBanco',
                mapping: 'intIdBanco'
            }, {
                name: 'strDescripcionBanco',
                mapping: 'strDescripcionBanco'
            }],
        listeners: {
            load: function(t, records, options) {
                frameBancos.removeAll();
                var i = 0;
                Ext.getCmp('panelBancos').setVisible(false);
                if (records.length > 0)
                {
                    if (records[0].data.strDescripcionBanco != "")
                    {
                        for (var i = 0; i < records.length; i++)
                        {
                            var cb = Ext.create('Ext.form.field.Checkbox',
                                {
                                    boxLabel: records[i].data.strDescripcionBanco,
                                    inputValue: records[i].data.intIdBanco,
                                    id: 'idBanco_' + i,
                                    name: 'banco'
                                });
                            frameBancos.add(cb);
                            itemsBancos[i] = cb;
                            Ext.getCmp('panelBancos').setVisible(true);
                        }
                    }
                    Ext.MessageBox.hide();
                }
                else
                {
                    Ext.getCmp('panelBancos').setVisible(false);
                    Ext.MessageBox.hide();
                }
            }
        }
    });

    // Ciclos de Facturación
    var storeCicloFacturacion = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: urlGetCiclosFacturacion,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            },
            extraParams: { strTipo: 'Consulta'}
        },
        fields:
            [
                {name: 'intIdCiclo', mapping: 'intIdCiclo'},
                {name: 'strNombreCiclo', mapping: 'strNombreCiclo'}
            ],
        autoLoad: true
    });       
    
      // Ciclos de Facturación
    var storeCicloFacturacionNuevo = new Ext.data.Store({
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: urlGetCiclosFacturacion,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            },
            extraParams: { strTipo: 'Asignacion'}
        },
        fields:
            [
                {name: 'intIdCiclo', mapping: 'intIdCiclo'},
                {name: 'strNombreCiclo', mapping: 'strNombreCiclo'}
            ],
        autoLoad: true
    });       

    // Estado de Servicios
    objStoreEstadosServ = function(objScope) {
        modelEstadosServ = Ext.define('modelEstadosServByEmpresa', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'intIdObj', type: 'int'},
                {name: 'strDescripcionObj', type: 'string'}
            ]
        });
        return new Ext.create('Ext.data.Store', {
            id: objScope.id,
            model: modelEstadosServ,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: urlGetEstadosServCambioCiclo,
                timeout: 99999,
                reader: {
                    type: 'json',
                    root: 'registros'
                },
                extraParams: objScope.extraParams,
                simpleSortMode: true
            }
        });
    };
    var objClienteB              = new Cliente();
    var objStoreEstadosServ      = objStoreEstadosServ(objScope);
    objStoreEstadosServ.objStore = objStoreEstadosServ;
    objStoreEstadosServ.strIdObj = 'cbxIdEstadoServicio';
    objStoreEstadosServ.intWidth = 450;

    var cbxEstadoServicio     = objClienteB.objComboMultiSelectDatos(objStoreEstadosServ, 'Estados Servicios:');
    cbxEstadoServicio.colspan = 4;
    cbxEstadoServicio.setWidth(400);    
    
    //Listado Model
    Ext.define('ListaDetalleModel', {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name: 'intIdServicio', type: 'int'},
            {name: 'intIdPersonaRol', type: 'int'},
            {name: 'strIdentificacion', type: 'string'},
            {name: 'strNombreCliente', type: 'string'},
            {name: 'strLogin', type: 'string'},
            {name: 'strFormaPago', type: 'string'},
            {name: 'strDescripcionCuenta', type: 'string'},
            {name: 'strDescripcionBanco', type: 'string'},
            {name: 'fltValorRecurrente', type: 'float'},
            {name: 'fltSaldoDeudor', type: 'float'},
            {name: 'strNombreCiclo', type: 'string'},
            {name: 'strEstadoServ', type: 'string'},
            {name: 'strJurisdiccion', type: 'string'},
            {name: 'strPlanProducto', type: 'string'}
        ],
        idProperty: 'intIdServicio'
    });
    
     //Store de Clientes a ejecutar Cambio de Ciclo
     store = new Ext.data.Store({ 
        pageSize: itemsPerPage, 
        model: 'ListaDetalleModel',        
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 99999999999,
            method: 'post',                            
            url: urlGetClientesACambiarCicloFact,
            reader: {
                type: 'json',
                root: 'clientes',
                totalProperty: 'total'
            },
            extraParams: { strIdentificacion: '', strCliente: '', idCicloFacturacion: '',cbxIdEstadoServicio: '',
                           cbxIdPtoCobertura: '',idFormaPago: '', strEsCuentaTarjeta: '', idsTipoCuenta: '', idsBancos: '' },
            simpleSortMode: true
        },       
        listeners: {
            load: function(store, records, success) {
                if (message != null)
                {
                    Ext.MessageBox.hide();                   
                }                
            }
        }
    });
    var pluginExpanded               = true;  
    var AsignacionNuevoCicloBtn      = "";
    var AsignacionNuevoCicloTodosBtn = "";
    var ExportarExcelBtn             = "";
    sm = Ext.create('Ext.selection.CheckboxModel', {
        checkOnly: true
    })
    AsignacionNuevoCicloBtn = Ext.create('Ext.button.Button', {
        iconCls: 'icon_check',
        text: 'Asignacion Especifica de Ciclo',
        scope: this,
        handler: function() {
            AsignacionNuevoCiclo();
        }
    });
    AsignacionNuevoCicloTodosBtn = Ext.create('Ext.button.Button', {
        iconCls: 'icon_add',
        text: 'Asignacion de Ciclo a Todos',
        scope: this,
        handler: function() {
            AsignacionNuevoCicloTodos();
        }
    });
    ExportarExcelBtn = Ext.create('Ext.button.Button', {
        iconCls: 'icon_exportar',
        text: 'Generar-Enviar CSV',
        scope: this,
        handler: function() {
            ExportarExcelConsulta();
        }
    });
     var toolbar = Ext.create('Ext.toolbar.Toolbar', {
        dock: 'top',
        align: '->',
        items:
            [   {
                    xtype: 'combo',
                    fieldLabel: '* Nuevo Ciclo: ',
                    id: 'idCicloFacturacionNuevo',
                    name: 'idCicloFacturacionNuevo',
                    displayField: 'strNombreCiclo',
                    valueField: 'intIdCiclo',
                    emptyText: 'Seleccione...',
                    labelStyle: 'text-align:left;',
                    multiSelect: false,
                    queryMode: 'local',
                    store: storeCicloFacturacionNuevo,
                },      
                {
                    iconCls: 'icon_add',
                    text: 'Seleccionar Todos',
                    itemId: 'select',
                    scope: this,
                    handler: function() {
                        Ext.getCmp('listView').getPlugin('pagingSelectionPersistence').selectAll()
                    }
                },
                {
                    iconCls: 'icon_limpiar',
                    text: 'Desmarque Todos',
                    itemId: 'clear',
                    scope: this,
                    handler: function() {
                        Ext.getCmp('listView').getPlugin('pagingSelectionPersistence').clearPersistedSelection();
                    }
                },
                {xtype: 'tbfill'},
                ExportarExcelBtn,
                AsignacionNuevoCicloTodosBtn,                
                AsignacionNuevoCicloBtn
            ]
    });
    
    //Frame Tipo de Cuenta
    var frameTipoCuenta = new Ext.form.CheckboxGroup({
        id: 'frameTipoCuenta',
        flex: 4,
        vertical: true,
        align: 'left',
        columns: 1,                                        
        listeners: {
            change: function(field, newValue, oldValue) {               
                strEsCuentaTarjetaSelected ='';
                boolEsCuentaTarjetaSelected = Ext.getCmp('strEsCuentaTarjeta_1').value;
                if (boolEsCuentaTarjetaSelected == true)
                {
                    strEsCuentaTarjetaSelected = Ext.getCmp('strEsCuentaTarjeta_1').inputValue;
                }
                else
                {
                    boolEsCuentaTarjetaSelected = Ext.getCmp('strEsCuentaTarjeta_2').value;
                    if (boolEsCuentaTarjetaSelected == true)
                    {
                        strEsCuentaTarjetaSelected = Ext.getCmp('strEsCuentaTarjeta_2').inputValue;
                    }
                }                                   
                if (strEsCuentaTarjetaSelected != 'Tarjeta')
                {

                    var idTipoCuentaSelected = "";
                    for (var i = 0; i < itemsTipoCuenta.length; i++)
                    {
                        TipoCuentaSeteada = Ext.getCmp('idTipoCuenta_' + i).value;
                        if (TipoCuentaSeteada == true)
                        {
                            if (i > 0)
                            {
                                idTipoCuentaSelected = idTipoCuentaSelected + ',';
                            }
                            idTipoCuentaSelected = idTipoCuentaSelected + Ext.getCmp('idTipoCuenta_' + i).inputValue;
                        }
                    }
                    storeBancos.getProxy()
                        .extraParams.idTipoCuentaSelected = idTipoCuentaSelected;
                    storeBancos.removeAll();
                    storeBancos.load();
                    itemsBancos = [];
                    message = Ext.MessageBox
                        .show({
                            title: 'Favor espere',
                            msg: 'Procesando...',
                            closable: false,
                            progressText: 'Guardando...',
                            width: 300,
                            wait: true,
                            waitConfig: {
                                interval: 200
                            }
                        });
                }
                
            }
        }        
    });
    
    var frameBancos = new Ext.form.CheckboxGroup({
        id: 'frameBancos',
        flex: 4,
        vertical: true,
        align: 'left',          
        columns: 2       
    });
           
    Ext.Ajax.request({
            url: urlGetFormasPagoParaContrato,
            method: 'post',
            success: function(response)
            {
                var formaPago = response.responseText;
                var form      = Ext.JSON.decode(formaPago);
                for (var i = 0; i < form.total; i++)
                {
                    var forma   = form.encontrados[i].strDescripcionFormaPago;
                    var idForma = form.encontrados[i].intIdFormaPago;

                    itemsFor[i] = new Ext.form.Radio({
                        boxLabel: forma,
                        id: 'idForma_' + i,
                        name: 'forma',
                        inputValue: idForma
                    });
                }

                var panel = Ext.create('Ext.panel.Panel',
                {
                    bodyPadding: 7,
                    layout: 'anchor',
                    buttonAlign: 'center',
                    collapsible: true,
                    collapsed: false,
                    width: '1500px',
                    title: 'Criterios de B\xfaqueda',
                    buttons: [{
                            text: 'Buscar',
                            iconCls: "icon_search",
                            handler: function() {
                                buscar();
                            }
                        }, {
                            text: 'Limpiar',
                            iconCls: "icon_limpiar",
                            handler: function() {
                                limpiar();
                            }
                        }],
                    items: [
                        {
                            xtype: 'textfield',
                            hideTrigger: true,
                            fieldLabel: 'Identificación',
                            id: 'strIdentificacion',
                            name: 'strIdentificacion',
                            maxLength: 20,
                            width: 300, 
                            enforceMaxLength: true,
                            labelStyle: 'text-align:left;'
                        },
                        {
                            xtype: 'textfield',
                            hideTrigger: true,
                            fieldLabel: 'Cliente',
                            id: 'strCliente',
                            name: 'strCliente',
                            maxLength: 200,
                            width: 300, 
                            enforceMaxLength: true,
                            labelStyle: 'text-align:left;'
                        },
                        {
                            xtype: 'combo',
                            fieldLabel: '* Ciclo de Facturación',
                            id: 'idCicloFacturacion',
                            name: 'idCicloFacturacion',
                            displayField: 'strNombreCiclo',
                            valueField: 'intIdCiclo',
                            emptyText: 'Seleccione...',
                            labelStyle: 'text-align:left;',
                            multiSelect: false,
                            queryMode: 'local',
                            store: storeCicloFacturacion,
                        },                                               
                        cbxEstadoServicio,
                        cbxPtoCobertura,                           
                        {
                            xtype: 'container',
                            layout: 'hbox',
                            items: [
                                {
                                    xtype: 'fieldset',
                                    width: 200,
                                    title: 'Forma Pago',
                                    collapsible: false,
                                    collapsed: false,
                                    items: [{
                                            xtype: 'radiogroup',
                                            columns: 1,
                                            vertical: true,
                                            align: 'left',
                                            items: itemsFor,
                                            listeners: {
                                                change: function(field, newValue, oldValue) {
                                                    if(newValue.forma == 3)
                                                    {
                                                        Ext.getCmp('panelEsCuentaTarjeta').setVisible(true);
                                                    }
                                                    else
                                                    {
                                                        Ext.getCmp('strEsCuentaTarjeta_1').reset();
                                                        Ext.getCmp('strEsCuentaTarjeta_2').reset();
                                                        for (var i = 0; i < itemsTipoCuenta.length; i++)
                                                        {
                                                            Ext.getCmp('idTipoCuenta_' + i).setValue(false);
                                                        }
                                                        for (var i = 0; i < itemsBancos.length; i++)
                                                        {
                                                            Ext.getCmp('idBanco_' + i).setValue(false);
                                                        }
                                                        Ext.getCmp('panelEsCuentaTarjeta').setVisible(false);
                                                        Ext.getCmp('panelTipoCuenta').setVisible(false);
                                                        Ext.getCmp('panelBancos').setVisible(false);                                                        
                                                    }                                                    
                                                }
                                            }
                                        }]
                                },
                                {
                                    xtype: 'component',
                                    width: 10
                                },    
                                {
                                    id: 'panelEsCuentaTarjeta',
                                    name: 'panelEsCuentaTarjeta',
                                    xtype: 'fieldset',
                                    title: 'Cuenta /Tarjeta',
                                    width: 150,
                                    collapsible: false,
                                    collapsed: false,
                                    items: [{
                                            xtype: 'radiogroup',                                            
                                            fieldLabel: '',
                                            columns: 1,
                                            vertical: true,                                            
                                            items: [
                                                {boxLabel: 'Tarjeta', id: 'strEsCuentaTarjeta_1', 
                                                    name: 'strEsCuentaTarjeta', inputValue: 'Tarjeta'},
                                                {boxLabel: 'Cuenta Bancaria', id: 'strEsCuentaTarjeta_2',
                                                    name: 'strEsCuentaTarjeta', inputValue: 'Cuenta'}
                                            ],
                                            listeners: {
                                                change: function(field, newValue, oldValue) {                                                   
                                                    if ( newValue.strEsCuentaTarjeta )
                                                    {
                                                        for (var i = 0; i < itemsTipoCuenta.length; i++)
                                                        {
                                                            Ext.getCmp('idTipoCuenta_' + i).setValue(false);
                                                        }
                                                        for (var i = 0; i < itemsBancos.length; i++)
                                                        {
                                                            Ext.getCmp('idBanco_' + i).setValue(false);
                                                        }
                                                        Ext.getCmp('panelBancos').setVisible(false);
                                                        storeTipoCuenta.getProxy()
                                                            .extraParams.strEsCuentaTarjetaSelected = newValue.strEsCuentaTarjeta;
                                                        storeTipoCuenta.removeAll();
                                                        storeTipoCuenta.load();
                                                        itemsTipoCuenta = [];
                                                        message = Ext.MessageBox
                                                            .show({
                                                                title: 'Favor espere',
                                                                msg: 'Procesando...',
                                                                closable: false,
                                                                progressText: 'Guardando...',
                                                                width: 300,
                                                                wait: true,
                                                                waitConfig: {
                                                                    interval: 200
                                                                }
                                                            });
                                                    }
                                                }
                                            }
                                        }],
                                },
                                {
                                    xtype: 'component',
                                    width: 10
                                },                               
                                {
                                    id: 'panelTipoCuenta',
                                    name: 'panelTipoCuenta',
                                    xtype: 'fieldset',
                                    title: 'Tipos de Cuenta',
                                    width: 350,
                                    collapsible: false,
                                    collapsed: false,                                    
                                    items: [
                                        frameTipoCuenta,
                                        {
                                            xtype: 'panel',
                                            buttonAlign: 'right',
                                            bbar: [
                                                {
                                                    text: 'Select All',
                                                    handler: function() {
                                                        for (var i = 0; i < itemsTipoCuenta.length; i++)
                                                        {
                                                            Ext.getCmp('idTipoCuenta_' + i).setValue(true);
                                                        }
                                                    }
                                                },
                                                '-',
                                                {
                                                    text: 'Deselect All',
                                                    handler: function() {
                                                        for (var i = 0; i < itemsTipoCuenta.length; i++)
                                                        {
                                                            Ext.getCmp('idTipoCuenta_' + i).setValue(false);
                                                        }
                                                    }
                                                }],                                           
                                        }],                                    
                                },                                
                                {
                                    xtype: 'component',
                                    width: 10
                                },                               
                                {
                                    id: 'panelBancos',
                                    name: 'panelBancos',
                                    xtype: 'fieldset',
                                    title: 'Bancos',
                                    width: 700,
                                    collapsible: false,
                                    collapsed: false,                                    
                                    items: [
                                        frameBancos,
                                        {
                                            xtype: 'panel',
                                            buttonAlign: 'right',
                                            bbar: [
                                                {
                                                    text: 'Select All',
                                                    handler: function() {
                                                        for (var i = 0; i < itemsBancos.length; i++)
                                                        {
                                                            Ext.getCmp('idBanco_' + i).setValue(true);
                                                        }
                                                    }
                                                },
                                                '-',
                                                {
                                                    text: 'Deselect All',
                                                    handler: function() {
                                                        for (var i = 0; i < itemsBancos.length; i++)
                                                        {
                                                            Ext.getCmp('idBanco_' + i).setValue(false);
                                                        }
                                                    }
                                                }]
                                        }]
                                }]
                        }],
                    renderTo: 'filtro'
                });
                Ext.getCmp('panelBancos').setVisible(false);
                Ext.getCmp('panelEsCuentaTarjeta').setVisible(false);
                Ext.getCmp('panelTipoCuenta').setVisible(false);
                Ext.EventManager.onWindowResize(function() {               
                    panel.doComponentLayout();
                });
            },
            failure: function(result) {
                Ext.Msg.alert('Error ', 'Error: '+ result.statusText);
            }
        });
                
        var listView = Ext.create('Ext.grid.Panel', {
        id: 'listView',
        width : '1300px',
        height : 500,
        title: 'Listado de Servicios',
        store: store,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: false
        },
        dockedItems: [toolbar],
        columns: [
           {
                text: 'Identificación',
                width: 130,
                dataIndex: 'strIdentificacion'
            }, {
                text: 'Cliente Nombre',
                width: 280,
                dataIndex: 'strNombreCliente'           
            }, {
                text: 'Login',
                width: 130,
                dataIndex: 'strLogin'
            }, {
                text: 'Forma Pago',
                dataIndex: 'strFormaPago',
                align: 'center',
                width: 150
            }, {
                text: 'Desc. Cuenta',
                dataIndex: 'strDescripcionCuenta',
                align: 'center',
                width: 150
            }, {
                text: 'Desc. Banco',
                dataIndex: 'strDescripcionBanco',
                align: 'center',
                width: 150
            }, {
                text: 'Valor Recurrente Fact.',
                dataIndex: 'fltValorRecurrente',
                align: 'center',
                width: 100
            }, {
                text: 'Saldo Deudor',
                dataIndex: 'fltSaldoDeudor',
                align: 'center',
                width: 100
            }, {
                text: 'Ciclo Fact.',
                dataIndex: 'strNombreCiclo',
                align: 'center',
                width: 120
             }, {
                text: 'Estado Serv.',
                dataIndex: 'strEstadoServ',
                align: 'center',
                width: 100        
            }, {
                text: 'Jurisdicción',
                dataIndex: 'strJurisdiccion',
                align: 'center',
                width: 120   
            }, {
                text: 'Plan/Producto',
                dataIndex: 'strPlanProducto',
                align: 'center',
                width: 120       
            }],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: '{0} - {1} de {2} registros encontrados &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            emptyMsg: "No hay datos para mostrar",
            align:'center'
        }),
        renderTo: 'listView'
    });
             
});

function buscar() 
{   
    Ext.getCmp('listView').getPlugin('pagingSelectionPersistence').clearPersistedSelection();
    if (Ext.getCmp('idCicloFacturacion').getValue() == null)
    {
        Ext.Msg.show({
            title: 'Error en B\u00fasqueda',
            msg: 'El Ciclo de Facturación es campo Obligatorio ',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
        return false;
    }
    message = Ext.MessageBox.show({
        title: 'Favor espere',
        msg: 'Procesando...',
        closable: false,
        progressText: 'Saving...',
        width: 300,
        wait: true,
        waitConfig: {
            interval: 200
        }
    });    
    
    var idFormaPago         = "";      
    var strEsCuentaTarjeta  = "";
    var idsTiposCuenta      = "";
    var idsBancos           = "";
    
    idFormaPago             = getFormaPago();
    strEsCuentaTarjeta      = getEsCuentaTarjeta();
    idsTiposCuenta          = getTipoCuenta();
    idsBancos               = getBancoTarjeta();    
    
    var strIdentificacion   = Ext.getCmp('strIdentificacion').getValue();    
    var strCliente          = Ext.getCmp('strCliente').getValue();
    var idCicloFacturacion  = Ext.getCmp('idCicloFacturacion').getValue();
    var cbxIdPtoCobertura   = Ext.getCmp('cbxIdPtoCobertura').getValue().toString();
    var cbxIdEstadoServicio = Ext.getCmp('cbxIdEstadoServicio').getValue().toString();    
    cbxIdEstadoServicio     = limpiaEstado(cbxIdEstadoServicio);
            
    // ... Recargamos el store del grid con los filtros ... //
    store.getProxy().extraParams.strIdentificacion    = strIdentificacion;
    store.getProxy().extraParams.strCliente           = strCliente;
    store.getProxy().extraParams.idCicloFacturacion   = idCicloFacturacion;
    store.getProxy().extraParams.cbxIdEstadoServicio  = cbxIdEstadoServicio;
    store.getProxy().extraParams.cbxIdPtoCobertura    = cbxIdPtoCobertura;
    store.getProxy().extraParams.idFormaPago          = idFormaPago;
    store.getProxy().extraParams.strEsCuentaTarjeta   = strEsCuentaTarjeta;          
    store.getProxy().extraParams.idsTipoCuenta        = idsTiposCuenta;
    store.getProxy().extraParams.idsBancos            = idsBancos;
    
    store.removeAll();
    store.load();
}

function limpiar() 
{
	sm.deselectAll();
	Ext.getCmp('strIdentificacion').reset();
	Ext.getCmp('strCliente').reset();
    Ext.getCmp('idCicloFacturacion').reset();
	Ext.getCmp('cbxIdEstadoServicio').reset();
    Ext.getCmp('cbxIdPtoCobertura').reset();
    	
	for (var i = 0; i < itemsFor.length; i++)
    {
		formaSeteada = Ext.getCmp('idForma_' + i).setValue(false);
	}
    
    Ext.getCmp('strEsCuentaTarjeta_1').reset();
    Ext.getCmp('strEsCuentaTarjeta_2').reset();
    
    for (var i = 0; i < itemsTipoCuenta.length; i++)
    {
        Ext.getCmp('idTipoCuenta_' + i).setValue(false);
    }
    
    for (var i = 0; i < itemsBancos.length; i++)
    {
        Ext.getCmp('idBanco_' + i).setValue(false);
    }
    Ext.getCmp('panelEsCuentaTarjeta').setVisible(false);
    Ext.getCmp('panelTipoCuenta').setVisible(false);
    Ext.getCmp('panelBancos').setVisible(false);
    
    if (message != null) {
		Ext.MessageBox.hide();
	}
    store.removeAll();
}

function eliminaDuplicados(selection)
{
    var arreglo     = [];
    var obj         = {};
    var out         = [];
    for (var i = 0; i < selection.length; ++i)
    {
        arreglo[i]      = selection[i].data.intIdPersonaRol;
        obj[arreglo[i]] = 0;
    }
    for (i in obj) 
    {
        out.push(i);
    }
    return out;
}
function getFormaPago()
{
    //Obtengo Forma de Pago    
    var idFormaPagoSelected = "";
    for (var i = 0; i < itemsFor.length; i++)
    {
        formaSeteada = Ext.getCmp('idForma_' + i).value;
        if (formaSeteada == true) 
        {
            if (idFormaPagoSelected != null && idFormaPagoSelected == "")
            {
                idFormaPagoSelected = idFormaPagoSelected + Ext.getCmp('idForma_' + i).inputValue;
            }
        }
    }
    return idFormaPagoSelected;
}

function getEsCuentaTarjeta()
{
    //Obtengo Si es cuenta o Tarjeta   
    var strEsCuentaTarjetaSelected = "";
    boolEsCuentaTarjetaSelected = Ext.getCmp('strEsCuentaTarjeta_1').value;
    if (boolEsCuentaTarjetaSelected == true)
    {
        strEsCuentaTarjetaSelected = Ext.getCmp('strEsCuentaTarjeta_1').inputValue;
    }
    else
    {
        boolEsCuentaTarjetaSelected = Ext.getCmp('strEsCuentaTarjeta_2').value;
        if (boolEsCuentaTarjetaSelected == true)
        {
            strEsCuentaTarjetaSelected = Ext.getCmp('strEsCuentaTarjeta_2').inputValue;
        }
    } 
    return strEsCuentaTarjetaSelected;
}

function getTipoCuenta()
{
    //Obtengo Tipo de cuenta
    var idsTipoCuentaSelected = "";
    for (var i = 0; i < itemsTipoCuenta.length; i++) 
    {
        tipoCuentaSeteada = Ext.getCmp('idTipoCuenta_' + i).value;
        if (tipoCuentaSeteada == true) 
        {
            if (idsTipoCuentaSelected != null && idsTipoCuentaSelected == "")
            {
                idsTipoCuentaSelected = idsTipoCuentaSelected + Ext.getCmp('idTipoCuenta_' + i).inputValue;
            } 
            else 
            {
                idsTipoCuentaSelected = idsTipoCuentaSelected + ",";
                idsTipoCuentaSelected = idsTipoCuentaSelected + Ext.getCmp('idTipoCuenta_' + i).inputValue;
            }
        }
    }
    return idsTipoCuentaSelected;
}

function getBancoTarjeta()
{
    //Obtengo Bancos
    var idsBancosSelected = "";
    for (var i = 0; i < itemsBancos.length; i++) 
    {
        bancosTarjetasSeteada = Ext.getCmp('idBanco_' + i).value;
        if (bancosTarjetasSeteada == true)
        {
            if (idsBancosSelected != null && idsBancosSelected == "") 
            {
                idsBancosSelected = idsBancosSelected + Ext.getCmp('idBanco_' + i).inputValue;
            } 
            else
            {
                idsBancosSelected = idsBancosSelected + ",";
                idsBancosSelected = idsBancosSelected + Ext.getCmp('idBanco_' + i).inputValue;
            }
        }
    }
    return idsBancosSelected;
}

function ExportarExcelConsulta()
{
    var idFormaPago = "";
    var strEsCuentaTarjeta = "";
    var idsTiposCuenta = "";
    var idsBancos = "";

    idFormaPago = getFormaPago();
    strEsCuentaTarjeta = getEsCuentaTarjeta();
    idsTiposCuenta = getTipoCuenta();
    idsBancos = getBancoTarjeta();

    var strIdentificacion   = Ext.getCmp('strIdentificacion').getValue();
    var strCliente          = Ext.getCmp('strCliente').getValue();
    var idCicloFacturacion  = Ext.getCmp('idCicloFacturacion').getValue();
    var cbxIdPtoCobertura   = Ext.getCmp('cbxIdPtoCobertura').getValue().toString();
    var cbxIdEstadoServicio = Ext.getCmp('cbxIdEstadoServicio').getValue().toString();
    cbxIdEstadoServicio     = limpiaEstado(cbxIdEstadoServicio);

    //Valido que filtro o criterio de Ciclo de Facturacion sea Obligatorio
    if (Ext.getCmp('idCicloFacturacion').getValue() == null)
    {
        Ext.Msg.show({
            title: 'Error en Asignar Ciclo',
            msg: 'El Criterio Ciclo de Facturación es campo Obligatorio ',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
        return false;
    }
    Ext.MessageBox.wait('Generando Reporte. Favor espere..');
    Ext.Ajax
        .request({
            url: urlGenerarRptCambioCiclo,
            method: 'post',
            timeout: 9999999999,
            params: {                
                strIdentificacion: strIdentificacion,
                strCliente: strCliente,
                idCicloFacturacion: idCicloFacturacion,
                cbxIdEstadoServicio: cbxIdEstadoServicio,
                cbxIdPtoCobertura: cbxIdPtoCobertura,
                idFormaPago: idFormaPago,
                strEsCuentaTarjeta: strEsCuentaTarjeta,
                idsTipoCuenta: idsTiposCuenta,
                idsBancos: idsBancos
            },
            success: function(response) {
                store.removeAll();
                Ext.MessageBox.hide();
                Ext.Msg
                    .alert(
                        'Alerta',
                        response.responseText);
                Ext.getCmp('listView').getPlugin('pagingSelectionPersistence').clearPersistedSelection();
            },
            failure: function(response) {
                Ext.Msg.alert('Error ', 'Error: ' + response.responseText);
            }
        });
}

function AsignacionNuevoCicloTodos()
{    
    var idFormaPago         = "";           
    var strEsCuentaTarjeta  = "";
    var idsTiposCuenta      = "";
    var idsBancos           = "";
    
    idFormaPago             = getFormaPago();
    strEsCuentaTarjeta      = getEsCuentaTarjeta();
    idsTiposCuenta          = getTipoCuenta();
    idsBancos               = getBancoTarjeta();
    
    var strIdentificacion   = Ext.getCmp('strIdentificacion').getValue();    
    var strCliente          = Ext.getCmp('strCliente').getValue();
    var idCicloFacturacion  = Ext.getCmp('idCicloFacturacion').getValue();
    var cbxIdPtoCobertura   = Ext.getCmp('cbxIdPtoCobertura').getValue().toString();
    var cbxIdEstadoServicio = Ext.getCmp('cbxIdEstadoServicio').getValue().toString();       
    cbxIdEstadoServicio     = limpiaEstado(cbxIdEstadoServicio);
    
     //Valido que filtro o criterio de Ciclo de Facturacion sea Obligatorio
    if (Ext.getCmp('idCicloFacturacion').getValue() == null)
    {
        Ext.Msg.show({
            title: 'Error en Asignar Ciclo',
            msg: 'El Criterio Ciclo de Facturación es campo Obligatorio ',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
        return false;
    }     
    
    var idCicloFacturacionNuevo = Ext.getCmp('idCicloFacturacionNuevo').getValue();
    //Valido que el nuevo Ciclo de Facturacion a asignar o cambiar sea Obligatorio
    if (idCicloFacturacionNuevo == null)
    {
        Ext.Msg.show({
            title: 'Error en Asignar Ciclo',
            msg: 'El Nuevo Ciclo de Facturación es campo Obligatorio ',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
        return false;
    }
    //Valido que no se permita asignar el mismo Ciclo de Facturacion .
    if (idCicloFacturacionNuevo == idCicloFacturacion)
    {
        Ext.Msg.show({
            title: 'Error en Asignar Ciclo',
            msg: 'No puede asignarse el mismo Ciclo de Facturación',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
        return false;
    }
    
    Ext.Msg.confirm('Alerta', 'Se asignará el nuevo Ciclo a los Clientes que correspondan a filtros seleccionados. \n\
                         Desea continuar?',
        function(btn) {
            if (btn == 'yes')
            {
                message = Ext.MessageBox.show({
                    title: 'Favor espere',
                    msg: 'Procesando...',
                    progressText: 'Saving...',
                    width: 300,
                    wait: true,
                    closable: false,
                    waitConfig: {
                        interval: 200
                    }
                });    
                Ext.Ajax
                    .request({
                        url: urlGetAsignarCicloFactTodos,
                        method: 'post',
                        timeout: 9999999999,
                        params: {
                            idCicloFacturacionNuevo: idCicloFacturacionNuevo,
                            strIdentificacion : strIdentificacion,
                            strCliente : strCliente,
                            idCicloFacturacion : idCicloFacturacion,
                            cbxIdEstadoServicio : cbxIdEstadoServicio,                            
                            cbxIdPtoCobertura: cbxIdPtoCobertura,
                            idFormaPago : idFormaPago,
                            strEsCuentaTarjeta : strEsCuentaTarjeta,                            
                            idsTipoCuenta: idsTiposCuenta,
                            idsBancos: idsBancos
                        },
                        success: function(response) {
                            store.removeAll();
                            Ext.MessageBox.hide();
                            Ext.Msg
                                .alert(
                                    'Alerta',
                                    response.responseText);
                            Ext.getCmp('listView').getPlugin('pagingSelectionPersistence').clearPersistedSelection();
                        },
                        failure: function(response) {
                            Ext.Msg.alert('Error ', 'Error: ' + response.responseText);
                        }
                    });
            }
        });
                    
}
function AsignacionNuevoCiclo()
{    
    var param                   = '';
    var selection               = Ext.getCmp('listView').getPlugin('pagingSelectionPersistence').getPersistedSelection();
    var idCicloFacturacionNuevo = Ext.getCmp('idCicloFacturacionNuevo').getValue();
    var idCicloFacturacion      = Ext.getCmp('idCicloFacturacion').getValue();
    var cbxIdPtoCobertura       = Ext.getCmp('cbxIdPtoCobertura').getValue().toString();
    var arrayselection          = [];
    //Elimino Duplicados para generar un array de solo Ids de Clientes (ID_PERSONA_ROL)
    arrayselection              = eliminaDuplicados(selection);    
    
    if (idCicloFacturacionNuevo == null)
    {
        Ext.Msg.show({
            title: 'Error en Asignar Ciclo',
            msg: 'El Nuevo Ciclo de Facturación es campo Obligatorio ',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
        return false;
    }
    
    if (idCicloFacturacionNuevo == idCicloFacturacion)
    {
        Ext.Msg.show({
            title: 'Error en Asignar Ciclo',
            msg: 'No puede asignarse el mismo Ciclo de Facturación',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
        return false;
    }
    
    if (intCantidaRegProcesa==0)
    {
        Ext.Msg.show({
            title: 'Error en Asignar Ciclo',
            msg: 'No existe parámetro [CANTIDAD_PROCESA_PMA_CAMBIOCICLO] que define la Cantidad de Registros máximos por Proceso',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
        return false;
    }
    
    if (arrayselection.length > 0)
    {                        
        //Particiono el array seleccionado en grupos de 1000 registros si fuese el caso      
        var registros = 0;
        var arreglo   = [];
        var indice    = 0;  
        for (var i = 0; i < arrayselection.length; ++i)
        {            
            param     = param + arrayselection[i];
            registros = registros +1;
            
            if (i < (arrayselection.length - 1))
            {   
                if(registros < 1000)
                {                                    
                    param = param + ',';
                }                
            }
            if (i == (arrayselection.length - 1) || registros == 1000)
            {                
                arreglo[indice] = param;                
                indice          = indice+1;
                registros       = 0;
                param           = '';               
            }
        }
        
        Ext.Msg.confirm('Alerta', 'Se asignará el nuevo Ciclo a los registros seleccionados en total :[' + arrayselection.length + '] cliente(s). \n\
                         Desea continuar?',
            function(btn) {
                if (btn == 'yes')
                {
                    message = Ext.MessageBox.show({
                        title: 'Favor espere',
                        msg: 'Procesando...',
                        progressText: 'Saving...',
                        width: 300,
                        wait: true,
                        closable: false,
                        waitConfig: {
                            interval: 200
                        }
                    });
                    //Recorro array de Grupos de Ids de Clientes a procesar y realizo la asignación del nuevo ciclos de Facturacion 
                    for (var key in arreglo)
                    {
                        (function(key) {
                            Ext.Ajax
                                .request({
                                    url: urlGetAsignarCicloFact,
                                    method: 'post',
                                    timeout: 9999999999,
                                    params: {
                                        idsPersonaRol: arreglo[key], idCicloFacturacionNuevo: idCicloFacturacionNuevo,
                                        cbxIdPtoCobertura: cbxIdPtoCobertura
                                    },
                                    success: function(response) {
                                        store.removeAll();
                                        Ext.MessageBox.hide();
                                        Ext.Msg
                                            .alert(
                                                'Alerta',
                                                response.responseText);
                                        Ext.getCmp('listView').getPlugin('pagingSelectionPersistence').clearPersistedSelection();
                                    },
                                    failure: function(response) {
                                        Ext.Msg.alert('Error ', 'Error: ' + response.responseText);
                                    }
                                });

                        })(key);
                    }
                    //fin for
                }
            });
    }
    else
    {
         Ext.Msg.show({
            title: 'Error en Asignar Ciclo',
            msg: 'Seleccione por lo menos un registro de la lista ',
            buttons: Ext.Msg.OK,
            animEl: 'elId',
            icon: Ext.MessageBox.ERROR
        });
        return false;        
    }   
    
}

function limpiaEstado(strIdEstados) {
    if (strIdEstados.indexOf("0,") === 0) {
        strIdEstados = strIdEstados.substring(2, strIdEstados.length);
    }
    return strIdEstados;
}