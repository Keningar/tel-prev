Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
        'Ext.window.MessageBox'
]);

function winVerDatosEnvio(idPto,ciudad,parroquia,sector,nombre,direccion,telefono,email) {

    winDetalle="";
            if(!winDetalle) {
          	

        var form = Ext.widget('form', {
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            border: false,
            bodyPadding: 10,

            fieldDefaults: {
                labelAlign: 'top',
                labelWidth: 130,
                labelStyle: 'font-weight:bold'
            },
            defaults: {
                margins: '0 0 10 0'
            },
            url: url_grabar,
            items: [
            {
                xtype: 'textfield',
                fieldLabel: 'Nombre',
                labelAlign: 'left',                
                name: 'nombre',
                value: nombre,
                allowBlank: false
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Ciudad',
                labelAlign: 'left',
                name: 'ciudad',
                value: ciudad,
                readOnly: true
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Parroquia',
                labelAlign: 'left',
                name: 'parroquia',
                value: parroquia,
                readOnly: true
            } ,                
            {
                xtype: 'textfield',
                fieldLabel: 'Sector',
                labelAlign: 'left',
                name: 'sector',
                value: sector,
                readOnly: true
            },
            {
                xtype: 'textareafield',
                fieldLabel: 'Direccion',
                labelAlign: 'left',
                name: 'direccion',
                value: direccion,
                readOnly: true,
                anchor: '100%'
            },            
            {
                xtype: 'textfield',
                fieldLabel: 'Email',
                labelAlign: 'left',                
                name: 'email',
                value: email,
                readOnly: true
            },            
            {
                xtype: 'textfield',
                fieldLabel: 'Telefono',
                labelAlign: 'left',                
                name: 'telefono',
                value: telefono,
                readOnly: true
            },            
            {
                xtype: 'hiddenfield',             
                name: 'idPto',
                value: idPto
            }
            
            ],
            buttons: [{
                text: 'Cancel',
                handler: function() {
                    this.up('form').getForm().reset();
                    this.up('window').destroy();
                }
            }]
        });

        winDetalle = Ext.widget('window', {
            title: 'Ingresar Datos de Envio',
            closeAction: 'hide',
            closable: false,
            width: 350,
            height: 340,
            minHeight: 200,
            layout: 'fit',
            resizable: true,
            modal: true,
            items: form
        });

    }

    winDetalle.show();

}

