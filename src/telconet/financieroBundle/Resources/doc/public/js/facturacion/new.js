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
			 {name:'codigo', type: 'string'},
			 {name:'informacion', type: 'string'},
             {name:'precio', type: 'string'},
             {name:'cantidad', type: 'string'},
             {name:'descuento', type: 'string'},
             {name:'tipo', type: 'string'},
             {name:'tipoOrden', type: 'string'},
             {name:'fechaActivacion', type: 'string'},
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
        url: url_listar_informacion_existente,
        reader: {
            type: 'json',
            root: 'listadoInformacion'
            // records will have a 'plant' tag
        },
        extraParams:{puntoid:'',informacionGrid:''},
        simpleSortMode: true               
    },
    listeners: {
            beforeload: function(store){
                    store.getProxy().extraParams.puntoid= punto_id; 
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
								array_data_caract['tipoOrden']=valor;
							if(j==7)
								array_data_caract['fechaActivacion']=valor;
							if(j==8)
								array_data_caract['puntoId']=valor;
							j++;
						}
						informacion.push(array_data_caract);
						array_data_caract ={};
						j=0;
					}
					//console.log(informacion);
					store.getProxy().extraParams.informacionGrid= JSON.stringify(informacion); 
			}
    }
});

// create the grid and specify what field you want
// to use for the editor at each header.
grid = Ext.create('Ext.grid.Panel', {
        store:store,
        columns: [new Ext.grid.RowNumberer(), 
        {
            text: 'Tipo',
            dataIndex: 'tipo',
            hidden:true
        },{
            text: 'TipoOrden',
            dataIndex: 'tipoOrden',
            hidden:true
        },{
            text: 'PuntoId',
            dataIndex: 'puntoId',
            hidden:true
        },{
            text: 'Codigo',
            dataIndex: 'codigo',
            hidden:true
        },{
            text: 'Producto/Plan',
            width: 130,
            dataIndex: 'informacion'
		},{
            text: 'Fe. Activacion',
            width: 130,
            dataIndex: 'fechaActivacion'
        },{
            text: 'Precio',
            width: 130,
            dataIndex: 'precio'
        },{
            text: 'Cantidad',
            dataIndex: 'cantidad',
            align: 'right',
            width: 70			
        },{
            text: 'Descuento',
            dataIndex: 'descuento',
            align: 'right',
            width: 70			
        }, {
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
        renderTo: 'lista_informacion_pre_cargada',
        width: 800,
        height: 200,
        title: 'Listado de servicios',
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
				array_data_caract['tipoOrden']=valor;
			if(j==7)
				array_data_caract['fechaActivacion']=valor;
			if(j==8)
				array_data_caract['puntoId']=valor;
			j++;
		}
		informacion.push(array_data_caract);
		array_data_caract ={};
		j=0;
	}
	//console.log(informacion);
	if(informacion.length>0)
	{
		document.getElementById("listado_informacion").value=JSON.stringify(informacion); 
		document.forms[0].submit();
	}
	else
	{
		alert("Ingrese detalles a la factura");
	}
}

function verificarCheck(info)
{
    if(info=='Orden')
    {
		//debo verificar si existe informacion en el grid
        store.load();
        //$("#lista_informacion_pre_cargada").removeClass("campo-oculto");
    }
    
    if(info=='Manual')
    {
        $.ajax({
            type: "POST",
            data: "tipo=portafolio",
            url:url_info_portafolio,
            success: function(msg){					
                if (msg.msg == 'ok')
                {
                    if(msg.info=='portafolio')
                    {
                        $('#formulario_portafolio').removeClass('campo-oculto');
                        document.getElementById("planes").innerHTML=msg.div;
                    }
                }
            }
        });
        
        $('#planes').change(function()
        {
            var info_plan=document.getElementById('planes').value;
            var plan=info_plan.split("-");
            //var producto=$('#producto').val();
            
            $.ajax({
                type: "POST",
                data: "plan=" + plan[0],
                url:url_info_plan,
                success: function(msg){
                    if (msg.msg == 'ok')
                    {					
                        //Llenar el div que presentara las clausulas
                        //Validar que de respuesta sinop solicitarla
                        document.getElementById("precio").value=msg.precio;
                        document.getElementById("descuento_plan").value=msg.descuento;
                        document.getElementById("tipoOrden").value=msg.tipoOrden;
                        //console.log(msg.id);
                    }
                    else
                        document.getElementById("contenido_plan").innerHTML=msg.msg;
                }
            });
        });
    }
}

function agregar_detalle_portafolio()
{
    //Obtener informacion del formulario
    var info_producto=formulario.planes.value;
    var producto=info_producto.split("-");
    var cantidad=formulario.cantidad_plan.value;
    var tipoOrden=formulario.tipoOrden.value;
    var precio_unitario=0;
    var precio_total=0;
    var descuento=0;
    precio_unitario=formulario.precio.value;
    descuento=formulario.descuento_plan.value;
    precio_total=(precio_unitario*cantidad);
    var rec = new ListadoDetalleOrden({'codigo':producto[0],'informacion':producto[1],'precio':precio_total,'cantidad':cantidad,'descuento':descuento,'tipo':"PL",'tipoOrden':tipoOrden,'fechaActivacion':'','puntoId':''});
    store.add(rec);
    limpiar_detalle_portafolio();
}

function limpiar_detalle_portafolio()
{
    if(formulario.cantidad_plan)
        formulario.cantidad_plan.value="";
        
	if(formulario.descuento_plan)
        formulario.descuento_plan.value="";

    if(formulario.precio)
        formulario.precio.value="";
        
    if(formulario.tipoOrden)
        formulario.tipoOrden.value="";
	
	if(formulario.planes)
		formulario.planes.options[0].selected=true;
}
