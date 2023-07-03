Ext.require([
    '*'
]);




Ext.onReady(function()
{
    Ext.define('PersonaFormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'formaContactoId', type: 'int'},
            {name: 'valor', type: 'string'},
            {name: 'esWhatsapp'}
        ]
    });

    storeCorreos = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            model: 'PersonaFormasContactoModel',
        });

    storeTelefonos = Ext.create('Ext.data.Store',
        {
            autoDestroy: true,
            model: 'PersonaFormasContactoModel',
        });


    gridNumero = Ext.create('Ext.grid.Panel', {
        width: 300,
        height: 200,
        store: storeTelefonos,
        loadMask: true,
        iconCls: 'icon-grid',
        align:'left',
        // grid columns
        columns: [
            {
                header: 'Id',
                dataIndex: 'formaContactoId',
                hidden: true,
                hideable: false
            },
            {
                header: 'Valor',
                dataIndex: 'valor',
                width: 200,
                sortable: true
            },
            {
                xtype: 'checkcolumn',
                header: 'Envío',
                dataIndex: 'esWhatsapp',
                width: 60,
                editor:
                    {
                        xtype: 'checkbox',
                        cls: 'x-grid-checkheader-editor'
                    },
                stopSelection: false
            },

        ],
        title: 'Teléfonos',
        //renderTo: 'gridPerfiles'
    });



    gridCorreo = Ext.create('Ext.grid.Panel', {
        width: 300,
        height: 200,
        store: storeCorreos,
        loadMask: true,
        iconCls: 'icon-grid',
        align: 'left',
        // grid columns
        columns: [
            {
                header: 'Id',
                dataIndex: 'formaContactoId',
                hidden: true,
                hideable: false
            },
            {
                header: 'Valor',
                dataIndex: 'valor',
                width: 200,
                sortable: true
            },
            {
                xtype: 'checkcolumn',
                header: 'Envío',
                dataIndex: 'esWhatsapp',
                width: 60,
                editor:
                    {
                        xtype: 'checkbox',
                        cls: 'x-grid-checkheader-editor'
                    },
                stopSelection: false
            },

        ],
        title: 'Correos',
        //renderTo: 'gridPerfiles'
    });


    let objComboTipoIdent = new Ext.create('Ext.form.ComboBox', {
        id: 'objComboTipoIdent',
        name: 'objComboTipoIdent',
        fieldLabel: 'Tipo Identificación',
        store: ['CEDULA', 'RUC', 'PASAPORTE'],
        labelAlign: 'left',
        queryMode: 'local',
        editable: false,
        displayField: '',
        valueField: '',
        width: 325,
      });
      let objTextIdentificacion = Ext.create('Ext.form.Text', {
        id: 'objTextIdentificacion',
        name: 'objTextIdentificacion',
        fieldLabel: 'Identificación',
        labelAlign: 'left',
        width: 325,
        value: '',
        margin: "0 0 0 10"
      });
      let searchButton = Ext.create('Ext.Button',{
        text: "Buscar",
        iconMask: true,
        width:50,
        handler: function() {
            if (valida()){
                buscar();
            }
            
        }

      });
    // create the grid and specify what field you want
    // to use for the editor at each header.

        formAceptacionCliente = Ext.create('Ext.form.Panel', {

            style: {
                "margin-left": "auto",
                        "margin-right": "auto"
                   },
            bodyStyle: 'padding: 0px 0px 0; background:#FFFFFF;',
            width: 850,
            height:400,
            title: 'Formulario de Aceptacion de Clientes',
            renderTo: Ext.get('aceptacion_cliente'),
            align: 'middle',
            pack: 'center',
            listeners: {
                afterRender: function(thisForm, options) {
                }
            },
    
            layout:'vbox',
            layoutConfig: {
                type: 'table',
                columns: 3,
                pack: 'center',
                align: 'middle',
                tableAttrs: {
                    style: {
                        width: '90%',
                        height: '90%'
                    }
                },
                tdAttrs: {
                    align: 'left',
                    valign: 'middle'
                }
            },
            buttonAlign: 'center',
            buttons: [
                {
                    text: 'Guardar',
                    name: 'btnGuardar',
                    id: 'idBtnGuardar',
                    disabled: false,
                    handler: function() {
                    
                                guardar();

                    }
                
                },
                {
                    text: 'Regresar',
                    handler: function() { 
                        window.location.href = strUrlIndex;
                    }
                }]
        });

        var container = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'vbox',
                align: 'center',
                pack: 'center', },
            width: 800,
            height: 40,
            items: [
                {
                    xtype: 'panel',
                    border: false,

                    layout: {
                        type: 'hbox',
                        align: 'center',
                        pack: 'center'
                    }, 
                    items: [
                        
                           objComboTipoIdent,
                           objTextIdentificacion,
                           searchButton

                        ]
                }]
        });

        var container2 = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'vbox',
                align: 'center',
                pack: 'center',
                margin: 10,
             },
            width: 800,
            height: 40,
            readOnly: true,
            enabled:false,    
            items: [
                {
                    xtype: 'panel',
                    border: false,

                    layout: {
                        type: 'hbox',
                        align: 'center',
                        pack: 'center',
                        margin: 10      
                    }, 
                    items: [
                        {
                            xtype: 'textfield',
                            id: 'nombres',
                            fieldLabel: 'Nombres:',
                            value: '',
                            width: 325,
                            readOnly: true,
            
                        },
                        {
                            xtype: 'textfield',
                            id: 'apellidos',
                            fieldLabel: 'Apellidos:',
                            value: '',
                            width: 375,
                            margin: "0 0 0 10",
                            readOnly: true,
                        },
    

                            ]
                }]
        });    

        var container3 = Ext.create('Ext.container.Container',
        {
            layout: {
                type: 'vbox',
                align: 'center',
                pack: 'center',
                margin: 10,
             },
            width: 800,
            height: 200,
            readOnly: true,
            enabled:false,    
            items: [
                {
                    xtype: 'panel',
                    border: false,

                    layout: {
                        type: 'hbox',
                        align: 'center',
                        pack: 'center',
                        margin: 10      
                    }, 
                    //layout: { type: 'hbox', align: 'stretch' },
                    items: [
                        gridCorreo,
                        gridNumero
                            ]
                }]
        });
        formAceptacionCliente.add(container);
        formAceptacionCliente.add(container2);
        formAceptacionCliente.add(container3);
    

}); 

