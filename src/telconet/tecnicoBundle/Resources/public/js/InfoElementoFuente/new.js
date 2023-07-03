Ext.onReady(function()
{
    var dateFechaDesde = new Ext.form.DateField
    ({
        id: 'dateFechaInstalacion',
        fieldLabel: false,
        labelAlign: 'left',
        xtype: 'datefield',
        format: 'd-m-Y',
        width: 150,
        editable: false,
        name: 'dateFechaDesde',
        renderTo: 'fechaInstalacion'
    });
        
    var storeSwitches = new Ext.data.Store
    ({
        total: 'total',
        autoLoad: true,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetDispositivos,
            timeout: 400000,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'idElemento',     mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'}
            ]
    });
    
    
    var comboDispositivos = new Ext.form.ComboBox
    ({
        id: 'cmbDispositivo',
        name: 'cmbDispositivo',
        fieldLabel: false,
        editable: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Dispositivo',
        store: storeSwitches,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'dispositivo',
        listeners: 
        {
            select: 
            {
                fn: function(combo, value) 
                {
                    Ext.getCmp('cmbPuertos').reset();
                    Ext.getCmp('cmbPuertos').setDisabled(false);
                    
                    if( combo.getValue() != '' && combo.getValue() != null)
                    {
                        getPuertos(combo.getValue());
                    }
                }
            }
        },
        forceSelection: true
    });
    
    
    var storePuertos = new Ext.data.Store
    ({
        total: 'total',
        autoLoad: false,
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetPuertos,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name: 'id',     mapping: 'id'},
            {name: 'puerto', mapping: 'puerto'}
        ]
    });

    var comboPuertos = new Ext.form.ComboBox
    ({
        id: 'cmbPuertos',
        name: 'cmbPuertos',
        fieldLabel: false,
        disabled: true,
        editable: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Puerto',
        store: storePuertos,
        displayField: 'puerto',
        valueField: 'id',
        renderTo: 'puerto'
    });
    
    function getPuertos(switchId)
    {
        storePuertos.proxy.extraParams = {elemento: switchId};
        storePuertos.load();
    }
});


function verificarData()
{    
    Ext.MessageBox.wait("Guardando datos...");
    
    var dispositivo      = Ext.getCmp('cmbDispositivo').getValue();
    var puerto           = Ext.getCmp('cmbPuertos').getValue();
    var nombre           = $("#infoElementoFuente_nombreElemento").val();
        nombre           = nombre.trim();
    var continuar        = true;
    var fechaInstalacion = Ext.getCmp('dateFechaInstalacion').getValue();

    if( dispositivo == "" || dispositivo == null )
    {
        continuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un dispositivo");
        
        return false;
    }
    else if( puerto == "" || puerto == null )
    {
        continuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar un puerto");
        
        return false;
    }
    else if( fechaInstalacion == "" || fechaInstalacion == null )
    {
        continuar = false;
        
        Ext.MessageBox.hide();
        Ext.Msg.alert("Atención", "Debe seleccionar una fecha de instalación");
        
        return false;
    }
    
    if( continuar )
    {
        Ext.Ajax.request
        ({
            url: strUrlVerificarData,
            method: 'post',
            params: 
            {
                puerto: puerto,
                nombre: nombre
            },
            success: function(response)
            {
                var text = response.responseText;

                if(text === "OK")
                {
                    document.getElementById('intIdDispositivo').value     = dispositivo;
                    document.getElementById('intIdPuerto').value          = puerto;
                    document.getElementById('dateFechaInstalacion').value = fechaInstalacion;
                    document.getElementById("form_new_proceso").submit();
                }
                else
                {
                    Ext.MessageBox.hide();
                    Ext.Msg.alert('Error', text); 
                }
            },
            failure: function(result)
            {
                Ext.MessageBox.hide();
                Ext.Msg.alert('Error',result.responseText);
            }
        });
    }
    
    return false;
}
