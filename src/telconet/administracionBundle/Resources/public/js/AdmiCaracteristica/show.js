var gridOpciones = null;

Ext.onReady(function()
{
    Ext.define('Opciones', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [
            {name:'idParametroCab', mapping:'idParametroCab'},
            {name:'idParametroDet', mapping:'idParametroDet'},
            {name:'valorParametro', mapping:'valorParametro'}
        ]
    });
    
    var storeOpciones = Ext.create('Ext.data.Store', 
    {
        autoLoad: true,
        model: 'Opciones',        
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetOpcionesSeleccionable,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                idParametroCab: intIdParametroCab
            }
        }
    });
    
    gridOpciones = Ext.create('Ext.grid.Panel',
    {
        id:'gridOpciones',
        store: storeOpciones,
        columnLines: true,
        columns: 
        [
            {
                id: 'idParametroCab',
                header: 'idParametroCab',
                dataIndex: 'idParametroCab',
                hidden: true,
                hideable: false
            }, 
            {
                id: 'idParametroDet',
                header: 'idParametroDet',
                dataIndex: 'idParametroDet',
                hidden: true,
                hideable: false
            }, 
            {
                id: 'valorParametro',
                header: 'Opción',
                dataIndex: 'valorParametro',
                width: 288,
                sortable: true
            }
        ],
        viewConfig:
        {
            stripeRows:true
        },
        width: 300,
        height: 200,
        frame: true,
        title: 'Opciones Guardadas',
    });
    
    if( strTipoIngreso == 'Seleccionable' )
    {
        gridOpciones.render('gridSeleccionable');
    } 
});