function valida(){
    let retorno = true;
    if (Ext.getCmp('objComboTipoIdent').value == null ){
        //Ext.MessageBox.hide();
        Ext.MessageBox.show({
          title: 'Error',
          msg: 'Debe elegir el tipo de documento',
          buttons: Ext.MessageBox.OK,
          icon: Ext.MessageBox.ERROR
        });
        retorno = false;
    }
    if (Ext.getCmp('objComboTipoIdent').value == "CEDULA" || Ext.getCmp('objComboTipoIdent').value == "RUC") {
        
        var RegExPattern = /^([0-9]{10})$/;
        var cantidad = 10;
        if (Ext.getCmp('objComboTipoIdent').value == "RUC"){
            RegExPattern = /^([0-9]{13})$/;
            cantidad = 13
        }
        
        if ((Ext.getCmp('objTextIdentificacion').value.match(RegExPattern)) && (Ext.getCmp('objTextIdentificacion').value.value != ''))
        {
            retorno = true;
        }
        else
        {
            Ext.MessageBox.show({
                title: 'Error',
                msg: Ext.getCmp('objComboTipoIdent').value + " debe ser numérico y completar los " + cantidad + " Digitos",
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
              });
              retorno = false;
        }
    

    }
    return retorno;
}    

function buscar(){
        
        Ext.get(document.body).mask('Consultando datos...');
        Ext.Ajax.request({
            url: strUrlRegularizacionPersona,
            timeout: 1000000,
            method: 'POST',
            params: {
                tipoIdentificacion: Ext.getCmp('objComboTipoIdent').value,
                identificacion: Ext.getCmp('objTextIdentificacion').value
            },
    
            success: function(response) {
                
                Ext.get(document.body).unmask();
                var json = Ext.JSON.decode(response.responseText);
                if (json.status == 0)
                {
                    //Ext.Msg.alert('Validacion Exitosa ', json.message);
                    //window.location.href = strUrlIndex;
                    Ext.getCmp('nombres').setValue(json.data.persona.nombres);
                    Ext.getCmp('apellidos').setValue(json.data.persona.apellidos);
                    gridCorreo.getStore().removeAll();
                    gridNumero.getStore().removeAll();
                    for (var i = 0; i < json.data.formasContacto.length; i++) {
                        if (json.data.formasContacto[i].formaContactoId == 5){
                            gridCorreo.getStore().insert(0, json.data.formasContacto[i]);       
                        } else {
                            gridNumero.getStore().insert(0, json.data.formasContacto[i]);       
                        }
                    }
                    
                } 
                else
                {
                    Ext.getCmp('nombres').setValue("");
                    Ext.getCmp('apellidos').setValue("");
                    gridCorreo.getStore().removeAll();
                    gridNumero.getStore().removeAll();
                    Ext.Msg.alert('Error - Formas de Contacto Prospecto ', json.message);
                }
            },
            failure: function(result) {
                Ext.get(document.body).unmask();
                Ext.Msg.alert('Error - ', 'Error: ' + result.statusText);
            }
        });
    

}

