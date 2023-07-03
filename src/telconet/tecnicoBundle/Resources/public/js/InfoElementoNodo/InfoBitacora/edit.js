var comboElemento = undefined;
var storeElementoRelacionado = undefined;

Ext.onReady(function() {

    $('#telconet_schemabundle_infobitacoraaccesonodotype_verificarEstadoNodo').prop('checked', true);

    $('#telconet_schemabundle_infobitacoraaccesonodotype_canton').attr('disabled','disabled');
    $('#telconet_schemabundle_infobitacoraaccesonodotype_departamento').attr('disabled','disabled');
    $('#telconet_schemabundle_infobitacoraaccesonodotype_tecnicoAsignado').attr('disabled','disabled');
    $('#telconet_schemabundle_infobitacoraaccesonodotype_tareaId').attr('disabled','disabled');
    $('#telconet_schemabundle_infobitacoraaccesonodotype_elementoNodoNombre').attr('disabled','disabled');
    
    storeElementoRelacionado = new Ext.data.Store({
        total: 10,    
        proxy: 
        {
            type: 'ajax',
            timeout: 180000,
            url: strUrlGetElementosBitacora,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            }
        },
        fields:
        [
            {name: 'elemento_id',                 mapping: 'elemento_id'},
            {name: 'nombre_elemento_relacionado', mapping: 'nombre_elemento'}
        ]
    });

    comboElemento = new Ext.form.ComboBox({
        id: 'combo_elemento',
        name: 'combo_elemento',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        minChars : 5,
        store: storeElementoRelacionado,
        width: 250,
        displayField: 'nombre_elemento_relacionado',
        valueField: 'elemento_id',
        renderTo: 'combo_elemento',
        listeners: {
            select: { fn: function(combo, value) {
                $('#telconet_schemabundle_infobitacoraaccesonodotype_elemento').val(combo.getValue());
            }}
        }
    });

});

function validacionesForm() {

    return true;
}
