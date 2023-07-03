Ext.onReady(function()
{
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
                                        Ext.Ajax.request(
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
                                                }
                                            });
                                    }
                                });
                        }
                    }
                ]
        });
        
    if ($('#Estado').val() == 'Eliminado')
    {
        Ext.getCmp('btnGuardar').hide();
        Ext.getCmp('btnEliminar').disable();
        Ext.Msg.show(
            {
                title: 'Error',
                msg: 'No se puede editar la Empresa Externo <br>\'' +
                    $('#admiempresaexternatype_identificacionCliente').val() + '-' +
                    $('#admiempresaexternatype_razonSocial').val() +
                    '\' <br> porque su estado es \'Eliminado\'.',
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
    }
    
    var permisoEliminacion = $("#ROLE_298-2918");
    var boolPermisoEliminacion = (typeof permisoEliminacion === 'undefined') ? false : (permisoEliminacion.val() == 1 ? true : false);
    if (!boolPermisoEliminacion)
    {
        Ext.getCmp('btnEliminar').hide();
    }

});
