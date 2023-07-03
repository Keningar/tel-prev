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
             {name:'valor', type: 'float'},
			 {name:'idpagodet', type: 'int'},
             {name:'multa', type: 'float'}
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
            hidden:true
        },{
            text: 'Motivo',
            dataIndex: 'motivo',
            width: 200,
            hidden:false
        },{
            text: 'Observacion',
            dataIndex: 'observacion',
            width: 200,
            hidden:false
        },{
            text: 'Valor',
            width: 130,
            dataIndex: 'valor'
        },{
            text: 'Multa',
            width: 130,
            dataIndex: 'multa'
        },{
            header: 'Acciones',
            xtype: 'actioncolumn',
            width:100,
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
        width: 800,
        height: 200,
        title: 'Detalle de nota de debito',
        frame: true,
        plugins: [cellEditing]
    });
});
    
function enviarInformacion()
{
	//alert("aki js");
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
            if(j==5)
				array_data_caract['multa']=valor;
			j++;
		}
		informacion.push(array_data_caract);
		array_data_caract ={};
		j=0;
	}
    
    if (informacion.length > 0)
    {
        loadMask('myLoading', true, 'Procesando');
        document.getElementById("listado_informacion").value=JSON.stringify(informacion); 
        document.formulario.submit();
    }
    else
    {
        alert("Ingrese detalles a la nota de debito interna");
    }
    
}

function generarDetalle()
{   
    console.log(store.data.getCount());
    if(store.data.getCount()<=0)
    {
        //Obtener informacion del formulario
        var observacion=formulario.observacion.value;
        var valor=formulario.valor_p.value;
        var motivos=formulario.motivos.value;
        var pagos=formulario.pagos.value;
    
        //En ciertos casos
        var multa=formulario.valor_comision.value;
        pagos=pagos.split("-");
        motivos=motivos.split("-");
        var rec = new ListadoDetalleOrden({'idmotivo':motivos[0],
                                            'motivo':motivos[1],
                                            'observacion':observacion,
                                            'valor':valor,
                                            'idpagodet':pagos[0],
                                            'multa':multa,
                                        });
        //Se agrega validación para ANTS                                
                                        
        if (((pagos[4] == 'ANT' || pagos[4] == 'ANTC' || pagos[4] == 'ANTS') && pagos[6] == 'Pendiente' && validaValor()) || pagos[6] == 'Cerrado')
        {
            if (motivos[0] > 0 && valor > 0)
            {
                if (motivos[1] == 'Registro de Cheque Devuelto' && strPrefijoEmpresa != 'MD')
                {
                    if (multa > 0)
                    {
                        store.add(rec);
                        $('#multa').addClass('campo-oculto');
                        limpiar_detalle();
                    }
                    else
                    {
                        document.getElementById("mensaje-multa").innerHTML = "Debe ingresar valor de multa";
                        $('#mensaje-multa').removeClass('campo-oculto');
                        $('#mensaje-multa').addClass('errormessage');
                    }
                }
                else
                {
                    store.add(rec);
                    limpiar_detalle();
                }
            }
            else
            {               
                alert("Error, Valor ingresado para la NDI Incorrecto");             
            }
        }
        else
        {
            alert("Los detalles deben contener informacion");
        }
    }
    else
    {
        alert("Solo es permitido ingresar un detalle para la Nota de debito");
    }    
}

function limpiar_detalle()
{
    if(formulario.observacion)
        formulario.observacion.value="";
        
	if(formulario.valor_p)
        formulario.valor_p.value="";
    
	if(formulario.valor_comision)
        formulario.valor_comision.value="";
	
	if(formulario.motivos)
		formulario.motivos.options[0].selected=true;

	if(formulario.pagos)
		formulario.pagos.options[0].selected=true;		
}

