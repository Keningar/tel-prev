/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var winVerAsignaciones;
var winVerHistorialAsignaciones;

/************************************************************************ */
/************************** VER ASIGNACIONES ********************************* */
/************************************************************************ */
function showVerAsignaciones(idDetalleSolicitud)
{
    winVerAsignaciones="";

    if (!winVerAsignaciones)
    {
        storeTareasAsignadas = new Ext.data.Store({ 
            pageSize: 10,
            total: 'total',
            proxy: {
                type: 'ajax',
                url : 'gridTareasAsignadas',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    id_detalle_solicitud: idDetalleSolicitud,
                    estado: 'Todos'
                }
            },
            fields:
                    [
                        {name:'id_info_detalle', mapping:'id_info_detalle'},
                        {name:'id_detalle_solicitud', mapping:'id_detalle_solicitud'},
                        {name:'id_tarea', mapping:'id_tarea'},
                        {name:'id_proceso', mapping:'id_proceso'},
                        {name:'id_asignado', mapping:'id_asignado'},
                        {name:'ref_id_asignado', mapping:'ref_id_asignado'},
                        {name:'nombre_tarea', mapping:'nombre_tarea'},
                        {name:'nombre_proceso', mapping:'nombre_proceso'},
                        {name:'nombre_asignado', mapping:'nombre_asignado'},
                        {name:'ref_nombre_asignado', mapping:'ref_nombre_asignado'},
                        {name:'latidud', mapping:'latidud'},
                        {name:'longitud', mapping:'longitud'},
                        {name:'estado', mapping:'estado'},
                        {name:'action1', mapping:'action1'},
                        {name:'action2', mapping:'action2'},
                        {name:'action3', mapping:'action3'}               
                    ],
            autoLoad: true
        });
        
        gridTareasAsignadas = Ext.create('Ext.grid.Panel', {
            width: 780,
            height: 450,
            store: storeTareasAsignadas,
            loadMask: true,
            frame: false,
            columns:[
                {
                    id: 'id_info_detalle',
                    header: 'IdInfoDetalle',
                    dataIndex: 'id_info_detalle',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_tarea',
                    header: 'IdTarea',
                    dataIndex: 'id_tarea',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_proceso',
                    header: 'IdProceso',
                    dataIndex: 'id_proceso',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_asignado',
                    header: 'IdAsignado',
                    dataIndex: 'id_asignado',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'ref_id_asignado',
                    header: 'RefIdAsignado',
                    dataIndex: 'ref_id_asignado',
                    hidden: true,
                    hideable: false
                },                  
                /*{
                    id: 'nombre_proceso',
                    header: 'Nombre Proceso',
                    dataIndex: 'nombre_proceso',
                    width: 190,
                    sortable: true
                },  */            
                {
                    id: 'nombre_tarea',
                    header: 'Nombre Tarea',
                    dataIndex: 'nombre_tarea',
                    width: 170,
                    sortable: true
                },             
                {
                    id: 'nombre_asignado',
                    header: 'Nombre Asignado',
                    dataIndex: 'nombre_asignado',
                    width: 300,
                    sortable: true
                },             
                {
                    id: 'ref_nombre_asignado',
                    header: 'Ref Nombre Asignado',
                    dataIndex: 'ref_nombre_asignado',
                    width: 300,
                    sortable: true
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeTareasAsignadas,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            })
        });
    
        formPanelAsignaciones = Ext.create('Ext.form.Panel', {
            width:800,
            height:490,
            BodyPadding: 10,
            frame: true,
            items: [ gridTareasAsignadas ]
        });
        
	winVerAsignaciones = Ext.widget('window', {
            title: 'Tareas Asignadas',
            width: 810,
            height:500,
            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            items: [formPanelAsignaciones]
        });
    }                        
                         
    winVerAsignaciones.show();
    
}

function cierraVentanaAsignaciones(){
    winVerAsignaciones.close();
    winVerAsignaciones.destroy();
}

