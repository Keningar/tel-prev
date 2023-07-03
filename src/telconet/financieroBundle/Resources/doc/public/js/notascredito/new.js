/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Ext.onReady(function(){
	
var desde_date = new Ext.form.DateField({
                    name: 'feDesdeFacturaPost',
                    allowBlank: false,
                    format: 'Y-m-d',
                    renderTo: 'feDesdeFactura',
                });
                
var hasta_date = new Ext.form.DateField({
                    name: 'feHastaFacturaPost',
                    allowBlank: false,
                    format: 'Y-m-d',
                    renderTo: 'feHastaFactura',
                });

var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
    clicksToEdit: 1
});

Ext.define('ListadoDetalleOrden', {
    extend: 'Ext.data.Model',
    fields: [
			 {name:'codigo', type: 'string'},
			 {name:'informacion', type: 'string'},
             {name:'valor', type: 'string'},
             {name:'cantidad', type: 'string'},
             {name:'descuento', type: 'string'},
             {name:'tipo', type: 'string'},
             {name:'puntoId', type: 'string'},
            ]
}); 

 store = Ext.create('Ext.data.Store', {
    // destroy the store if the grid is destroyed
    autoDestroy: true,
    model: 'ListadoDetalleOrden',
    proxy: {
        type: 'ajax',
        // load remote data using HTTP
        url: url_info_por_dias,
        reader: {
            type: 'json',
            root: 'listadoInformacion'
            // records will have a 'plant' tag
        },
        extraParams:{fechaDesde:'',fechaHasta:'',idFactura:'',porcentaje:'',tipo:''},
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
            text: 'Codigo',
            dataIndex: 'codigo',
            hidden:true
		},{
            text: 'PuntoId',
            dataIndex: 'puntoId',
            hidden:true
        },{
            text: 'Producto/Plan',
            dataIndex: 'informacion',
            hidden:false
        },{
            text: 'Valor',
            width: 130,
            dataIndex: 'valor'
        },{
            text: 'Cantidad',
            dataIndex: 'cantidad',
            hidden:false
        },{
            text: 'Descuento',
            dataIndex: 'descuento',
            hidden:false
        },{
            text: 'Tipo',
            dataIndex: 'tipo',
            hidden:true
        }],
        selModel: {
            selType: 'cellmodel'
        },
        renderTo: 'lista_informacion_pre_cargada',
        width: 600,
        height: 200,
        title: 'Detalle de factura',
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
				array_data_caract['codigo']=valor;
			if(j==1)
				array_data_caract['informacion']=valor;
			if(j==2)
				array_data_caract['precio']=valor;
			if(j==3)
				array_data_caract['cantidad']=valor;
			if(j==4)
				array_data_caract['descuento']=valor;
			if(j==5)
				array_data_caract['tipo']=valor;
			if(j==6)
				array_data_caract['puntoId']=valor;
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

function verificarCheck(info)
{
    if(info=='PorDias')
    {
		$('#formulario_por_dias').removeClass('campo-oculto');
		$('#formulario_por_porcentaje').addClass('campo-oculto');
		$('#formulario_valor_original').addClass('campo-oculto');
    }
    
    if(info=='PorServicio')
    {
        $('#formulario_por_dias').addClass('campo-oculto');
		$('#formulario_por_porcentaje').removeClass('campo-oculto');
		$('#formulario_valor_original').addClass('campo-oculto');
    }
    
    if(info=='ValorOriginal')
    {
        $('#formulario_por_dias').addClass('campo-oculto');
		$('#formulario_por_porcentaje').addClass('campo-oculto');
    }
    
    $('#info_check').val(info);
}

function generarDetalle()
{
	var clickeado=$('#info_check').val();
	
	if(clickeado=='PorDias')
    {
		//llamo al ajax por dias
		//envio los valores de las fechas
		var fechaDesde=document.getElementById('formulario').feDesdeFacturaPost.value;
		var fechaHasta=document.getElementById('formulario').feHastaFacturaPost.value;
        store.getProxy().extraParams.fechaDesde= fechaDesde;
        store.getProxy().extraParams.fechaHasta= fechaHasta;
        store.getProxy().extraParams.idFactura= idFactura;
        store.getProxy().extraParams.tipo= "PD";
        store.load();
    }
    
	if(clickeado=='PorServicio')
    {
		//llamo al ajax por servicio
		//envio el valor del porcentaje
		var porcentajeFactura=document.getElementById('porcentajeFactura').value;
        store.getProxy().extraParams.porcentaje= porcentajeFactura;
        store.getProxy().extraParams.idFactura= idFactura;
        store.getProxy().extraParams.tipo= "PS";
        store.load();
    }
	
	if(clickeado=='ValorOriginal')
    {
		//llamo al ajax por valor original
		//se muestra el detalle de la factura exactamente igual
        store.getProxy().extraParams.idFactura= idFactura;
        store.getProxy().extraParams.tipo= "VO";
        store.load();
        
    }
}
