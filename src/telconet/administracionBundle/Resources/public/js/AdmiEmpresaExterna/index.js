/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.require([
    'Ext.ux.grid.plugin.PagingSelectionPersistence'
]);

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    Ext.define('ModelStore',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'id_persona', mapping: 'id_persona'},
                    {name: 'id_persona_empresa_rol', mapping: 'id_persona_empresa_rol'},
                    {name: 'id_empresa', mapping: 'id_empresa'},
                    {name: 'nombre_empresa', mapping: 'nombre_empresa'},
                    {name: 'razon_social', mapping: 'razon_social'},
                    {name: 'tipo_identificacion', mapping: 'tipo_identificacion'},
                    {name: 'identificacion', mapping: 'identificacion'},
                    {name: 'nombres', mapping: 'nombres'},
                    {name: 'apellidos', mapping: 'apellidos'},
                    {name: 'nacionalidad', mapping: 'nacionalidad'},
                    {name: 'direccion', mapping: 'direccion'},
                    {name: 'estado', mapping: 'estado'},
                    {name: 'action1', mapping: 'action1'},
                    {name: 'action2', mapping: 'action2'},
                    {name: 'action3', mapping: 'action3'}
                ],
            idProperty: 'id_persona_empresa_rol'
        });

    dataStoreEmpresaExterna = new Ext.data.Store(
        {
            pageSize: 20,
            model: 'ModelStore',
            total: 'total',
            proxy:
                {
                    type: 'ajax',
                    timeout: 600000,
                    url: urlGridEmpresasExternas,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'encontrados'
                        },
                    extraParams:
                        {
                            identificacion: '',
                            nombre: '',
                            razonSocial: '',
                            estado: 'Todos'
                        }
                },
            autoLoad: true
        });

    grid = Ext.create('Ext.grid.Panel',
        {
            id: 'grid',
            width: 1000,
            height: 400,
            store: dataStoreEmpresaExterna,
            loadMask: true,
            renderTo: 'gridEmpresasExternas',
            iconCls: 'icon-grid',
            bbar: Ext.create('Ext.PagingToolbar',
                {
                    store: dataStoreEmpresaExterna,
                    displayInfo: true,
                    displayMsg: 'Mostrando {0} - {1} de {2}',
                    emptyMsg: "No hay datos que mostrar."
                }),
            viewConfig:
                {
                    enableTextSelection: true,
                    id: 'gv'
                },
            columns:
                [
                    new Ext.grid.RowNumberer(),
                    {
                        id: 'id_persona_empresa_rol',
                        header: 'IdPersonaEmpresaRol',
                        dataIndex: 'id_persona_empresa_rol',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'id_persona',
                        header: 'IdPersona',
                        dataIndex: 'id_persona',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'id_empresa',
                        header: 'IdEmpresa',
                        dataIndex: 'id_empresa',
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'identificacion',
                        header: 'Identificación',
                        dataIndex: 'identificacion',
                        width: 90,
                        sortable: true
                    },
                    {
                        id: 'nombres',
                        header: 'Nombre Empresa',
                        dataIndex: 'nombres',
                        width: 180,
                        sortable: true
                    },
                    {
                        id: 'razonSocial',
                        header: 'Razón Social',
                        dataIndex: 'razon_social',
                        width: 250,
                        sortable: true
                    },
                    {
                        id: 'nacionalidad',
                        header: 'Nacionalidad',
                        dataIndex: 'nacionalidad',
                        width: 80,
                        sortable: true,
                        hidden: true,
                        hideable: false
                    },
                    {
                        id: 'direccion',
                        header: 'Dirección',
                        dataIndex: 'direccion',
                        width: 280,
                        sortable: true
                    },
                    {
                        id: 'nombre_empresa',
                        header: 'Empresa Interna',
                        dataIndex: 'nombre_empresa',
                        width: 100,
                        sortable: true,
                        hidden: true,
                        hideable: false
                    },
                    {
                        header: 'Estado',
                        dataIndex: 'estado',
                        width: 70,
                        sortable: true
                    },
                    {
                        xtype: 'actioncolumn',
                        header: 'Acciones',
                        width: 100,
                        items: [
                            {
                                /*Ver Empresa Externa*/
                                getClass: function(v, meta, rec)
                                {
                                    return 'button-grid-show';
                                },
                                tooltip: 'Ver',
                                handler: function(grid, rowIndex, colIndex)
                                {
                                    var rec = dataStoreEmpresaExterna.getAt(rowIndex);
                                    window.location = "" + rec.get('id_persona_empresa_rol') + "/show";
                                }
                            },
                            {
                                /*Editar Empresa Externa*/
                                getClass: function(v, meta, rec)
                                {
                                    strEditarEmpresaEmpresa = 'button-grid-invisible';
                                    var permiso = $("#ROLE_298-2917");
                                    var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
                                    if (boolPermiso)
                                    {
                                        if ('Eliminado' !== rec.get('estado'))
                                        {
                                            strEditarEmpresaEmpresa = 'button-grid-edit';
                                        }
                                    }
                                    return strEditarEmpresaEmpresa;
                                },
                                tooltip: 'Editar Empresa Externa',
                                handler: function(grid, rowIndex, colIndex)
                                {
                                    var rec = dataStoreEmpresaExterna.getAt(rowIndex);
                                    window.location = "" + rec.get('id_persona_empresa_rol') + "/edit";
                                }
                            }
                        ]
                    }
                ]
        });

    /* ******************************************* */
    /* FILTROS DE BUSQUEDA */
    /* ******************************************* */
    Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 7,
            border: false,
            buttonAlign: 'center',
            renderTo: 'filtroEmpresasExternas',
            collapsible: true,
            collapsed: true,
            width: 1000,
            title: 'Criterios de búsqueda',
            layout:
                {
                    type: 'table',
                    align: 'stretch',
                    columns: 4
                },
            bodyStyle:
                {
                    background: '#fff'
                },
            buttons:
                [
                    {
                        text: 'Buscar',
                        iconCls: "icon_search",
                        handler: function()
                        {
                            getEmpresasExternas();
                        }
                    },
                    {
                        text: 'Limpiar',
                        iconCls: "icon_limpiar",
                        handler: function()
                        {
                            Ext.getCmp('txtIdentificacion').value = '';
                            Ext.getCmp('txtIdentificacion').setRawValue('');
                            Ext.getCmp('txtNombre').value = '';
                            Ext.getCmp('txtNombre').setRawValue('');
                            Ext.getCmp('txtRazonSocial').value = '';
                            Ext.getCmp('txtRazonSocial').setRawValue('');
                            Ext.getCmp('cbxEstado').value = "Todos";
                            Ext.getCmp('cbxEstado').setRawValue("Todos");
                            getEmpresasExternas();
                        }
                    }
                ],
            items:
                [
                    {width: 50, border: false},
                    {
                        xtype: 'textfield',
                        id: 'txtIdentificacion',
                        fieldLabel: 'Identificación',
                        maskRe: /[0-9]/,
                        value: '',
                        maxLength: 13,
                        enforceMaxLength: 13,
                        width: 210,
                        listeners:
                            {
                                focus: function(field, newValue, oldValue)
                                {
                                    Ext.getCmp('txtNombre').setValue('');
                                    Ext.getCmp('txtRazonSocial').setValue('');
                                },
                                specialkey: function(funct, event)
                                {
                                    if (event.getKey() == event.ENTER)
                                    {
                                        getEmpresasExternas();
                                    }
                                }
                            }
                    },
                    {width: 50, border: false},
                    {
                        xtype: 'combobox',
                        fieldLabel: 'Estado',
                        id: 'cbxEstado',
                        value: 'Todos',
                        width: 200,
                        editable: false,
                        listeners:
                            {
                                select: function()
                                {
                                    getEmpresasExternas();
                                }
                            },
                        store:
                            [
                                ['Todos', 'Todos'],
                                ['Activo', 'Activo'],
                                ['Eliminado', 'Eliminado']
                            ]
                    },
                    {width: 50, border: false},
                    {
                        xtype: 'textfield',
                        id: 'txtNombre',
                        fieldLabel: 'Nombre',
                        value: '',
                        maxLength: 300,
                        width: 350,
                        listeners:
                            {
                                focus: function(field, newValue, oldValue)
                                {
                                    Ext.getCmp('txtIdentificacion').setValue('');
                                    Ext.getCmp('txtRazonSocial').setValue('');
                                },
                                specialkey: function(funct, event)
                                {
                                    if (event.getKey() == event.ENTER)
                                    {
                                        getEmpresasExternas();
                                    }
                                }
                            }
                    },
                    {width: 50, border: false},
                    {
                        xtype: 'textfield',
                        id: 'txtRazonSocial',
                        fieldLabel: 'Razón Social',
                        value: '',
                        maxLength: 300,
                        width: 350,
                        listeners:
                            {
                                focus: function(field, newValue, oldValue)
                                {
                                    Ext.getCmp('txtIdentificacion').setValue('');
                                    Ext.getCmp('txtNombre').setValue('');
                                },
                                specialkey: function(funct, event)
                                {
                                    if (event.getKey() == event.ENTER)
                                    {
                                        getEmpresasExternas();
                                    }
                                }
                            }
                    }
                ]
        });

});

function getEmpresasExternas()
{
    dataStoreEmpresaExterna.getProxy().extraParams.identificacion = Ext.getCmp('txtIdentificacion').value;
    dataStoreEmpresaExterna.getProxy().extraParams.nombre = Ext.getCmp('txtNombre').value;
    dataStoreEmpresaExterna.getProxy().extraParams.razonSocial = Ext.getCmp('txtRazonSocial').value;
    dataStoreEmpresaExterna.getProxy().extraParams.estado = Ext.getCmp('cbxEstado').value;
    dataStoreEmpresaExterna.currentPage = 1;
    dataStoreEmpresaExterna.load();
}
