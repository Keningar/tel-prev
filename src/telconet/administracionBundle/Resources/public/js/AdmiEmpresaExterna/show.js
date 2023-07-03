Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();

    Ext.define('FormasContacto',
        {
            extend: 'Ext.data.Model',
            fields:
                [
                    {name: 'idPersonaFormaContacto', mapping: 'idPersonaFormaContacto'},
                    {name: 'idPersona', mapping: 'idPersona'},
                    {name: 'formaContacto', mapping: 'formaContacto'},
                    {name: 'valor', mapping: 'valor'}
                ]
        });

    var storeFormasContacto = Ext.create('Ext.data.Store',
        {
            autoLoad: true,
            model: 'FormasContacto',
            proxy:
                {
                    type: 'ajax',
                    url: urlFormasContactoEmpresa,
                    reader:
                        {
                            type: 'json',
                            totalProperty: 'total',
                            root: 'personaFormasContacto'
                        },
                    extraParams:
                        {
                            personaempresaexternaID: personaempresaexternaId
                        }
                }
        });

    gridRelaciones = Ext.create('Ext.grid.Panel',
        {
            id: 'gridFormasContactoiId',
            store: storeFormasContacto,
            columnLines: true,
            width: 650,
            height: 120,
            frame: true,
            renderTo: 'gridFormasContacto',
            columns:
                [
                    Ext.create('Ext.grid.RowNumberer'),
                    {
                        id: 'formaContacto',
                        header: 'Forma Contacto',
                        dataIndex: 'formaContacto',
                        fontWeight: 'bold',
                        width: 175
                    },
                    {
                        id: 'valor',
                        header: 'Valor',
                        dataIndex: 'valor',
                        width: 450,
                        sortable: true
                    }],
            viewConfig: {
                stripeRows: true
            }
        });

    Ext.create('Ext.panel.Panel',
        {
            bodyPadding: 10,
            border: false,
            buttonAlign: 'center',
            renderTo: 'form_accionesEmpresaExterna',
            layout:
                {
                    type: 'table',
                    columns: 1,
                    align: 'stretch'
                },
            bodyStyle:
                {
                    background: '#fff'
                },
            items:
                [
                    {
                        xtype: 'button',
                        id: 'btnEditar',
                        text: 'Editar Empresa Externa',
                        ui: 'lo-que-sea',
                        cls: 'button-editar',
                        handler: function()
                        {
                            Ext.getCmp('btnEliminar').disable();
                            Ext.getCmp('btnEditar').disable();
                            window.location = urlEditarEmpresaExterna;
                        }
                    },
                    {
                        xtype: 'button',
                        id: 'btnEliminar',
                        text: 'Eliminar Empresa Externa',
                        ui: 'lo-que-sea',
                        cls: 'button-eliminar',
                        margin: '15 0 0 0',
                        handler: function()
                        {
                            Ext.Msg.confirm('Alerta',
                                'Se eliminará la Empresa Externa.<br> Todos su personal Activo se eliminará también.<br> ¿Desea continuar?',
                                function(btn)
                                {
                                    if (btn === 'yes')
                                    {
                                        Ext.getCmp('btnEliminar').disable();
                                        Ext.getCmp('btnEditar').disable();
                                        connEsperaAccion.request(
                                            {
                                                url: urlEliminarEmpresaExterna,
                                                method: 'POST',
                                                timeout: 60000,
                                                success: function(response)
                                                {
                                                    var text = Ext.decode(response.responseText);
                                                    if (text.estatus)
                                                    {
                                                        Ext.Msg.show(
                                                            {
                                                                title: 'Información',
                                                                msg: text.msg,
                                                                buttons: Ext.Msg.OK,
                                                                icon: Ext.MessageBox.INFO
                                                            });
                                                        window.location = urlShow;
                                                    }
                                                    else
                                                    {
                                                        Ext.Msg.show(
                                                            {
                                                                title: 'Error',
                                                                msg: text.msg,
                                                                buttons: Ext.Msg.OK,
                                                                icon: Ext.MessageBox.ERROR
                                                            });
                                                        Ext.getCmp('btnEliminar').enable();
                                                        Ext.getCmp('btnEditar').enable();
                                                    }
                                                },
                                                failure: function(result)
                                                {
                                                    Ext.Msg.show(
                                                        {
                                                            title: 'Error',
                                                            msg: 'Error: ' + result.statusText,
                                                            buttons: Ext.Msg.OK,
                                                            icon: Ext.MessageBox.ERROR
                                                        });
                                                    Ext.getCmp('btnEliminar').enable();
                                                    Ext.getCmp('btnEditar').enable();
                                                }
                                            });
                                    }
                                });
                        }
                    }
                ]
        });

    var permisoEdicion = $("#ROLE_298-2917");
    var boolPermisoEdicion = (typeof permisoEdicion === 'undefined') ? false : (permisoEdicion.val() == 1 ? true : false);
    if (!boolPermisoEdicion)
    {
        Ext.getCmp('btnEditar').hide();
    }
    else if ($('#Estado').val() == 'Eliminado')
    {
        Ext.getCmp('btnEditar').disable();
    }

    var permisoEliminacion = $("#ROLE_298-2918");
    var boolPermisoEliminacion = (typeof permisoEliminacion === 'undefined') ? false : (permisoEliminacion.val() == 1 ? true : false);
    if (!boolPermisoEliminacion)
    {
        Ext.getCmp('btnEliminar').hide();
    }
    else if ($('#Estado').val() == 'Eliminado')
    {
        Ext.getCmp('btnEliminar').disable();
    }

});

var connEsperaAccion = new Ext.data.Connection
    ({
        listeners:
            {
                'beforerequest':
                    {
                        fn: function(con, opt)
                        {
                            Ext.MessageBox.show
                                ({
                                    msg: 'Eliminando Empresa Externa, Por favor espere!!',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });
                        },
                        scope: this
                    },
                'requestcomplete':
                    {
                        fn: function(con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    },
                'requestexception':
                    {
                        fn: function(con, res, opt)
                        {
                            Ext.MessageBox.hide();
                        },
                        scope: this
                    }
            }
    });
