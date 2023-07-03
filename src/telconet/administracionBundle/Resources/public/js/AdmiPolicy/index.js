/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.require([
	'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);
	
Ext.onReady(function() { 
    Ext.tip.QuickTipManager.init();
    
         
    Ext.define('ModelStore', {
        extend: 'Ext.data.Model',
        fields:
            [
                {name: 'id_policy', mapping: 'idPolicy'},
                {name: 'nombrePolicy', mapping: 'nombrePolicy'},
                {name: 'leaseTime', mapping: 'leaseTime'},
                {name: 'mascara', mapping: 'mascara'},
                {name: 'gateway', mapping: 'gateway'},
                {name: 'dnsName', mapping: 'dnsName'},
                {name: 'dnsServers', mapping: 'dnsServers'},
                {name: 'estado', mapping: 'estado'},
                {name: 'action1', mapping: 'action1'},
                {name: 'action2', mapping: 'action2'}
            ],
        idProperty: 'id_policy'
    });
	
    store = new Ext.data.Store({
        pageSize: 10,
        model: 'ModelStore',
        total: 'total',
        proxy: {
            type: 'ajax',
            timeout: 600000,
            url: url_gridPolicy,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Todos'
            }
        },
        autoLoad: true
    });   

    grid = Ext.create('Ext.grid.Panel', {
        id: 'grid',
        width: 970,
        height: 400,
        store: store,
        selModel: Ext.create('Ext.selection.CheckboxModel'),
        plugins: [{ptype: 'pagingselectpersist'}],
        viewConfig: {
            enableTextSelection: true,
            id: 'gv',
            trackOver: true,
            stripeRows: true,
            loadMask: true
        },        
        columns: [
            {
                id: 'id_policy',
                header: 'IdPolicy',
                dataIndex: 'id_policy',
                hidden: true,
                hideable: false
            },
            {
                id: 'nombrePolicy',
                header: 'Nombre Policy',
                dataIndex: 'nombrePolicy',
                width: 180,
                sortable: true
            },
            {
                id: 'leaseTime',
                header: 'Lease Time',
                dataIndex: 'leaseTime',
                width: 80,
                sortable: true
            },
            {
                id: 'mascara',
                header: 'Mascara',
                dataIndex: 'mascara',
                width: 90,
                sortable: true
            },
            {
                id: 'gateway',
                header: 'Gateway',
                dataIndex: 'gateway',
                width: 90,
                sortable: true
            },
            {
                id: 'dnsName',
                header: 'DNS Name',
                dataIndex: 'dnsName',
                width: 150,
                sortable: true
            },
            {
                id: 'dnsServers',
                header: 'DNS Servers',
                dataIndex: 'dnsServers',
                width: 200,
                sortable: true
            },
            {
                header: 'Estado',
                dataIndex: 'estado',
                width: 80,
                sortable: true
            },           
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 80,
                items: [
                    {
                        getClass: function(v, meta, rec) 
                        {
                            var permiso = $("#ROLE_277-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action1 = "icon-invisible";
                            }

                            if (rec.get('action1') == "icon-invisible")
                            {
                                this.items[0].tooltip = '';
                            }
                            else
                            {
                                this.items[0].tooltip = 'Ver';
                            }

                            return rec.get('action1');
                        },
                        tooltip: 'Ver',
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = $("#ROLE_277-6");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action1 = "icon-invisible";
                            }

                            if (rec.get('action1') != "icon-invisible")
                            {
                                window.location = rec.get('id_policy') + "/show";
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
                            }

                        }
                    },
                    {
                        getClass: function(v, meta, rec) 
                        {

                            var permiso = $("#ROLE_277-8");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) {
                                rec.data.action2 = "icon-invisible";
                            }

                            if (rec.get('action3') == "icon-invisible")
                            {
                                this.items[1].tooltip = '';
                            }
                            else
                            {
                                this.items[1].tooltip = 'Eliminar';
                            }

                            return rec.get('action2');
                        },
                        handler: function(grid, rowIndex, colIndex) 
                        {
                            var rec = store.getAt(rowIndex);

                            var permiso = $("#ROLE_277-8");
                            var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                            if (!boolPermiso) 
                            {
                                rec.data.action2 = "icon-invisible";
                            }

                            if (rec.get('action2') != "icon-invisible")
                            {
                                mostrarServidores(rec.get('id_policy'));
                            }
                            else
                            {
                                Ext.Msg.alert('Error ', 'No tiene permisos para realizar esta accion');
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
         
    /* ******************************************* */
                /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, // Don't want content to crunch against the borders        
        border: false,        
        buttonAlign: 'center',
        layout: {
            type: 'table',
            columns: 5,
            align: 'stretch'
        },
        bodyStyle: {
            background: '#fff'
        },
        collapsible: true,
        collapsed: false,
        width: 970,
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
        items: [
            {html: "&nbsp;", border: false, width: 50},
            {
                xtype: 'textfield',
                id: 'txtNombre',
                fieldLabel: 'Nombre',
                value: '',
                width: '300'
            },
            {html: "&nbsp;", border: false, width: 80},
            {
                xtype: 'combobox',
                fieldLabel: 'Estado',
                id: 'sltEstado',
                value: 'Todos',
                store: [
                    ['Todos', 'Todos'],
                    ['ACTIVO', 'Activo'],                    
                    ['ELIMINADO', 'Eliminado']
                ],
                width: '300'
            },
            {html: "&nbsp;", border: false, width: 80}           
        ],
        renderTo: 'filtro'
    });

});


/* ******************************************* */
            /*  FUNCIONES  */
/* ******************************************* */

function buscar(){
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;   
    store.load();
}

function limpiar(){
    Ext.getCmp('txtNombre').value="";
    Ext.getCmp('txtNombre').setRawValue("");
    Ext.getCmp('sltEstado').value="Todos";
    Ext.getCmp('sltEstado').setRawValue("Todos");
  
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.getProxy().extraParams.estado = Ext.getCmp('sltEstado').value;
 
    store.load();
}

function mostrarServidores(policy){
  
    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Eliminando Policy...');
                },
                scope: this
            },
            'requestcomplete': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            },
            'requestexception': {
                fn: function (con, res, opt) {
                    Ext.get(document.body).unmask();
                },
                scope: this
            }
        }
    });
    
     storeServidores = new Ext.data.Store({ 
        total: 'total',
        proxy: {
            type: 'ajax',
            method: 'post',
            url : url_getServidor,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }            
        },
        fields:
		[
			{name:'idElemento', mapping:'idElemento'},
			{name:'nombreElemento', mapping:'nombreElemento'}
		],
		autoLoad: true
    });
    
    comboServidores = new Ext.form.ComboBox({
        id: 'cmb_servidores',
        name: 'cmb_servidores',
        fieldLabel: "Elemento",
        emptyText: 'Seleccione',
        store: storeServidores,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        height:30,
		width: 350,
        border:0,
        margin:0,
		queryMode: "remote"		
    });
    
	btnguardar = Ext.create('Ext.Button', {
        text: 'Aceptar',
        cls: 'x-btn-rigth',
        handler: function()
        {            
            if(Ext.getCmp('cmb_servidores').value != null && Ext.getCmp('cmb_servidores').value != '')
            {
                Ext.Msg.confirm('Alerta', 'Se eliminara el registro. Desea continuar?', function(btn)
                {
                    if (btn == 'yes')
                    {
                        conn.request({
                            url: url_ajaxDelete,
                            method: 'post',
                            params: 
                                {
                                    param: policy, 
                                    elemento: Ext.getCmp('cmb_servidores').value
                                    },
                            success: function(response)
                            {
                                var text = response.responseText;
                                Ext.Msg.alert('Informacion ', text,function(btn){
                                    if(btn == 'ok')
                                    {
                                        win.destroy();
                                        store.load();
                                    }
                                });
                               
                            },
                            failure: function(result)
                            {
                                Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                            }
                        });
                    }
                });            
            }
            else
            {
                Ext.Msg.alert('Advertencia ', 'Debe Seleccionar un elemento');
            }
        }
    });


    btncancelar = Ext.create('Ext.Button', {
        text: 'Cerrar',
        cls: 'x-btn-rigth',
        handler: function() {
            win.destroy();
        }
    });


    formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 5,
        waitMsgTarget: true,
        layout: 'column',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 150,
            msgTarget: 'side'
        },
        items:
            [
                {
                    xtype: 'fieldset',
                    title: 'Seleccionar Elemento',
                    autoHeight: true,
                    width: 400,
                    items:
                        [
                            comboServidores
                        ]
                }
            ]
    });

    win = Ext.create('Ext.window.Window', {
        title: 'Asignar Elemento a Policy a eliminar',
        modal: true,
        width: 430,
        height: 150,
        resizable: false,
        layout: 'fit',
        items: [formPanel],
        buttonAlign: 'center',
        buttons: [btnguardar, btncancelar]
    }).show();


    
}