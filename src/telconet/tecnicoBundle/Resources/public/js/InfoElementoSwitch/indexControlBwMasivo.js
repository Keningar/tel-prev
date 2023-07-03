/**
 * Funcion que sirve para cargar la data en el grid de la lista de ejecuciones del control bw masivo
 * 
 * @author Felix Caicedo <facaicedo@telconet.ec>
 * @version 1.0 10-11-2020
 * */
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    store = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        sorters: [{
            property: 'fecha_procesar',
            direction: 'DESC'
        }],
        autoDestroy: true,
        autoLoad: true,
        proxy: {
            timeout: 3000000,
            type: 'ajax',
            url : getEjecucionesControlBwMasivo,
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
        [
            {name:'id_ejecucion',       mapping:'id_ejecucion'},
            {name:'fecha_procesar',     mapping:'fecha_procesar'},
            {name:'total_sw',           mapping:'total_sw'},
            {name:'total_interfaces',   mapping:'total_interfaces'},
            {name:'usuario',            mapping:'usuario'},
            {name:'fecha_creacion',     mapping:'fecha_creacion'},
            {name:'fecha_inicio',       mapping:'fecha_inicio'},
            {name:'fecha_finalizacion', mapping:'fecha_finalizacion'},
            {name:'estado',             mapping:'estado'},
            {name:'ruta_archivo',       mapping:'ruta_archivo'}
        ]
    });
    
    grid = Ext.create('Ext.grid.Panel', {
        title: 'Lista de Ejecuciones Control Bw Masivo',
        width: '100%',
        height: 500,
        store: store,
        loadMask: true,
        frame: false,
        iconCls: 'icon-grid',
        viewConfig: { enableTextSelection: true },               
        columns:[
                {
                  id: 'id_ejecucion',
                  header: 'id_ejecucion',
                  dataIndex: 'id_ejecucion',
                  hidden: true,
                  hideable: false
                },
                {
                  id: 'ruta_archivo',
                  header: 'ruta_archivo',
                  dataIndex: 'ruta_archivo',
                  hidden: true,
                  hideable: false
                },
                {
                  header: 'Fecha Programada<br>(AAAA/MM/DD)',
                  dataIndex: 'fecha_procesar',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Total Switch',
                  dataIndex: 'total_sw',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Total Interfaces',
                  dataIndex: 'total_interfaces',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Usuario',
                  dataIndex: 'usuario',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Fecha Creación<br>(AAAA/MM/DD)',
                  dataIndex: 'fecha_creacion',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Fecha Inicio/Ejecución<br>(AAAA/MM/DD)',
                  dataIndex: 'fecha_inicio',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Fecha Fin/Ejecución<br>(AAAA/MM/DD)',
                  dataIndex: 'fecha_finalizacion',
                  width: '11%',
                  sortable: true
                },
                {
                  header: 'Estado',
                  dataIndex: 'estado',
                  width: '11%',
                  sortable: true
                },
                {
                    xtype: 'actioncolumn',
                    header: 'Acciones',
                    width: '11%',
                    items: [
                        //SHOW EJECUCION
                        {
                            getClass: function(v, meta, rec) {
                                return 'button-grid-show';
                            },
                            tooltip: 'Ver',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                window.location = ""+rec.get('id_ejecucion')+"/view";
                            }
                        },
                        //VIEW ARCHIVO
                        {
                            getClass: function(v, meta, rec) {
                                if( rec.get('ruta_archivo') != null ){
                                    return 'button-grid-direccionDown';
                                }
                                else{
                                    return 'button-grid-invisible';
                                }
                            },
                            tooltip: 'Descargar archivo csv adjunto',
                            handler: function(grid, rowIndex, colIndex) {
                                var rec = store.getAt(rowIndex);
                                var rutaFisica = rec.get('ruta_archivo');
                                if( rutaFisica != null ){
                                    var posicion = rutaFisica.indexOf('/public');
                                    window.open(rutaFisica.substring(posicion,rutaFisica.length));
                                }
                            }
                        }
                    ]
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
            store: store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        }),
        renderTo: 'grid'
    });
    store.load();
}); 
