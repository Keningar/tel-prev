var gridBaterias = null;

Ext.onReady(function()
{
    Ext.tip.QuickTipManager.init();
    
    /******************* Creacion Grid Baterias ******************/
    Ext.define('BateriasModel', 
    {
        extend: 'Ext.data.Model',
        fields: 
        [			
            {name:'intIdElemento',          mapping:'intIdElemento'},
            {name:'strNombreElemento',      mapping:'strNombreElemento'},
            {name:'strFechaCreacion',       mapping:'strFechaCreacion'},
            {name:'strTipoElemento',        mapping:'strTipoElemento'},
            {name:'strMarcaElemento',       mapping:'strMarcaElemento'},
            {name:'intIdMarcaElemento',     mapping:'intIdMarcaElemento'},
            {name:'intIdModeloElemento',    mapping:'intIdModeloElemento'},
            {name:'strModeloElemento',      mapping:'strModeloElemento'},
            {name:'strSerieFisica',         mapping:'strSerieFisica'},
            {name:'strAMPERAJE',            mapping:'strAMPERAJE'},
            {name:'strTIPO_BATERIA',        mapping:'strTIPO_BATERIA'},
            {name:'strFECHA_INSTALACION',   mapping:'strFECHA_INSTALACION'}
        ]
    });

    
    storeBaterias = Ext.create('Ext.data.Store', 
    {
        autoLoad: true,
        model: 'BateriasModel',        
        proxy: 
        {
            type: 'ajax',
            url: strUrlGetBaterias,
            reader: 
            {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams:
            {
                ups: intIdElementoUps
            }
        }
    });
    
    gridBaterias = Ext.create('Ext.grid.Panel', 
    {
        id:'gridBats',
        store: storeBaterias,
        columns: 
        [
            new Ext.grid.RowNumberer(),
            {
                id: 'intIdElemento',
                header: 'intIdElemento',
                dataIndex: 'intIdElemento',
                hidden: true,
                hideable: false
            },
            {
                id: 'strTIPO_BATERIA',
                header: 'Tipo',
                dataIndex: 'strTIPO_BATERIA',
                width: 100,
                sortable: true
            },
            {
                id: 'strAmperaje',
                header: 'Amperaje',
                dataIndex: 'strAMPERAJE',
                width: 94
            },
            {
                id: 'strSerieFisica',
                header: 'Serie Física',
                dataIndex: 'strSerieFisica',
                width: 180
            },
            {
                id: 'strModeloElemento',
                header: 'Modelo',
                dataIndex: 'strModeloElemento',
                width: 150,
                sortable: true
            },
            {
                id: 'strFECHA_INSTALACION',
                header: 'Fecha de Instalación',
                dataIndex: 'strFECHA_INSTALACION',
                width: 150
            }
        ],
        viewConfig:
        {
            stripeRows:true
        },
        width: 700,
        height: 200,
        title: 'Baterias',
        renderTo: 'gridBaterias'
    });
});
