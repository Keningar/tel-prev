/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();
    
    var storeTareas = new Ext.data.Store({ 
        total: 'total',
        autoLoad:true,
        proxy: {
            type: 'ajax',
            url : 'getTareas',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
              [
                {name:'idTarea', mapping: 'idTarea'},
                {name:'nombreTarea', mapping:'nombreTarea'},
                {name:'nombreTareaAnterior', mapping:'nombreTareaAnterior'},
                {name:'nombreTareaSiguiente', mapping:'nombreTareaSiguiente'},
                {name:'tiempoMax', mapping:'tiempoMax'},
                {name:'costo', mapping:'costo'},
                {name:'peso', mapping:'peso'},
                {name:'estado', mapping:'estado'},
                {name:'action1', mapping:'action1'}
              ]
    });
    
    
    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridTareas = Ext.create('Ext.grid.Panel', {
        id:'gridTareas',
        store: storeTareas,
        columnLines: true,
        columns: [Ext.create('Ext.grid.RowNumberer'),
        {
            id: 'idTarea',
            header: 'idTarea',
            dataIndex: 'idTarea',
            hidden: true,
            hideable: false
        }, {
            id: 'nombreTarea',
            header: 'Nombre Tarea',
            dataIndex: 'nombreTarea',
            width: 190,
            sortable: true
        },{
            id: 'nombreTareaAnterior',
            header: 'Tarea Anterior',
            dataIndex: 'nombreTareaAnterior',
            width: 180,
            sortable: true
        },{
            id: 'nombreTareaSiguiente',
            header: 'Tarea Siguiente',
            dataIndex: 'nombreTareaSiguiente',
            width: 180,
            sortable: true
        },{
            id: 'tiempoMax',
            header: 'Tiempo Maximo',
            dataIndex: 'tiempoMax',
            width: 90,
            sortable: true
        },{
            id: 'peso',
            header: 'Peso %',
            dataIndex: 'peso',
            width: 50,
            sortable: true
        },{
            id: 'estado',
            header: 'Estado',
            dataIndex: 'estado',
            width: 70,
            sortable: true
        },{
            xtype: 'actioncolumn',
			header: 'Acciones',
			width: 50,
			items: [
				{
					getClass: function(v, meta, rec) {
						var permiso = $("#ROLE_51-6");
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);							
						if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
						
						if (rec.get('action1') == "icon-invisible") 
							this.items[0].tooltip = '';
						else 
							this.items[0].tooltip = 'Ver';
						
						if (rec.get('estado') == "Eliminado") 
							rec.data.action1 = "icon-invisible";
						else 
							this.items[0].tooltip = 'Ver';
							
						return rec.get('action1')
					},
					tooltip: 'Ver',
					handler: function(grid, rowIndex, colIndex) {
						var rec = storeTareas.getAt(rowIndex);
							
						var permiso = $("#ROLE_51-6");
						var boolPermiso = (typeof permiso === 'undefined') ? false : (permiso.val() == 1 ? true : false);
						if(!boolPermiso){ rec.data.action1 = "icon-invisible"; }
														
						if(rec.get('action1')!="icon-invisible")
							window.location = "../../admi_tarea/"+rec.get('idTarea')+"/show";
						else
							Ext.Msg.alert('Error ','No tiene permisos para realizar esta accion');
					}
				}
			]
        }],        
        viewConfig:{
            stripeRows:true
        },
        width: 850,
        height: 250,
        frame: true,
        title: 'Tareas del Proceso',
        renderTo: 'grid'
    });
                  
    /**************************************************/
    
});