function llenarTotal(informacion)
{
    /*
     * Antes de llenar el total de la nota de debito interna debemos verificar:
     * Si es de tipo depositable
     * Si esta depositado 
     * Segun eso permitimos el ingreso de la informacion
     * 
     * {{ pago.id }}-{{ pago.valorPago }}-{{ pago.depositado }}-{{ pago.esDepositable }}-{{ pago.codigoTipoDocumento }}
     * -{{ pago.nombreTipoDocumento }}-{{ pago.estado }}
     * 
     */
    var informacionCombo=formulario.pagos.value;
	var informacion=informacionCombo.split("-");
	
    //EsDepositable
    if(informacion[3]=='S')
    {
        //Depositado
        if(informacion[2]=='S')
        {
            formulario.valor_p.value=informacion[1];
        }
        else
        {
            //Presentacion de mensaje
            document.getElementById("mensaje").innerHTML = "El pago no se encuenta depositado, favor proceder a realizar el deposito";
            $('#mensaje').removeClass('campo-oculto');
            $('#mensaje').addClass('errormessage');
            
            //Bloqueo la creacion de la nota de debito interna
            $('#contenedorDocumento').addClass('campo-oculto');
        }
    }
    else
    {
        $('#mensaje').addClass('campo-oculto');
        $('#contenedorDocumento').removeClass('campo-oculto');
        
        formulario.valor_p.value=informacion[1];	
    }
    //Para el caso de Anticipos(ANT) , Anticipos por Cruce (ANTC) o Anticipos Sin Cliente (ANTS) que esten en estado Pendiente se permitira 
    //editar el campo Valor y generar la NDI por un valor menor o igual al Anticipo.   
    if ((informacion[4] == 'ANT'|| informacion[4] == 'ANTC'|| informacion[4] == 'ANTS')&& informacion[6] == 'Pendiente'&& strPermiteEdicionNdi == 'S')
    {
        var campo = document.getElementById('valor_p');
        campo.readOnly = false; // Se quita el atributo            
    }
    else
    {   //Sino verificara que el estado del Pago o Anticipo sea Cerrado
        if (informacion[6] == 'Cerrado')
        {
            var campo = document.getElementById('valor_p');
            campo.readOnly = true; // Se añade el atributo                        
        }
    }
}

function validaValor()
{
    var pagos = formulario.pagos.value;
    pagos     = pagos.split("-");
    // Para el caso de Anticipos(ANT) o Anticipos por Cruce (ANTC) que esten en estado Pendiente se permite editar el campo Valor
    // y generar la NDI por un valor menor al Anticipo
    // Se agrega validación para ANTS
    if (validador())
    {
        if ((pagos[4] == 'ANT' || pagos[4] == 'ANTC' || pagos[4] == 'ANTS') && pagos[6] == 'Pendiente')
        {   
            if(Number(formulario.valor_p.value) > Number(pagos[1]))
            {
                mostrarDiv('div_valor');
                $('#div_valor').html('Error, Valor ingresado para la NDI no puede ser mayor al valor del '+pagos[5]+'.');        
                formulario.valor_p.value = '';
                return false;
            }
            else
            {
                 if(formulario.valor_p.value == 0)
                 {
                     $('#div_valor').html('Error, Valor ingresado para la NDI no puede ser cero.');        
                     formulario.valor_p.value = '';
                     return false;
                 }
                 else
                 {
                     ocultarDiv('div_valor');
                     return true;
                 }
            }
            
        }
    }
    else 
    {
        mostrarDiv('div_valor');
        $('#div_valor').html('Error, Valor ingresado para la NDI Incorrecto');        
        formulario.valor_p.value = '';
        return false;
    }
}

function validador()
{
    return /^\d+(\.\d+)?$/.test(formulario.valor_p.value);
}   
function mostrarDiv(div)
{
    capa = document.getElementById(div);
    capa.style.display = 'block';
}
function ocultarDiv(div)
{
    capa = document.getElementById(div);
    capa.style.display = 'none';
}
        
function loadMask(el, flag, msg)
{
    Mask = new Ext.LoadMask(Ext.get(el), {msg: msg});

    if (flag)
    {
        Mask.show();
    }
    else
    {
        Mask.hide();
    }
}

function validarMotivos()
{
    /*
     * Segun el motivo cargamos el campo requerido
     * 
     * {{ motivo.id }}-{{ motivo.nombreMotivo }}
     * 
     */
    var informacionComboMotivos=formulario.motivos.value;
	var informacionMotivos=informacionComboMotivos.split("-");
    
    if(informacionMotivos[1]=='Registro de Cheque Devuelto')
    {
        $('#multa').removeClass('campo-oculto');
    }
    else
    {
        $('#multa').addClass('campo-oculto');
    }
    
}
