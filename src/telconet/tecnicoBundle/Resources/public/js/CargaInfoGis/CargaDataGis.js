Ext.QuickTips.init();
Ext.onReady(function() {

    Ext.create('Ext.form.Panel', {
        renderTo: 'frmCargaDataGis',
        width: 500,
        frame: true,
        title: 'Carga información',
        bodyPadding: '10 10 0',
        defaults: {
            anchor: '100%',
            allowBlank: false
        },
        items: [
            {
                xtype: 'filefield',
                id: 'form-file',
                name: 'archivo',
                fieldLabel: 'Archivo',
                style: Utils.STYLE_BOLD,
                emptyText: 'Seleccione un archivo .xlsx',
                buttonText: 'Buscar',
                buttonConfig: {
                    iconCls: 'upload-icon'
                }
            }
        ],
        buttons: [
            {
                text: 'Subir',
                handler: function() {
                    var form = this.up('form').getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: urlUpLoadFile,
                            waitMsg: 'Subiendo archivo...',
                            success: function(form, action) {
                                Ext.Msg.alert(Utils.arrayTituloMensajeBox[action.result.strStatus], action.result.strMessageStatus);
                            },
                            failure: function(form, action) {
                                Ext.Msg.alert(Utils.arrayTituloMensajeBox[action.result.strStatus], action.result.strMessageStatus);
                            }
                        });
                    } else{
                        Ext.Msg.alert("Error", "Debe seleccionar un archivo");
                    }
                }
            }
        ]
    });
});