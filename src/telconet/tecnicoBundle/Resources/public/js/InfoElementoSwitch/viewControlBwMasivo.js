/**
 * Funcion que sirve para cargar la data en el grid de la lista de ejecuciones del control bw masivo
 * 
 * @author Felix Caicedo <facaicedo@telconet.ec>
 * @version 1.0 10-11-2020
 * */
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    store = new Ext.data.Store({ 
        pageSize: 100000,
        total: 'total',
        sorters: [{
            property: 'strNombreElemento',
            direction: 'ASC'
        }],
        autoDestroy: true,
        autoLoad: true,
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url : getDetallesControlBwMasivo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'registros'
            },
            extraParams: {
                intIdEjecucion: intIdEjecucion
            }
        },
        fields:
        [
            {name:'strTipo',            mapping:'strTipo'},
            {name:'strNombreElemento',  mapping:'strNombreElemento'},
            {name:'strNombreInterface', mapping:'strNombreInterface'},
            {name:'strCapacidadUnoAnt', mapping:'strCapacidadUnoAnt'},
            {name:'strCapacidadDosAnt', mapping:'strCapacidadDosAnt'},
            {name:'strCapacidadUno',    mapping:'strCapacidadUno'},
            {name:'strCapacidadDos',    mapping:'strCapacidadDos'},
            {name:'strEstado',          mapping:'strEstado'},
            {name:'strObservacion',     mapping:'strObservacion'}
        ]
    });
    grid = Ext.create('Ext.grid.Panel', {
        title: 'Lista Ejecuciones Switch/Interfaces',
        width: '100%',
        height: 500,
        store: store,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',
        renderTo: Ext.get('grid_ejecuciones_control_bw_masivo'),
        columns:[
                {
                  header: 'Tipo',
                  dataIndex: 'strTipo',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Switch',
                  dataIndex: 'strNombreElemento',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Interface',
                  dataIndex: 'strNombreInterface',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Capacidad Up Anterior',
                  dataIndex: 'strCapacidadUnoAnt',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Capacidad Down Anterior',
                  dataIndex: 'strCapacidadDosAnt',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Capacidad Up Nueva',
                  dataIndex: 'strCapacidadUno',
                  width: '12%',
                  sortable: true
                },
                {
                  header: 'Capacidad Down Nueva',
                  dataIndex: 'strCapacidadDos',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'strEstado',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Observación',
                  dataIndex: 'strObservacion',
                  width: '12%',
                  sortable: true
                }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });
}); 