function guardar(){
    Ext.MessageBox.show({
        icon: Ext.Msg.INFO,
        title:'Mensaje',
        msg: '¿Está seguro que desea enviar el link para solicitar la respuesta a las políticas y clásulas con las opciones "Si" y "No"?',
        buttons    : Ext.MessageBox.YESNO,
        buttonText: {yes: "Si"},
        fn: function(btn){
            if(btn=='yes'){
                Ext.get(document.body).mask('Guardando datos...');
                
                arrayCorreos = new Array(); 
                for (var i = 0; i < storeCorreos.getCount(); i++)
                {
                    arrayCorreos.push(storeCorreos.getAt(i).data);
                }
                arrayTelefonos = new Array();
                let contWS = 0; 
                for (var i2 = 0; i2 < storeTelefonos.getCount(); i2++)
                {
                    arrayTelefonos.push(storeTelefonos.getAt(i2).data);
                    if (storeTelefonos.getAt(i2).data.esWhatsapp == true ) {
                        contWS++;
                    }
                }
                if (contWS != 1 && enviaWhatsapp == "SI") {
                    Ext.Msg.alert('Error - ', 'Debe escoger sólo un número de whatsapp');
                    Ext.get(document.body).unmask();
                    return false;                    
                }
                Ext.Ajax.request({
                    url: strUrlGenerarCredencial,
                    timeout: 1000000,
                    method: 'POST',
                    params: {
                        arrayCorreos: Ext.JSON.encode(arrayCorreos),
                        arrayTelefonos: Ext.JSON.encode(arrayTelefonos),
                        tipoIdentificacion: Ext.getCmp('objComboTipoIdent').value,
                        identificacion: Ext.getCmp('objTextIdentificacion').value
                    },

                    success: function(response) {
                        Ext.get(document.body).unmask();
                        var json = Ext.JSON.decode(response.responseText);
                        console.log(json);
                        if (json.status == 0)
                        {
                            Ext.Msg.alert('Formas de contacto guardadas con exito ', json.message);
                            window.location.href = strUrlIndex;
                        } else
                        {
                            Ext.Msg.alert('Error - Formas de Contacto Prospecto ', json.message);
                        }
                    },
                    failure: function(result) {
                        Ext.get(document.body).unmask();
                        Ext.Msg.alert('Error - ', 'Error: ' + result.statusText);
                    }
                });


            } else {
                return false;
            }
        }    
    });  
}