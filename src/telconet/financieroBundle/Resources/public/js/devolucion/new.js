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
            // records will have a 'plant' ta
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
            hidden:true
        },{
            text: 'Motivo',
            dataIndex: 'motivo',
            width: 200,
            hidden:false
        },{
            text: 'Observacion',
            dataIndex: 'observacion',
            width: 300,
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
        renderTo: 'lista_informacion',
        width: 800,
        height: 200,
        title: 'Detalle devolucion',
        frame: true,
        plugins: [cellEditing]
    });
});

function obtenerSesionActual()
{
    Ext.MessageBox.show({
        title: 'Atención',
        progressText: 'Procesando Información...',
        width:200,
        progress:true,
        closable:false,
        animEl: 'body'
    });
    
    Ext.Ajax.request({
        url: url_obtener_sesion_actual,
        async:false,
        success: function(response, opts) {
            var resultado = Ext.JSON.decode(response.responseText);
            document.getElementById("session_punto_id").value=resultado['puntoId']; 
            document.getElementById("session_punto_login").value=resultado['puntoLogin']; 
            Ext.MessageBox.hide();
        },
        failure: function(response, opts) {
            document.getElementById("session_punto_id").value=''; 
            Ext.MessageBox.hide();
        }
    });
}

function enviarInformacion()
{
    document.getElementById("session_punto_id").value='';
    document.getElementById("session_punto_login").value='';
	var array_data_caract ={};
	var j=0;
	var informacion=[];
    var contItems = grid.getStore().getCount();
    
    obtenerSesionActual();
    var idPuntoSessionActual  = document.getElementById("session_punto_id").value; 
    var idPuntoSessionCargada = document.getElementById("punto_id").value; 
    var loginSessionCargada   = document.getElementById("punto_login").value;
    var loginSessionActual    = document.getElementById("session_punto_login").value;

    if(idPuntoSessionActual !== idPuntoSessionCargada){
       
        Ext.Msg.alert('Atención', 'El login al cual se le está aplicando la devolución ( '
                      +loginSessionCargada+' ) no corresponde al login en sesión ('+loginSessionActual+'). Verificar para proceder.');
        
    }else{
        if(contItems > 0){  
            for(var i=0; i < grid.getStore().getCount(); i++){
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
        
            loadMask('myLoading', true, 'Procesando');
        
            console.log(informacion);
            document.getElementById("listado_devolucion").value=JSON.stringify(informacion); 
            document.formulario.submit();
        }//(contItems > 0)
        else
        {
            //Validar de manera adicional que la interfaz 
            //no permita guardar valores mientras que no se tenga un detalle agregado.
           Ext.Msg.alert('Atención', 'No se pueden crear devoluciones mientras no se tenga un detalle agregado.');
        }    
    }
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
	//alert(pagos);
	/*
	 * Validaciones:
	 * 
	 * - Validando que ningun dato sea nulo al agregar el detalle
	 * - Que no me deje agregar el mismo pago
	 * - Que no me permita cambiar el valor que se va a devolver
	 * - Agregar la presentacion del pago en el grid
	 */
	
	
	if(motivos[0]!='' && motivos[1]!='' && observacion!='' && valor!='' && pagos[0]!='')
	{
		if(verificarExistencia(pagos[0])==0)
		{
			ocultarDiv('div_error');
			var rec = new ListadoDetalleOrden({'idmotivo':motivos[0],'motivo':motivos[1],'observacion':observacion,'valor':valor,'idpagodet':pagos[0]});
		}
		else
		{
			mostrarDiv('div_error');
			$('#div_error').html('El pago seleccionado ya esta agregado');
			limpiar_detalle();
		}
	}
	else
	{
		mostrarDiv('div_error');
		$('#div_error').html('Favor ingresar todos los datos');
		limpiar_detalle();
	}	

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

function verificarExistencia(verificarPagoDet)
{
	var array_data_caract ={};
	var j=0;
	var informacion=[];
	var iguales=0;
	
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
	
	//alert(informacion.length);
	
	if(informacion.length>0)
	{
		for(var i=0;i<informacion.length;i++){
			if (informacion[i].idpagodet==verificarPagoDet)
			{
				iguales=1;
				break;
			}
		}
	}
	else
		iguales=0;
		
	//alert(iguales);
		
	return iguales;
}

function mostrarDiv(div)
{
	capa = document.getElementById(div);
	capa.style.display = 'block';    
}

function ocultarDiv(div){
	capa = document.getElementById(div);
	capa.style.display = 'none';    
}  
