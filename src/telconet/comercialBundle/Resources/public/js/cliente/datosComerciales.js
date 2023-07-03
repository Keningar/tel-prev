Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.form.field.Number',
    'Ext.form.field.Date',
    'Ext.tip.QuickTipManager'
]);

Ext.define('serviciosModel', {
    extend: 'Ext.data.Model',
    idProperty: 'idServicio',
    fields: [
        {name: 'idServicio', type: 'string'},
        {name: 'idPunto', type: 'string'},
        {name: 'descripcionPunto', type: 'string'},
        {name: 'idProducto', type: 'string'},
        {name: 'descripcionProducto', type: 'string'},
        {name: 'cantidad', type: 'string'},
        {name: 'precioVenta', type: 'float'},
        {name: 'estado', type: 'string'},
        {name: 'fechaCreacion', type: 'string'},
    ]
});
/*"servicios":[{"idPunto":28,"descripcionPunto":"francechev 1","idProducto":84,"descripcionProducto":"Internet dedicado","cantidad":1,"fechaCreacion":"26\/11\/2012 11:06","precioVenta":240,"estado":"Pendiente"},
        {"idPunto":29,"descripcionPunto":"francechev 2","idProducto":84,"descripcionProducto":"Internet dedicado","cantidad":1,"fechaCreacion":"26\/11\/2012 11:06","precioVenta":200,"estado":"Pendiente"},
        {"idPunto":29,"descripcionPunto":"francechev 2","idProducto":"","descripcionProducto":"","cantidad":1,"fechaCreacion":"23\/11\/2012 17:02","precioVenta":350,"estado":"Pendiente"}]
*/
/*var data = [
    {projectId: 100, project: 'Ext Forms: Field Anchoring', taskId: 112, description: 'Integrate 2.0 Forms with 2.0 Layouts', estimate: 6, rate: 150, due:'06/24/2007'},
    {projectId: 100, project: 'Ext Forms: Field Anchoring', taskId: 113, description: 'Implement AnchorLayout', estimate: 4, rate: 150, due:'06/25/2007'},
    {projectId: 100, project: 'Ext Forms: Field Anchoring', taskId: 114, description: 'Add support for multiple types of anchors', estimate: 4, rate: 150, due:'06/27/2007'},
    {projectId: 100, project: 'Ext Forms: Field Anchoring', taskId: 115, description: 'Testing and debugging', estimate: 8, rate: 0, due:'06/29/2007'},
    {projectId: 101, project: 'Ext Grid: Single-level Grouping', taskId: 101, description: 'Add required rendering "hooks" to GridView', estimate: 6, rate: 100, due:'07/01/2007'},
    {projectId: 101, project: 'Ext Grid: Single-level Grouping', taskId: 102, description: 'Extend GridView and override rendering functions', estimate: 6, rate: 100, due:'07/03/2007'},
    {projectId: 101, project: 'Ext Grid: Single-level Grouping', taskId: 103, description: 'Extend Store with grouping functionality', estimate: 4, rate: 100, due:'07/04/2007'},
    {projectId: 101, project: 'Ext Grid: Single-level Grouping', taskId: 121, description: 'Default CSS Styling', estimate: 2, rate: 100, due:'07/05/2007'},
    {projectId: 101, project: 'Ext Grid: Single-level Grouping', taskId: 104, description: 'Testing and debugging', estimate: 6, rate: 100, due:'07/06/2007'},
    {projectId: 102, project: 'Ext Grid: Summary Rows', taskId: 105, description: 'Ext Grid plugin integration', estimate: 4, rate: 125, due:'07/01/2007'},
    {projectId: 102, project: 'Ext Grid: Summary Rows', taskId: 106, description: 'Summary creation during rendering phase', estimate: 4, rate: 125, due:'07/02/2007'},
    {projectId: 102, project: 'Ext Grid: Summary Rows', taskId: 107, description: 'Dynamic summary updates in editor grids', estimate: 6, rate: 125, due:'07/05/2007'},
    {projectId: 102, project: 'Ext Grid: Summary Rows', taskId: 108, description: 'Remote summary integration', estimate: 4, rate: 125, due:'07/05/2007'},
    {projectId: 102, project: 'Ext Grid: Summary Rows', taskId: 109, description: 'Summary renderers and calculators', estimate: 4, rate: 125, due:'07/06/2007'},
    {projectId: 102, project: 'Ext Grid: Summary Rows', taskId: 110, description: 'Integrate summaries with GroupingView', estimate: 10, rate: 125, due:'07/11/2007'},
    {projectId: 102, project: 'Ext Grid: Summary Rows', taskId: 111, description: 'Testing and debugging', estimate: 8, rate: 125, due:'07/15/2007'}
];*/

