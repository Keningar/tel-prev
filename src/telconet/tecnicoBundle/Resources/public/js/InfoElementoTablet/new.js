

Ext.onReady(function() { 
    Ext.define('ModelResponsableStore', 
    {
        extend: 'Ext.data.Model',
        fields:
        [				
            {name: 'intIdPerResponsableCmb',   mapping: 'idPersonaEmpresaRol'},
            {name: 'strResponsableCmb',        mapping: 'nombreCompleto'}

        ],
        idProperty: 'intIdPerResponsableCmb'
    });
    storeResponsables = new Ext.data.Store
    ({
        total: 'total',
        pageSize: 100,
        model: 'ModelResponsableStore',
        proxy:
        {
            type: 'ajax',
            method: 'post',
            url: strUrlGetResponsables,
            reader:
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                query              : ''
            },
            actionMethods: 
            {
                create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
            }
        },
        autoload: true
    });
    
    var cmbResponsable = new Ext.form.ComboBox
    ({
        fieldLabel: '*Responsable',
        labelWidth: 135,
        name: 'cmbResponsable',
        id: 'cmbResponsable',
        anchor: '100%',
        queryMode: 'remote',
        emptyText: 'Seleccione Responsable',
        width: 400,
        store: storeResponsables,
        displayField: 'strResponsableCmb',
        valueField: 'intIdPerResponsableCmb',
        renderTo: 'divResponsable',
        forceSelection: false,
        layout: 'anchor',
        disabled: false,
        listeners:
        {
            select: function(combo)
            {
                document.getElementById("strRegionPerResponsable").innerHTML       = "";
                document.getElementById("strCantonPerResponsable").innerHTML       = "";
                document.getElementById("strDepartamentoPerResponsable").innerHTML = "";
                Ext.Ajax.request
                ({
                    url: strUrlGetInfoPerResponsable,
                    method: 'post',
                    params:
                    { 
                        intIdPerResponsable: combo.getValue()
                    },
                    success: function(result)
                    {  
                        var objData = Ext.JSON.decode(result.responseText);
                        document.getElementById("strRegionPerResponsable").innerHTML       = objData.strRegionPerResponsable;
                        document.getElementById("strCantonPerResponsable").innerHTML       = objData.strCantonPerResponsable;
                        document.getElementById("strDepartamentoPerResponsable").innerHTML = objData.strDepartamentoPerResponsable;
                    },
                    failure: function(result)
                    {
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Error',result.responseText); 
                    }
                });
            }
        }
    });         
});

