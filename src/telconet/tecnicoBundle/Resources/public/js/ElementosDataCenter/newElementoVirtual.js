
function agregarElementoVirtual()
{
    var jsonInformacion = {};
    
    var formPanel = Ext.create('Ext.form.Panel', {
        bodyPadding: 2,
        waitMsgTarget: true,                
        width:500,
        layout: {
            type: 'table',
            columns: 1
        },
        items: 
        [
            {
                id: 'containerPuertosNexus',
                width:470,
                xtype: 'fieldset',
                title: '<i class="fa fa-tag" aria-hidden="true"></i>&nbsp;<b>Agregar Nuevo elemento virtual</b>',
                defaultType: 'textfield',
                items: [
                {
                    xtype: 'container',
                    layout: {
                        type: 'table',
                        columns: 1,
                        align: 'stretch'
                    },
                    items: 
                    [
                        {
                            xtype:           'combobox',
                            name:            'cmbCiudadDC',
                            id:              'cmbCiudadDC',
                            fieldLabel:      '<i class="fa fa-th" aria-hidden="true"></i>&nbsp;<b>Data Center</b>',
                            store:           storeDataCenter,
                            displayField:    'nombreElemento',                                                
                            valueField:      'id',
                            editable:        false,
                            width:           400,
                            listeners: 
                            {
                                change: function (combo) 
                                {
                                    Ext.getCmp('txtElementoVirtual').setDisabled(true);
                                    Ext.getCmp('cmbElementoPadre').setDisabled(true);
                                    Ext.getCmp('txtElementoPadre').setDisabled(true);
                                    Ext.getCmp('txtElementoVirtual').setValue("");
                                    Ext.getCmp('cmbElementoPadre').setValue("");
                                    Ext.getCmp('cmbElementoPadre').setRawValue("");
                                    Ext.getCmp('txtElementoPadre').setValue("");
                                    
                                    
                                    Ext.get('winElementoVirtual').mask('Cargando Información');
                                    
                                    //Cargar la información relacionada
                                    Ext.Ajax.request({
                                        url: url_ajaxGetTiposElementosDataCenter,
                                        method: 'post',
                                        timeout: 1000000,
                                        success: function(response)
                                        {                                
                                            Ext.get('winElementoVirtual').unmask();
                                            Ext.getCmp('cmbTipoElementoDC').setDisabled(false);                                            
                                            
                                            jsonInformacion = Ext.JSON.decode(response.responseText);
                                            
                                            var arrayTiposVirtuales = jsonInformacion.filter(function(elem){        
                                                return elem.clasificacion === 'VIRTUAL';
                                            });
                                                                                        
                                            var array = [];
                                            Ext.each(arrayTiposVirtuales,function(value)
                                            {                                                
                                                var json      = {};
                                                json['id']    = value.tipo;
                                                json['value'] = value.tipo;
                                                array.push(json);
                                            });
                                            
                                            var store = new Ext.data.Store({
                                                fields: ['id','value'],
                                                data: array
                                            });
                                           
                                            Ext.getCmp('cmbTipoElementoDC').bindStore(store);
                                        },
                                        failure:function()
                                        {
                                            Ext.get('winElementoVirtual').unmask();
                                        }
                                    });
                                }
                            }
                        },
                        {   width: 25,border: false   },
                        {                            
                            xtype:          'combobox',
                            id:             'cmbTipoElementoDC',
                            name:           'cmbTipoElementoDC',   
                            displayField:    'value',                                                
                            valueField:      'id',
                            disabled:       true,                           
                            editable:       false,
                            fieldLabel:     '<i class="fa fa-gg" aria-hidden="true"></i>&nbsp;<b>Tipo Elemento</b>',
                            width:          400,
                            listeners: 
                            {
                                change: function (combo) 
                                {
                                    Ext.getCmp('txtElementoVirtual').setDisabled(true);
                                    Ext.getCmp('cmbElementoPadre').setDisabled(true);
                                    Ext.getCmp('txtElementoPadre').setDisabled(true);
                                    Ext.getCmp('txtElementoVirtual').setValue("");
                                    Ext.getCmp('cmbElementoPadre').setValue("");
                                    Ext.getCmp('cmbElementoPadre').setRawValue("");
                                    Ext.getCmp('txtElementoPadre').setValue("");
                                    
                                    var opcion = combo.getValue();
                                    
                                    Ext.each(jsonInformacion, function(value){
                                        if(value.tipo === opcion)
                                        {
                                            Ext.getCmp('txtElementoPadre').setDisabled(false);
                                            
                                            if(!Ext.isEmpty(value.padre))
                                            {
                                                Ext.getCmp('txtElementoPadre').setValue(value.padre); 
                                                
                                                Ext.get('winElementoVirtual').mask('Cargando Información');
                                                Ext.Ajax.request({
                                                    url: url_ajaxGetElementosDataCenter,
                                                    method: 'post',
                                                    timeout: 1000000,
                                                    params: 
                                                    { 
                                                        tipo:       value.padre,
                                                        dataCenter: Ext.getCmp('cmbCiudadDC').getValue()
                                                    },
                                                    success: function(response)
                                                    {
                                                        Ext.get('winElementoVirtual').unmask();
                                                        Ext.getCmp('txtElementoVirtual').setDisabled(false);
                                                        Ext.getCmp('cmbElementoPadre').setDisabled(false);

                                                        var arrayJson = Ext.JSON.decode(response.responseText);
                                                        var array     = [];

                                                        Ext.each(arrayJson, function(value){
                                                            var json      = {};
                                                            json['id']    = value.idElemento;
                                                            json['value'] = value.nombreElemento;
                                                            array.push(json);
                                                        });                    

                                                        var store = new Ext.data.Store({
                                                            fields: ['id','value'],
                                                            data: array
                                                        });

                                                        Ext.getCmp('cmbElementoPadre').bindStore(store);
                                                    }
                                                }); 
                                            }
                                            else
                                            {
                                                Ext.getCmp('txtElementoPadre').setValue("-"); 
                                                Ext.getCmp('txtElementoVirtual').setDisabled(false);
                                            }                                                                                                                                           
                                        }
                                    });                                                                        
                                    //Obtener los elementos ligados a la ciudad segun si existe un padre
                                }
                            }
                        },
                        {   width: 25,border: false   },
                        {                            
                            xtype:          'textfield',
                            id:             'txtElementoPadre',
                            name:           'txtElementoPadre',      
                            fieldStyle:     'color:green;font-style: italic;font-weight:bold;',
                            editable:       false,
                            fieldLabel:     '<i class="fa fa-gg" aria-hidden="true"></i>&nbsp;<b>Tipo Elemento Padre</b>',
                            width:          400,
                            disabled:       true,
                            store:[]
                        },
                        {   width: 25,border: false   },
                        {                            
                            xtype:          'combobox',
                            id:             'cmbElementoPadre',
                            name:           'cmbElementoPadre',  
                            displayField:    'value',                                                
                            valueField:      'id',
                            disabled:       true,
                            fieldLabel:     '<i class="fa fa-gg" aria-hidden="true"></i>&nbsp;<b>Elemento Padre</b>',
                            width:          400,
                            listeners: 
                            {
                                change: function (combo) 
                                {
                                    Ext.getCmp('txtElementoVirtual').setValue("");
                                }
                            }
                        },                        
                        {   width: 25,border: false   },
                        {                                                       
                            xtype:          'textfield',
                            id:             'txtElementoVirtual',
                            name:           'txtElementoVirtual',
                            disabled:        true,
                            fieldLabel:     '<i class="fa fa-gg" aria-hidden="true"></i>&nbsp;<b>Elemento</b>',
                            width:          400
                        }                        
                    ]
                }]
            }        
        ],
        buttons:
            [
                {
                    text: '<i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;Guardar',
                    handler: function ()
                    {
                        guardarElementoVirtual();
                    }
                },
                {
                    text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                    handler: function ()
                    {
                        win.destroy();
                    }
                }
            ]
    });
    
    var win = Ext.create('Ext.window.Window', {
        title: 'Agregar nuevo elemento virtual',
        id:'winElementoVirtual',
        modal: true,
        width: 500,
        closable: true,
        layout: 'fit',
        items: [formPanel]
    }).show();
}

function guardarElementoVirtual()
{
    Ext.get('winElementoVirtual').mask('Guardando Información...');
    Ext.Ajax.request({
        url: url_ajaxGuardarElementoVirtual,
        method: 'post',
        timeout: 1000000,
        params: 
        { 
            idElementoPadre:   Ext.getCmp('cmbElementoPadre').getValue(),
            nombreElemento:    Ext.getCmp('txtElementoVirtual').getValue(),
            tipoElemento:      Ext.getCmp('cmbTipoElementoDC').getRawValue(),
            dataCenter:        Ext.getCmp('cmbCiudadDC').getValue()
        },
        success: function(response)
        {
            Ext.get('winElementoVirtual').unmask();
            
            var json = Ext.JSON.decode(response.responseText);
            
            if(json.status === 'OK')
            {
                Ext.Msg.alert('Mensaje',json.mensaje, function(btn){
                    if(btn=='ok')
                    {
                        Ext.getCmp('winElementoVirtual').destroy();
                        buscar();
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Alerta',json.mensaje);
            }
        }
    });
}


