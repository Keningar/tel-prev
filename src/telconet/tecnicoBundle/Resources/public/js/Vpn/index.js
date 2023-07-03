Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function() {
    //GRID VPNS
    store = new Ext.data.Store({
        autoLoad : true,
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: urlAjaxGrid,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'data'
            },
            extraParams: {
                nombre: '',
                flag: 'cliente'
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            },
        },
        fields:
            [
                {name: 'id', mapping: 'id'},
                {name: 'vpn', mapping: 'nombre_vpn'},
                {name: 'vrf', mapping: 'vrf'},
                {name: 'strVlan', mapping: 'strVlan'},
                {name: 'strBandCliente', mapping: 'strBandCliente'},
                {name: 'strExisteVlan', mapping: 'strExisteVlan'},
                {name: 'id_vrf', mapping: 'id_vrf'},
                {name: 'rd_id', mapping: 'rd_id'},
                {name: 'fe_creacion', mapping: 'fe_creacion'},
                {name: 'usr_creacion', mapping: 'usr_creacion'},
            ],
    });

    grid = Ext.create('Ext.grid.Panel', {
        height: 230,
        frame:true,
        title: 'Cliente',
        store: store,
        loadMask: true,
        frame: false,
        viewConfig: {enableTextSelection: true},
        columns: [
            {
                id: 'id',
                header: 'ID',
                dataIndex: 'id',
                width: 70,
            },
            {
                header: 'VPN',
                dataIndex: 'vpn',
                sortable: true,
                width: 230,
            },
            {
                header: 'VRF',
                dataIndex: 'vrf',
                sortable: true,
                width: 230,
            },
            {
                header: 'RD ID',
                dataIndex: 'rd_id',
                sortable: true
            },
            {
                header: 'USR CREACION',
                dataIndex: 'usr_creacion',
                sortable: true,
                width: 180,
            },
            {
                header: 'FECHA CREACION',
                dataIndex: 'fe_creacion',
                sortable: true,
                width: 180,
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 80,
                items:
                [
                    {
                        getClass: function(v, meta, rec)
                        {
                            if(rec.data.strBandCliente == "S")
                            {
                                var permiso = $("#ROLE_319-6677");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if(boolPermiso)
                                {
                                    return 'button-grid-reconfigurarPuerto';
                                }
                                else
                                {
                                    return 'icon-invisible';
                                }
                            }
                            else
                            {
                                return 'icon-invisible';
                            }
                        },
                        tooltip: 'Mapear Vlan',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = store.getAt(rowIndex);
                            mapeoVrfyVlan(rec.data);
                        }
                    }
				]
			},
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    
    var filterPanel = Ext.create('Ext.panel.Panel', {
        bodyPadding: 7, 
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
            {width: '10%', border: false},
            {
                xtype: 'textfield',
                id: 'txtNombre',
                fieldLabel: 'Nombre',
                value: '',
                width: '200px'
            }
        ],
        renderTo: 'filtro'
    });

    //GRID IMPORTADAS
    storeImportadas = new Ext.data.Store({
        autoLoad : true,
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url: urlAjaxGridImportadas,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'data'
            },
            actionMethods: {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            },
        },
        fields:
            [
                {name: 'id', mapping: 'id'},
                {name: 'vpn', mapping: 'vpn'},
                {name: 'vrf', mapping: 'vrf'},
                {name: 'strVlan', mapping: 'strVlan'},
                {name: 'strBandCliente', mapping: 'strBandCliente'},
                {name: 'strExisteVlan', mapping: 'strExisteVlan'},
                {name: 'id_vrf', mapping: 'id_vrf'},
                {name: 'rd_id', mapping: 'rd_id'},
                {name: 'fe_creacion', mapping: 'fe_creacion'},
                {name: 'usr_creacion', mapping: 'usr_creacion'},
            ],
    });

    gridImportadas = Ext.create('Ext.grid.Panel', {
        height: 230,
        frame:true,
        title: 'Importadas',
        store: storeImportadas,
        loadMask: true,
        frame: false,
        viewConfig: {enableTextSelection: true},
        columns: [
            {
                id: 'id',
                header: 'ID',
                dataIndex: 'id',
                width: 70,
            },
            {
                header: 'VPN',
                dataIndex: 'vpn',
                sortable: true,
                width: 230,
            },
            {
                header: 'VRF',
                dataIndex: 'vrf',
                sortable: true,
                width: 230,
            },
            {
                header: 'RD ID',
                dataIndex: 'rd_id',
                sortable: true
            },
            {
                header: 'USR CREACION',
                dataIndex: 'usr_creacion',
                sortable: true,
                width: 180,
            },
            {
                header: 'FECHA CREACION',
                dataIndex: 'fe_creacion',
                sortable: true,
                width: 180,
            },
            {
                xtype: 'actioncolumn',
                header: 'Acciones',
                width: 80,
                items:
                [
                    {
                        getClass: function(v, meta, rec)
                        {
                            if(rec.data.strBandCliente == "S")
                            {
                                var permiso = $("#ROLE_319-6677");
                                var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                if(boolPermiso)
                                {
                                    return 'button-grid-reconfigurarPuerto';
                                }
                                else
                                {
                                    return 'icon-invisible';
                                }
                            }
                            else
                            {
                                return 'icon-invisible';
                            }
                        },
                        tooltip: 'Mapear Vlan',
                        handler: function(grid, rowIndex, colIndex)
                        {
                            var rec = storeImportadas.getAt(rowIndex);
                            mapeoVrfyVlan(rec.data);
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
        renderTo: 'gridImportadas'
    });

});


function mapeoVrfyVlan(registro)
{
    var boolSeleccionaVlan = true;
    var boolEtiquetaVlan   = true;

    if(registro.strExisteVlan == "N")
    {
        boolSeleccionaVlan = false;
    }

    if(registro.strExisteVlan == "S")
    {
        boolEtiquetaVlan = false;
    }

    var conn = new Ext.data.Connection({
        listeners: {
            'beforerequest': {
                fn: function (con, opt) {
                    Ext.get(document.body).mask('Loading...');
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

	btnguardar = Ext.create('Ext.Button', {
			text: 'Aceptar',
			cls: 'x-btn-rigth',
			handler: function()
            {
                    var strNuevaVlan  = Ext.getCmp('comboVlan').value;
                    var strVlanActual = registro.strVlan;
                    var strVrf        = registro.vrf;
                    var strIdVrf      = registro.id_vrf;
                    var strVpn        = registro.vpn;
                    var strIdVpn      = registro.id;

                    if(strNuevaVlan == null && boolSeleccionaVlan == false)
                    {
                        Ext.Msg.alert('Alerta ',"Favor seleccionar una VLAN");
                    }
                    else if(!boolEtiquetaVlan)
                    {
                        win.destroy();
                    }
                    else
                    {
                        win.destroy();
                        conn.request({
                            method: 'POST',
                            params :{
                                nuevaVlan: strNuevaVlan,
                                vlanActual: strVlanActual,
                                idVrf: strIdVrf,
                                vrf: strVrf,
                                vpn: strVpn,
                                idVpn: strIdVpn
                            },
                            url: urlMapearVrfyVlan,
                            success: function(response){
                            var json = Ext.JSON.decode(response.responseText);
                                storeImportadas.load();
                                store.load();
                                Ext.Msg.alert('Alerta ',json.mensaje);

                            },
                            failure: function(result) {
                                Ext.Msg.show({
                                    title: 'Error',
                                    msg: result.statusText,
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            }
                        });
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
				title: 'Asociar vlan a vrf actual',
				autoHeight: true,
				width: 475,
				items:
				[
					{
						xtype: 'displayfield',
						fieldLabel: 'Vrf:',
						id: 'nombre_vrf',
						name: 'nombre_vrf',
						value: registro.vrf
					},
					{
						xtype: 'displayfield',
						fieldLabel: 'Vlan:',
						id: 'nombre_vlan_actual',
						name: 'nombre_vlan_actual',
						value: registro.strVlan
					},
					{
						xtype: 'combobox',
						fieldLabel: 'Seleccione Vlan',
						id: 'comboVlan',
						width: 435,
						name: 'comboVlan',
                        hidden: boolSeleccionaVlan,
                        store: [
                            ['42', '42'],
                            ['43', '43'],
                            ['44', '44'],
                            ['45', '45'],
                            ['46', '46'],
                            ['47', '47'],
                            ['48', '48'],
                            ['49', '49'],
                            ['50', '50']
                            ],
						displayField: 'id_vlan',
						valueField: 'nombre_vlan',
					},
					{
						xtype: 'displayfield',
						fieldLabel: 'Seleccione Vlan:',
						id: 'etiquetaComboVlan',
						name: 'etiquetaComboVlan',
						value: "Ya posee VLAN mapeada",
                        hidden: boolEtiquetaVlan
					},
				]
			}
		]
	});

	win = Ext.create('Ext.window.Window', {
		title: "Mapeo de Vrf y Vlan",
		closable: false,
		modal: true,
		width: 500,
		height: 180,
		resizable: false,
		layout: 'fit',
		items: [formPanel],
		buttonAlign: 'center',
		buttons:[btnguardar,btncancelar]
	}).show();



}



function buscar() {
    store.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    store.load();
}

function limpiar() {
    Ext.getCmp('txtNombre').value = "";
    Ext.getCmp('txtNombre').setRawValue("");
    store.load({params: {
            nombre: ""
        }});
}