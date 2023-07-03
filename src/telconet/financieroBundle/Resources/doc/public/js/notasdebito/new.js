/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function(){

var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
    clicksToEdit: 1
});

Ext.define('ListadoDetalleOrden', {
    extend: 'Ext.data.Model',
    fields: [
			 {name:'idmotivo', type: 'int'},
			 {name:'motivo', type: 'string'},
			 {name:'observacion', type: 'string'},
             {name:'valor', type: 'string'},
			 {name:'idpagodet', type: 'int'}
            ]
}); 

 store = Ext.create('Ext.data.Store', {
    // destroy the store if the grid is destroyed
    autoDestroy: true,
    model: 'ListadoDetalleOrden',
    proxy: {
        type: 'memory',
        // load remote data using HTTP
        reader: {
            type: 'json',
            root: 'listadoInformacion'
            // records will have a 'plant' tag
        },
        simpleSortMode: true               
    },
    listeners: {
            beforeload: function(store){
			}
    }
});

// create the grid and specify what field you want
// to use for the editor at each header.
grid = Ext.create('Ext.grid.Panel', {
        store:store,
        columns: [new Ext.grid.RowNumberer(), 
        {
            text: 'idMotivo',
            dataIndex: 'idmotivo',
            hidden:false
        },{
            text: 'Motivo',
            dataIndex: 'motivo',
            hidden:false
        },{
            text: 'Observacion',
            dataIndex: 'observacion',
            hidden:false
        },{
            text: 'Valor',
            width: 130,
            dataIndex: 'valor'
        },{
            header: 'Acciones',
            xtype: 'actioncolumn',
            width:130,
            sortable: false,
            items: [{
                iconCls: 'button-grid-delete',
                tooltip: 'Eliminar',
                handler: function(grid, rowIndex, colIndex) {
                    store.removeAt(rowIndex); 
                }
            }]
        }],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: 'lista_informacion_nd',
        width: 600,
        height: 200,
        title: 'Detalle de nota de debito',
        frame: true,
        plugins: [cellEditing]
    });
});
    
function enviarInformacion()
{
	var array_data_caract ={};
	var j=0;
	var informacion=[];
	for(var i=0; i < grid.getStore().getCount(); i++)
	{
		//console.log(grid_dos.getStore().getAt(i).data);
		variable=grid.getStore().getAt(i).data;
		for(var key in variable) {
			var valor = variable[key];
			if(j==0)
				array_data_caract['id']=valor;
			if(j==1)
				array_data_caract['motivo']=valor;
			if(j==2)
				array_data_caract['observacion']=valor;
			if(j==3)
				array_data_caract['valor']=valor;
			if(j==4)
				array_data_caract['idpagodet']=valor;				
			j++;
		}
		informacion.push(array_data_caract);
		array_data_caract ={};
		j=0;
	}
	//console.log(informacion);
	document.getElementById("listado_informacion").value=JSON.stringify(informacion); 
    document.forms[0].submit();
    
}

function generarDetalle()
{
	 //Obtener informacion del formulario
    var observacion=formulario.observacion.value;
    var valor=formulario.valor_p.value;
    var motivos=formulario.motivos.value;
	var pagos=formulario.pagos.value;
    pagos=pagos.split("-");
	motivos=motivos.split("-");
    var rec = new ListadoDetalleOrden({'idmotivo':motivos[0],'motivo':motivos[1],'observacion':observacion,'valor':valor,'idpagodet':pagos[0]});
    store.add(rec);
    limpiar_detalle();
}

function limpiar_detalle()
{
    if(formulario.observacion)
        formulario.observacion.value="";
        
	if(formulario.valor_p)
        formulario.valor_p.value="";
	
	if(formulario.motivos)
		formulario.motivos.options[0].selected=true;

	if(formulario.pagos)
		formulario.pagos.options[0].selected=true;		
}

function llenarTotal(informacion)
{
	var info_combo=formulario.pagos.value;
	var info=info_combo.split("-");
	formulario.valor_p.value=info[1];	
}
