Ext.onReady(function() {      

var numeroTarea    = $('#numeroTarea').val();
var cantidadTareas = $('#cantidadTareas').val();

if(cantidadTareas > 0)
{
    //Grid SubTareas
     storeSubtareas = new Ext.data.Store({
     pageSize: 10,
         total: 'total',
         proxy: {
         type: 'ajax',
             url : strUrlGetSubtareas,
             reader: {
                 type: 'json',
                 totalProperty: 'total',
                 root: 'encontrados'
             },
             extraParams: {
             comunicacionId: numeroTarea,
             }
         },
         fields:
         [
             {name: 'numero_tarea', mapping: 'numero_tarea'},
             {name: 'nombre_tarea', mapping: 'nombre_tarea'},
             {name: 'observacion',  mapping: 'observacion'},
             {name: 'responsable',  mapping: 'responsable'},
             {name: 'fecha_ejecu',  mapping: 'fecha_ejecu'},
             {name: 'estado',       mapping: 'estado'},            
         ],
         autoLoad: true
     });    


     gridSubtareas = Ext.create('Ext.grid.Panel', {
     width: "100%",
         height: 300,
         title:'Subtareas',
         store: storeSubtareas,
         viewConfig: {enableTextSelection: true, emptyText: 'No hay datos para mostrar'},
             loadMask: true,
             frame: false,
             columns:
             [
                 {
                 id: 'numero_tarea',
                     header: 'Número de Tarea',
                     dataIndex: 'numero_tarea',
                     width: 100,
                     sortable: true
                 },
                 {
                 id: 'nombre_tarea',
                     header: 'Nombre Tarea',
                     dataIndex: 'nombre_tarea',
                     width: 300,
                     sortable: true
                 },
                 {
                 id: 'observacion',
                     header: 'Observación',
                     dataIndex: 'observacion',
                     width: 250,
                     sortable: true
                 },
                 {
                 id: 'responsable',
                     header: 'Responsable',
                     dataIndex: 'responsable',
                     width: 200,
                     sortable: true
                 },
                 {
                 id: 'fecha_ejecu',
                     header: 'Fecha Ejecución',
                     dataIndex: 'fecha_ejecu',
                     width: 150,
                     sortable: true
                 },
                 {
                 id: 'estado',
                     header: 'Estado',
                     dataIndex: 'estado',
                     width: 100,
                     sortable: true
                 }
             ],
             renderTo: 'grid_Subtareas'
         });
}
});