function retornaPanelAsignaciones(idDetalleSolicitud)
{
    var storeTareasAsignadas2 = new Ext.data.Store({ 
        pageSize: 10,
        total: 'total',
        proxy: {
            type: 'ajax',
            url : 'gridTareasAsignadas',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                id_detalle_solicitud: idDetalleSolicitud,
                estado: 'Todos'
            }
        },
        fields:
                [
                    {name:'id_info_detalle', mapping:'id_info_detalle'},
                    {name:'id_detalle_solicitud', mapping:'id_detalle_solicitud'},
                    {name:'id_tarea', mapping:'id_tarea'},
                    {name:'id_proceso', mapping:'id_proceso'},
                    {name:'id_asignado', mapping:'id_asignado'},
                    {name:'ref_id_asignado', mapping:'ref_id_asignado'},
                    {name:'nombre_tarea', mapping:'nombre_tarea'},
                    {name:'nombre_proceso', mapping:'nombre_proceso'},
                    {name:'nombre_asignado', mapping:'nombre_asignado'},
                    {name:'ref_nombre_asignado', mapping:'ref_nombre_asignado'},
                    {name:'latidud', mapping:'latidud'},
                    {name:'longitud', mapping:'longitud'},
                    {name:'estado', mapping:'estado'},
                    {name:'action1', mapping:'action1'},
                    {name:'action2', mapping:'action2'},
                    {name:'action3', mapping:'action3'}               
                ],
        autoLoad: true
    });
        
    var gridTareasAsignadas2 = Ext.create('Ext.grid.Panel', {
        width: 680,
        height: 250,
        store: storeTareasAsignadas2,
        loadMask: true,
        frame: false,
        columns:[
            {
                id: 'id_info_detalle',
                header: 'IdInfoDetalle',
                dataIndex: 'id_info_detalle',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_tarea',
                header: 'IdTarea',
                dataIndex: 'id_tarea',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_proceso',
                header: 'IdProceso',
                dataIndex: 'id_proceso',
                hidden: true,
                hideable: false
            },
            {
                id: 'id_asignado',
                header: 'IdAsignado',
                dataIndex: 'id_asignado',
                hidden: true,
                hideable: false
            },    
            {
                id: 'ref_id_asignado',
                header: 'RefIdAsignado',
                dataIndex: 'ref_id_asignado',
                hidden: true,
                hideable: false
            },               
            /*{
                id: 'nombre_proceso',
                header: 'Nombre Proceso',
                dataIndex: 'nombre_proceso',
                width: 190,
                sortable: true
            },  */            
            {
                id: 'nombre_tarea',
                header: 'Nombre Tarea',
                dataIndex: 'nombre_tarea',
                width: 160,
                sortable: true
            },             
            {
                id: 'nombre_asignado',
                header: 'Nombre Asignado',
                dataIndex: 'nombre_asignado',
                width: 300,
                sortable: true
            },             
            {
                id: 'ref_nombre_asignado',
                header: 'Ref Nombre Asignado',
                dataIndex: 'ref_nombre_asignado',
                width: 300,
                sortable: true
            }
        ],
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeTareasAsignadas2,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay datos que mostrar."
        })
    });
    /*
    var formPanelAsignaciones2 = Ext.create('Ext.form.Panel', {
        width:700,
        height:290,
        BodyPadding: 10,
        frame: true,
        items: [ gridTareasAsignadas2 ]
    });*/
    
    return gridTareasAsignadas2;
}


