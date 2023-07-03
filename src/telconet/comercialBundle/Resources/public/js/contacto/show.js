Ext.onReady(function() {
    var objContacto = new Contacto();
    var arrayReturn = [];

    var booleanHideIdentificacion = true;
    var booleanHideTitulo = true;

    Ext.Ajax.request({
        url: urlGetInfoContacto,
        method: 'POST',
        timeout: 60000,
        async: false,
        params: {
            intIdPersona: intIdPersonaContacto
        },
        success: function(response) {
            var text = Ext.decode(response.responseText);
            arrayReturn['strNombres'] = text.registros.strNombres;
            arrayReturn['strApellidos'] = text.registros.strApellidos;
            arrayReturn['strDescripcionTitulo'] = text.registros.strDescripcionTitulo;
            if (arrayReturn['strDescripcionTitulo']) {
                booleanHideTitulo = false;
            }
            arrayReturn['strIdentificacionCliente'] = text.registros.strIdentificacionCliente;
            if (arrayReturn['strIdentificacionCliente']) {
                booleanHideIdentificacion = false;
            }
            arrayReturn['strDescTipoIdentificacion'] = text.registros.strDescTipoIdentificacion;
            arrayReturn['arrayRol'] = text.registros.arrayRol;
        },
        failure: function(result) {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText.trim());
        }
    });

    Ext.define('modelRol', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'intIdRol', type: 'int'},
            {name: 'intIdPersonaEmpresaRol', type: 'int'},
            {name: 'strDescripcionRol', type: 'string'},
            {name: 'strEstado', type: 'string'},
            {name: 'strUsrCreacion', type: 'string'},
            {name: 'strFeCreacion', type: 'string'},
            {name: 'strUsrUltMod', type: 'string'},
            {name: 'strFeUltMod', type: 'string'}
        ]
    });

    var storeTipoContactoPersona = Ext.create('Ext.data.Store', {
        pageSize: 5,
        autoLoad: true,
        model: 'modelRol',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlRolesPersonaPunto,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'registros',
                totalProperty: 'total'
            },
            extraParams: {
                intIdPersona: intIdPersonaContacto,
                strTipoConsulta: 'PERSONA',
                strEstado: 'Activo',
                intIdPersonaEmpresaRol: intIdPersonaEmpresaRol,
                intIdPunto: intIdPunto
            },
            simpleSortMode: true
        }
    });

    var gridInfoPersonaEmpresaRol = Ext.create('Ext.grid.Panel', {
        title: '[Cliente]' + arrayReturn['strDescripcionTitulo'] + ' ' +
            arrayReturn['strNombres'] + ' ' +
            arrayReturn['strApellidos'] + ' es contacto de tipo:',
        autoScroll: true,
        store: storeTipoContactoPersona,
        columns: [
            {width: 213, header: 'Tipo Contacto', dataIndex: 'strDescripcionRol'},
            {width: 180, header: 'Usuario Creación', dataIndex: 'strUsrCreacion'},
            {width: 180, header: 'Fecha Creación', dataIndex: 'strFeCreacion'},
            {width: 160, header: 'Estado', dataIndex: 'strEstado'}
        ],
        listeners: {
            viewready: function(grid) {
                addToolTip(grid);
            }
        },
        height: 200,
        width: 733,
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeTipoContactoPersona,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });

    var storeTipoContactoPunto = Ext.create('Ext.data.Store', {
        pageSize: 5,
        autoLoad: true,
        model: 'modelRol',
        collapsible: false,
        autoScroll: true,
        proxy: {
            type: 'ajax',
            url: urlRolesPersonaPunto,
            timeout: 90000,
            reader: {
                type: 'json',
                root: 'registros',
                totalProperty: 'total'
            },
            extraParams: {
                intIdPersona: intIdPersonaContacto,
                strTipoConsulta: 'PUNTO',
                strEstado: 'Activo',
                intIdPersonaEmpresaRol: intIdPersonaEmpresaRol,
                intIdPunto: intIdPunto
            },
            simpleSortMode: true
        }
    });

    var gridInfoPersonaPunto = Ext.create('Ext.grid.Panel', {
        title: '[Punto]' + arrayReturn['strDescripcionTitulo'] + ' ' +
            arrayReturn['strNombres'] + ' ' +
            arrayReturn['strApellidos'] + ' es contacto de tipo:',
        autoScroll: true,
        store: storeTipoContactoPunto,
        columns: [
            {width: 213, header: 'Tipo Contacto', dataIndex: 'strDescripcionRol'},
            {width: 180, header: 'Usuario Creación', dataIndex: 'strUsrCreacion'},
            {width: 180, header: 'Fecha Creación', dataIndex: 'strFeCreacion'},
            {width: 160, header: 'Estado', dataIndex: 'strEstado'}
        ],
        listeners: {
            viewready: function(grid) {
                addToolTip(grid);
            }
        },
        height: 200,
        width: 733,
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeTipoContactoPunto,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });

    var storeCreaPersonaFormaContacto = objContacto.storeCreaPersonaFormaContacto();
    storeCreaPersonaFormaContacto.proxy.extraParams = {
        intIdPersona: intIdPersonaContacto,
        strEstado: 'Activo'
    };
    storeCreaPersonaFormaContacto.load();

    var gridInfoPersonaFormaContacto = Ext.create('Ext.grid.Panel', {
        title: 'Formas contacto',
        autoScroll: true,
        store: storeCreaPersonaFormaContacto,
        columns: [
            {
                width: 300,
                header: "Forma contacto",
                dataIndex: 'strDescripcionFormaContacto'
            },
            {
                width: 313,
                header: "Valor",
                dataIndex: 'strValor'
            },
            {
                width: 120,
                header: "Estado",
                dataIndex: 'strEstadoPersonaFormaContacto'
            }
        ],
        listeners: {
            viewready: function(grid) {
                addToolTip(grid);
            }
        },
        height: 200,
        width: 600,
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeCreaPersonaFormaContacto,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });

    var formContacto = Ext.create('Ext.form.Panel', {
        renderTo: 'divFormContacto',
        bodyStyle: 'padding: 20px 55px 0; padding-bottom: 20px; background:#FFFFFF;',
        padding: 10,
        width: 900,
        autoScroll: false,
        layout: {
            type: 'table',
            columns: 1,
            tableAttrs: {
                style: {
                    width: '90%',
                    height: '90%'
                }
            },
            tdAttrs: {
                align: 'center',
                valign: 'middle'
            }
        },
        items: [
            {
                xtype: 'fieldset',
                title: 'Información Contacto',
                layout: {
                    type: 'vbox',
                    align: 'left',
                    pack: 'left'
                },
                items: [
                    {
                        xtype: 'displayfield',
                        fieldLabel: 'Titulo',
                        style: "font-weight:bold;",
                        name: 'strTitulo',
                        value: arrayReturn['strDescripcionTitulo'],
                        textAlign: 'left',
                        hidden: booleanHideTitulo
                    },
                    {
                        xtype: 'displayfield',
                        fieldLabel: 'Nombres',
                        style: "font-weight:bold;",
                        name: 'strNombreContacto',
                        value: arrayReturn['strNombres'],
                        textAlign: 'left'
                    },
                    {
                        xtype: 'displayfield',
                        fieldLabel: 'Apellidos',
                        style: "font-weight:bold;",
                        name: 'strApellidoContacto',
                        value: arrayReturn['strApellidos'],
                        textAlign: 'left'
                    },
                    {
                        xtype: 'displayfield',
                        fieldLabel: arrayReturn['strDescTipoIdentificacion'],
                        style: "font-weight:bold;",
                        name: 'strApellidoContacto',
                        value: arrayReturn['strIdentificacionCliente'],
                        textAlign: 'left',
                        hidden: booleanHideIdentificacion
                    }

                ]
            },
            {
                xtype: 'tabpanel',
                activeTab: 0,
                autoScroll: false,
                layoutOnTabChange: true,
                items: [gridInfoPersonaEmpresaRol, gridInfoPersonaPunto]
            },
            {
                xtype: 'tabpanel',
                activeTab: 0,
                autoScroll: false,
                layoutOnTabChange: true,
                items: [gridInfoPersonaFormaContacto]
            }
        ]
    });

    function addToolTip(grid) {
        var view = grid.view;
        grid.mon(view, {
            uievent: function(type, view, cell, recordIndex, cellIndex, e) {
                grid.cellIndex = cellIndex;
                grid.recordIndex = recordIndex;
            }
        });
        grid.tip = Ext.create('Ext.tip.ToolTip', {
            target: view.el,
            delegate: '.x-grid-cell',
            trackMouse: true,
            renderTo: Ext.getBody(),
            listeners: {
                beforeshow: function updateTipBody(tip) {
                    if (!Ext.isEmpty(grid.cellIndex) && grid.cellIndex !== -1) {
                        header = grid.headerCt.getGridColumns()[grid.cellIndex];
                        tip.update(grid.getStore().getAt(grid.recordIndex).get(header.dataIndex));
                    }
                }
            }
        });
    }
});