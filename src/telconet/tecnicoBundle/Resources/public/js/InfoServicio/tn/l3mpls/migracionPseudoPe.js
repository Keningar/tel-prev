
function migrarAPseudoPe(data)
{
    var storePseudoPe = new Ext.data.Store({
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: utlGetElementosPseudoPe,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'resultado'
            }
        },
        fields:
            [
                {name: 'idElemento',     mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'}
            ]
    });
                
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 85,
            msgTarget: 'side'
        },
        items: [{
                items: [
                    //informacion de backbone
                    {
                        xtype: 'fieldset',
                        title: '<b>Escoja el Edificio PseudoPe a relacionar con el Servicio</b>',
                        defaultType: 'textfield',
                        items: [
                            {
                                xtype: 'container',
                                layout: {
                                    type: 'table',
                                    columns: 3,
                                    align: 'stretch'
                                },
                                items: [
                                    {
                                        xtype: 'combo',
                                        id: 'cmbEdificio',
                                        name: 'cmbEdificio',
                                        store: storePseudoPe,
                                        fieldLabel: 'PseudoPe',
                                        displayField: 'nombreElemento',
                                        valueField: 'idElemento',
                                        allowBlank: false,
                                        queryMode: 'remote',
                                        width: 300
                                    },
                                    {width: '10%', border: false},
                                    {width: '10%', border: false}                                                                        
                                ]
                            }

                        ]
                    }                 
                ]
            }],
        buttons: [{
                text: 'Ejecutar',
                formBind: true,
                handler: function() {
                    
                    var edificio = Ext.getCmp('cmbEdificio').value;
                    
                    if(Ext.isEmpty(edificio))
                    {
                        Ext.Msg.alert('Mensaje', 'Debe escoger el Edificio al cual relacionar el Servicio');
                    }
                    else
                    {
                        Ext.MessageBox.wait('Cambiando a Esquema Edificio Pseudo-Pe...');
                        Ext.Ajax.request({
                            url: urlMigrarPseudoPe,
                            method: 'post',
                            timeout: 300000,
                            params: {
                                idServicio: data.idServicio,
                                idEdificio: edificio
                            },
                            success: function(response) {
                                Ext.MessageBox.hide();
                                win.hide();

                                var json = Ext.JSON.decode(response.responseText);

                                Ext.Msg.alert('Mensaje', json.mensaje);
                                
                                store.load();
                            }
                        });
                    }
                }
            }, {
                text: 'Cerrar',
                handler: function() {
                    win.destroy();
                }
            }]
    });

    var win = Ext.create('Ext.window.Window', {
        title: 'Agregar a Edificio',
        modal: true,
        width: 400,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();

}