/************************************************************************ */
/************************** VER ASIGNACIONES ********************************* */
/************************************************************************ */
function showVerHistorialAsignaciones(idDetalleSolicitud)
{
    winVerHistorialAsignaciones="";

    if (!winVerHistorialAsignaciones)
    {
        storeHistorialAsignaciones = new Ext.data.Store({ 
            pageSize: 15,
            total: 'total',
            proxy: {
                type: 'ajax',
                url : 'gridHistorialTareasAsignadas',
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams: {
                    id_detalle_solicitud: idDetalleSolicitud,
                    estado: 'Todos'
                }
            },
            fields:
                    [
                        {name:'id_info_detalle', mapping:'id_info_detalle'},
                        {name:'id_detalle_solicitud', mapping:'id_detalle_solicitud'},
                        {name:'id_tarea', mapping:'id_tarea'},
                        {name:'id_proceso', mapping:'id_proceso'},
                        {name:'id_asignado', mapping:'id_asignado'},
                        {name:'ref_id_asignado', mapping:'ref_id_asignado'},
                        {name:'nombre_tarea', mapping:'nombre_tarea'},
                        {name:'nombre_proceso', mapping:'nombre_proceso'},
                        {name:'nombre_asignado', mapping:'nombre_asignado'},
                        {name:'ref_nombre_asignado', mapping:'ref_nombre_asignado'},
                        {name:'fecha_asignada', mapping:'fecha_asignada'},
                        {name:'latidud', mapping:'latidud'},
                        {name:'longitud', mapping:'longitud'},
                        {name:'estado', mapping:'estado'},
                        {name:'action1', mapping:'action1'},
                        {name:'action2', mapping:'action2'},
                        {name:'action3', mapping:'action3'}               
                    ],
            autoLoad: true
        });
        
        gridHistorialAsignaciones = Ext.create('Ext.grid.Panel', {
            width: 930,
            height: 450,
            store: storeHistorialAsignaciones,
            loadMask: true,
            frame: false,
            columns:[
                {
                    id: 'id_info_detalle',
                    header: 'IdInfoDetalle',
                    dataIndex: 'id_info_detalle',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_tarea',
                    header: 'IdTarea',
                    dataIndex: 'id_tarea',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_proceso',
                    header: 'IdProceso',
                    dataIndex: 'id_proceso',
                    hidden: true,
                    hideable: false
                },
                {
                    id: 'id_asignado',
                    header: 'IdAsignado',
                    dataIndex: 'id_asignado',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'ref_id_asignado',
                    header: 'RefIdAsignado',
                    dataIndex: 'ref_id_asignado',
                    hidden: true,
                    hideable: false
                },                  
                /*{
                    id: 'nombre_proceso',
                    header: 'Nombre Proceso',
                    dataIndex: 'nombre_proceso',
                    width: 190,
                    sortable: true
                },  */            
                {
                    id: 'nombre_tarea',
                    header: 'Nombre Tarea',
                    dataIndex: 'nombre_tarea',
                    width: 170,
                    sortable: true
                },             
                {
                    id: 'nombre_asignado',
                    header: 'Nombre Asignado',
                    dataIndex: 'nombre_asignado',
                    width: 300,
                    sortable: true
                },             
                {
                    id: 'ref_nombre_asignado',
                    header: 'Ref Nombre Asignado',
                    dataIndex: 'ref_nombre_asignado',
                    width: 300,
                    sortable: true
                },             
                {
                    id: 'fecha_asignada',
                    header: 'Fecha Asignada',
                    dataIndex: 'fecha_asignada',
                    width: 120,
                    sortable: true
                }
            ],
            bbar: Ext.create('Ext.PagingToolbar', {
                store: storeHistorialAsignaciones,
                displayInfo: true,
                displayMsg: 'Mostrando {0} - {1} de {2}',
                emptyMsg: "No hay datos que mostrar."
            })
        });
    
        formPanelHistorialAsignaciones = Ext.create('Ext.form.Panel', {
            width:950,
            height:490,
            BodyPadding: 10,
            frame: true,
            items: [ gridHistorialAsignaciones ]
        });
        
	winVerHistorialAsignaciones = Ext.widget('window', {
            title: 'Historial Tareas Asignadas',
            width: 960,
            height:500,
            minHeight: 380,
            layout: 'fit',
            resizable: false,
            modal: true,
            closabled: false,
            items: [formPanelHistorialAsignaciones]
        });
    }                        
                         
    winVerHistorialAsignaciones.show();    
}

function cierraVentanaHistorialAsignaciones(){
    winVerHistorialAsignaciones.close();
    winVerHistorialAsignaciones.destroy();
}