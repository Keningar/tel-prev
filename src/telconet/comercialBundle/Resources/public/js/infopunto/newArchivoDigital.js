Ext.require([
    '*'
]);

Ext.define('ListModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name:'id', type:'int'},
        {name:'nombre', type:'string'}
    ]
});
Ext.onReady(function()
{
    storeDocumentos = Ext.create('Ext.data.Store',
    {
        model: 'ListModel',
        pageSize: 200,
        autoLoad: false,
        proxy:
            {
                type: 'ajax',
                url: url_lista_documentos,
                reader:
                    {
                        type: 'json',
                        root: 'registros'
                    }
            }
    });

    new Ext.form.ComboBox(
    {
        xtype: 'combobox',
        store: storeDocumentos,
        labelAlign: 'left',
        name: 'nombre',
        id: 'id',
        valueField: 'id',
        displayField: 'nombre',
        fieldLabel: '',
        width: 290,
        allowBlank: false,
        emptyText: 'Seleccione Tipo de Documento',
        disabled: false,
        renderTo: 'combo_documentos',
        listeners:
            {
                select:
                    {
                        fn: function(combo, value)
                        {
                            $('#infopuntoextratype_idTipoDocumento').val(combo.getValue());
                        }    
                    },
                click:
                    {
                        element: 'el',
                        fn: function()
                        {
                            storeDocumentos.load();
                        }
                    }
            }
    });


});

function validarFile() {
    const objectExampleData = $('.object-example-container').data('exampleObjects')
    console.log(objectExampleData);
    let id = $('#infopuntoextratype_idTipoDocumento').val();
    let boolRetorno = true;

    if(id == null || id == '' ){
        alert("Debe seleccionar el tipo de documento");
        return false;
    }

    for (var x of objectExampleData ){
        if (id == x.id){
            boolRetorno = window.confirm('Estimado usuario ya existe un archivo de tipo ' + x.descripcion + " subido a las " + x.fecha + ". ¿Desea continuar?");
            break;
        }
    }
    console.log(boolRetorno);
    return boolRetorno;
}