Ext.onReady(function(){
    
    Ext.tip.QuickTipManager.init();
    
                var store = Ext.create('Ext.data.JsonStore', {
                    model: 'serviciosModel',
                    groupField: 'descripcionPunto',
                    //pageSize: 10,
                    proxy: {
                        type: 'ajax',
                        url: url_servicios,
                        reader: {
                            type: 'json',
                            root: 'servicios',
                            //totalProperty: 'total'
                        },
                        extraParams:{idCli:''},
                        simpleSortMode: true
                    },
                    listeners: {
                        beforeload: function(store){
				//store.getProxy().extraParams.idCli= idCliente;   
                        }
                    }
                });
                
    store.load({params: {start: 0, limit: 10}});
     
   /* var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
    });*/
    var showSummary = true;
    var grid = Ext.create('Ext.grid.Panel', {
        width: 850,
        height: 450,
        frame: true,
        title: 'Detalle de Servicios',
        iconCls: 'icon-grid',
        renderTo: Ext.get('lista_datos_comerciales'),
        store: store,
        //plugins: [cellEditing],
        selModel: {
            selType: 'rowmodel'
        },
        listeners:{
            afterrender: function(grid, eOpts){
                    showSummary = !showSummary;
                    var view = grid.lockedGrid.getView();
                    view.getFeature('group').toggleSummaryRow(showSummary);
                    view.refresh();
                    view = grid.normalGrid.getView();
                    view.getFeature('group').toggleSummaryRow(showSummary);
                    view.refresh(); 
            }
        },
        dockedItems: [{
            dock: 'top',
            xtype: 'toolbar',
            items: [/*{
                tooltip: 'Toggle the visibility of the summary row',
                text: 'Toggle Summary',
                enableToggle: true,
                pressed: true,
                handler: function(){
                    showSummary = !showSummary;
                    var view = grid.lockedGrid.getView();
                    view.getFeature('group').toggleSummaryRow(showSummary);
                    view.refresh();
                    view = grid.normalGrid.getView();
                    view.getFeature('group').toggleSummaryRow(showSummary);
                    view.refresh();
                }
            }*/]
        }],
        features: [{
            id: 'group',
            ftype: 'groupingsummary',
            groupHeaderTpl: '{name}',
            hideGroupedHeader: true,
            enableGroupingMenu: false
        }],
        columns: [new Ext.grid.RowNumberer(),{
            text: 'Producto',
            width: 300,
            locked: false,
            tdCls: 'task',
            sortable: true,
            dataIndex: 'descripcionProducto',
            hideable: false,
            summaryType: 'count',
            summaryRenderer: function(value, summaryData, dataIndex) {
                return ((value === 0 || value > 1) ? '(' + value + ' Servicios)' : '(1 Servicio)');
            }
        }, {
            header: 'Cantidad',
            width: 130,
            sortable: true,
            //renderer: Ext.util.Format.usMoney,
            //summaryRenderer: Ext.util.Format.usMoney,
            dataIndex: 'cantidad',
            summaryType: 'count'
        }, {
            header: 'Precio',
            width: 130,
            sortable: true,
            renderer: Ext.util.Format.usMoney,
            summaryRenderer: Ext.util.Format.usMoney,
            dataIndex: 'precioVenta',
            summaryType: 'average'
        }, {
            header: 'Estado',
            width: 130,
            sortable: true,
            dataIndex: 'estado',
            summaryType: 'max'/*,
            renderer: function(value, metaData, record, rowIdx, colIdx, store, view){
                return value;
            },
            summaryRenderer: function(value, summaryData, dataIndex) {
                return value;
            }*/
        }, {
            header: 'Fecha creacion',
            width: 130,
            sortable: true,
            dataIndex: 'fechaCreacion',
            summaryType: 'max',
            renderer: function(value, metaData, record, rowIdx, colIdx, store, view){
                return value ;
            },
            summaryRenderer: function(value, summaryData, dataIndex) {
                return value ;
            }
        }]
    });
    
  
});