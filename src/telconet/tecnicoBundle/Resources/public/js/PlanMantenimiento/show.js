Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    var arrayIndicesMantenimientos  = strIdsMantenimientos.split(",");
    var numMantenimientoShow=0;
    for (var i = 0; i < numMantenimientosPlan; i++) 
    {
        numMantenimientoShow++;
        var newDivMantenimientoTarea= $('<div/>', { id: 'div_gridTareas_'+i, class: 'div_mantenimiento'});
            newDivMantenimientoTarea.appendTo($('#mantenimientos_tareas'));
    
        var storeTareas = new Ext.data.Store({ 
            total: 'total',
            autoLoad:true,
            proxy: {
                type: 'ajax',
                url : urlGetTareasMantenimientosPlan,
                reader: {
                    type: 'json',
                    totalProperty: 'total',
                    root: 'encontrados'
                },
                extraParams:{
                    idMantenimiento: arrayIndicesMantenimientos[i]
                }
            },
            fields:
            [
                {name:'idTarea',            mapping: 'idTarea'},
                {name:'nombreTarea',        mapping:'nombreTarea'},
                {name:'frecuenciaTarea',    mapping:'frecuenciaTarea'},
                {name:'tipoFrecuenciaTarea',mapping:'tipoFrecuenciaTarea'},
                {name:'estado',             mapping:'estado'}
            ]
        });
        
        var gridTareas = Ext.create('Ext.grid.Panel', 
        {
            id:'gridTareas_'+i,
            store: storeTareas,
            columnLines: true,
            columns: 
            [
                Ext.create('Ext.grid.RowNumberer'),
                {
                    id: 'idTarea_'+i,
                    header: 'idTarea',
                    dataIndex: 'idTarea',
                    hidden: true,
                    hideable: false
                }, 
                {
                    id: 'nombreTarea_'+i,
                    header: 'Nombre Tarea',
                    dataIndex: 'nombreTarea',
                    width: 400,
                    sortable: true
                },
                {
                    id: 'frecuenciaTarea_'+i,
                    header: 'Frecuencia',
                    dataIndex: 'frecuenciaTarea',
                    width: 100
                },
                {
                    id: 'tipoFrecuenciaTarea_'+i,
                    header: 'Unidad de Medida',
                    dataIndex: 'tipoFrecuenciaTarea',
                    width: 100
                },
                {
                    id: 'estado_'+i,
                    header: 'Estado',
                    dataIndex: 'estado',
                    width: 70,
                    sortable: true
                }
            ],        
            viewConfig:{
                stripeRows:true
            },
            width: 850,
            height: 250,
            frame: true,
            title: 'Mantenimiento '+numMantenimientoShow,
            renderTo: 'div_gridTareas_'+i
        });
        
    }
    
});