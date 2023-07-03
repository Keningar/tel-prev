
var comboTareasMantenimientoStore=null;
Ext.onReady(function(){

    comboTareasMantenimientoStore = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: 'getTareasMantenimientos',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            extraParams: {
                nombre: '',
                estado: 'Activo'
            }
        },
        fields:
            [
                {name: 'idTareaCombo', mapping: 'id_tarea'},
                {name: 'nombreTareaCombo', mapping: 'nombre_tarea'}
            ],
        listeners: {

            load: function(store) {
                if(store.getCount() == 0)
                {
                    Ext.Msg.alert('Alerta ', 'No hay tareas de mantenimiento asociadas');
                };
            }
        }
    });
    
    agregarGridTareasMantenimiento(0);
    $('#indicesMantenimientos').val("0,");
    refrescarTitulosMantenimientos();
    
    
    $('#agregar_mantenimiento').click(function() { 
            var strIndicesMantenimientos=$('#indicesMantenimientos').val();
            var tiposFrecuenciaList    = $('#tiposFrecuencia-fields-list');
            var frecuenciasList         = $('#frecuencias-fields-list');
            
            var newWidgetTipoFrecuencia = tiposFrecuenciaList.attr('data-prototype');
            var newWidgetFrecuencia     = frecuenciasList.attr('data-prototype');
            
            var name='__name__';
            newWidgetTipoFrecuencia = newWidgetTipoFrecuencia.replace(name, mantenimientosCount);            
            newWidgetFrecuencia = newWidgetFrecuencia.replace(name, mantenimientosCount);
            
            newWidgetTipoFrecuencia = newWidgetTipoFrecuencia.replace(name, mantenimientosCount);            
            newWidgetFrecuencia = newWidgetFrecuencia.replace(name, mantenimientosCount);
            
            var newDivMantenimientoOut= $('<div/>', { id: 'div_mantenimiento_'+mantenimientosCount, class: 'div_mantenimiento'});
            newDivMantenimientoOut.appendTo($('#mantenimientos_principal'));
            
            var newSpanEliminarMantenimiento= $('<span/>', { id: 'close_'+mantenimientosCount, class: 'btnCerrar', text:'x', onclick:'eliminarMantenimiento(this,'+mantenimientosCount+'); return false;'});
            newSpanEliminarMantenimiento.appendTo($('#div_mantenimiento_'+mantenimientosCount));
            
            var newDivMantenimiento= $('<div/>', { id: 'mantenimiento_'+mantenimientosCount, class: 'mantenimiento'});
            newDivMantenimiento.appendTo($('#div_mantenimiento_'+mantenimientosCount));
            
            var newPTituloMantenimiento= $('<p/>', { id:'titulo_mantenimiento_'+mantenimientosCount, class: 'titulosMantenimientos' });
            newPTituloMantenimiento.appendTo($('#mantenimiento_'+mantenimientosCount));
            
            var newDivBlock= $('<div/>', { id: 'div_block_'+mantenimientosCount, class: 'divBlock'});
            newDivBlock.appendTo($('#mantenimiento_'+mantenimientosCount));

            var newDivFrecuencia= $('<div></div>', { id: 'div_frecuencia_'+mantenimientosCount, class: 'divInlineBlock1'}).html(newWidgetFrecuencia);
            newDivFrecuencia.appendTo($('#div_block_'+mantenimientosCount));
            
            var newDivTipoFrecuencia= $('<div></div>', { id: 'div_tipoFrecuencia_'+mantenimientosCount, class: 'divInlineBlock2'}).html(newWidgetTipoFrecuencia);
            newDivTipoFrecuencia.appendTo($('#div_block_'+mantenimientosCount));
            
            var newDivGridTareas= $('<div></div>', { id: 'div_grid_tareas_mantenimiento_'+mantenimientosCount, class: 'grid_tareas_mantenimiento'});
            newDivGridTareas.appendTo($('#mantenimiento_'+mantenimientosCount));
            
            var newDivTareas= $('<div></div>', { id: 'div_tareas_'+mantenimientosCount, class: 'div_tareas'});
            newDivTareas.appendTo($('#div_grid_tareas_mantenimiento_'+mantenimientosCount));
            
            var newInputTareas= $('<input/>', { id: 'tareas_escogidas_'+mantenimientosCount, name:'tareas_escogidas_'+mantenimientosCount,  class: 'tareas_escogidas',type:'hidden',value:''});
            newInputTareas.appendTo($('#div_grid_tareas_mantenimiento_'+mantenimientosCount));
            

            agregarGridTareasMantenimiento(mantenimientosCount);
            strIndicesMantenimientos=strIndicesMantenimientos+mantenimientosCount+",";
            
            $('#indicesMantenimientos').val(strIndicesMantenimientos);
            
            refrescarTitulosMantenimientos();
            mantenimientosCount++;
            

            return false;
        });
    
    
});




function validarFormulario()
{   
    //Ext.MessageBox.wait("Grabando Datos...", 'Por favor espere');  
    if(document.getElementById("telconet_schemabundle_planmantenimientotype_nombreProceso").value==""){
        Ext.Msg.alert("Alerta","El campo nombre del plan es requerido.");
        return false;
    }
    if(document.getElementById("telconet_schemabundle_planmantenimientotype_descripcionProceso").value==""){
        Ext.Msg.alert("Alerta","El campo descripción del plan es requerido.");
        return false;
    }
    
    var numMantenimientos=$('#mantenimientos_principal .div_mantenimiento').length;
    if(numMantenimientos>0)
    {
        var valorBoolMantenimientos = validarMantenimientos();
        
        if(valorBoolMantenimientos)
        {
            var valorBool = validarTareas();
            
            if(valorBool)
            {
                var strIndicesMantenimientos    = $('#indicesMantenimientos').val();
                var arrayIndicesMantenimientos  = strIndicesMantenimientos.split(",");
                for(var contadorMantenimiento = 0; contadorMantenimiento < numMantenimientos; contadorMantenimiento++)
                {
                    var indice_mantenimiento_plan=arrayIndicesMantenimientos[contadorMantenimiento];
                    
                    json_tareas_mantenimientos = obtenerTareas(indice_mantenimiento_plan);
                    mantenimientos_tareas = Ext.JSON.decode(json_tareas_mantenimientos);

                    if(mantenimientos_tareas.total == 0)
                    {
                        Ext.Msg.alert("Alerta", "Debe ingresar al menos una tarea para un mantenimiento.");
                        return false;
                    }
                    else 
                    {
                        $('#tareas_escogidas_'+indice_mantenimiento_plan).val(json_tareas_mantenimientos);
                    }
                }
                Ext.Ajax.request
                ({
                    url: strUrlVerificarNombrePlan,
                    method: 'post',
                    params: 
                    { 
                        nombrePlan      : $("#telconet_schemabundle_planmantenimientotype_nombreProceso").val(),
                        idPlan          : ''
                    },
                    success: function(response)
                    {
                        var text = response.responseText;
                        if(text === "OK")
                        {
                            $("#numMantenimientosFinal").val(numMantenimientos);
                            document.getElementById("form_new_proceso").submit();
                            
                        }
                        else
                        {
                            Ext.MessageBox.hide();
                            Ext.Msg.alert('Error','El plan de mantenimiento que desea ingresar ya existe.');
                        }
                    },
                    failure: function(result)
                    {
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Error',result.responseText);
                    }
                });
                return false;
            }	
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    else
    {
        Ext.Msg.alert("Alerta","Debe ingresar al menos un mantenimiento para crear un plan de mantenimiento");
        return false;
    }
}

function eliminarSeleccion(datosSelect)
{	

    for(var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
    {		
        datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
    }

}

function obtenerTareas(contMantenimiento)
{
    var gridTareas = Ext.getCmp("gridTareas_"+contMantenimiento);
    var array = new Object();
    array['total'] =  gridTareas.getStore().getCount();
    array['tareas'] = new Array();
    var array_data = new Array();
    for(var i=0; i < gridTareas.getStore().getCount(); i++)
    {
        array_data.push(gridTareas.getStore().getAt(i).data);
    }
    array['tareas'] = array_data;
    return Ext.JSON.encode(array);
}

function validarMantenimientos()
{
    var numTotalMantenimientos      = $('#mantenimientos_principal .div_mantenimiento').length;
    var strIndicesMantenimientos    = $('#indicesMantenimientos').val();
    var arrayIndicesMantenimientos  = strIndicesMantenimientos.split(",");
    
    var bool_tiene_registros_repetidos  = false;
    var strMensaje='';
    
    
    if(numTotalMantenimientos>1)
    {
        for(var contadorMantenimiento = 1; contadorMantenimiento < numTotalMantenimientos; contadorMantenimiento++)
        {
            var indice_mantenimiento_plan=arrayIndicesMantenimientos[contadorMantenimiento];
            var frecuencia      = $('#mantenimientotype_frecuencias_'+indice_mantenimiento_plan).val();
            var tipoFrecuencia  = $('#mantenimientotype_tiposFrecuencia_'+indice_mantenimiento_plan).val();
            
            for(var j = 0; j < contadorMantenimiento; j++)
            {
                var indice_mantenimiento_plan_valida=arrayIndicesMantenimientos[j];
                var frecuencia_valida       = $('#mantenimientotype_frecuencias_'+indice_mantenimiento_plan_valida).val();
                var tipoFrecuencia_valida   = $('#mantenimientotype_tiposFrecuencia_'+indice_mantenimiento_plan_valida).val();

                if(frecuencia_valida == frecuencia && tipoFrecuencia_valida == tipoFrecuencia)
                {
                    bool_tiene_registros_repetidos = true;
                    var iMantenimiento1 = j+1;
                    var iMantenimiento2 = contadorMantenimiento+1;
                    strMensaje='El Mantenimiento '+iMantenimiento1+' y el Mantenimiento '+iMantenimiento2;
                    strMensaje+=' tienen las mismas frecuencias y tipos de frecuencia. Por favor corrija.';
                    break;
                }
            }
            
            if(bool_tiene_registros_repetidos)
            {
                break;
            }

        }
        if(bool_tiene_registros_repetidos)
        {
            Ext.Msg.alert('Alerta ',strMensaje);
            return false;
        }
        else
        {
            return true;
        }
    }
    else
    {
        return true;
    }    
}

function validarTareas()
{
    var numTotalMantenimientos      = $('#mantenimientos_principal .div_mantenimiento').length;
    var strIndicesMantenimientos    = $('#indicesMantenimientos').val();
    var arrayIndicesMantenimientos  = strIndicesMantenimientos.split(",");
    
    var contGridTareasOK=0;
    var strMensaje='';
    
    //Recorre los grids de tareas
    for(var contadorMantenimiento = 0; contadorMantenimiento < numTotalMantenimientos; contadorMantenimiento++)
    {
        
        var numMantenimiento=contadorMantenimiento+1;
        var indice_mantenimiento_plan=arrayIndicesMantenimientos[contadorMantenimiento];
        var storeValida = Ext.getCmp("gridTareas_"+indice_mantenimiento_plan).getStore();
        
        var bool_esta_vacio = false;
        
        if(storeValida.getCount() > 0)
        {
            var bool_tiene_registros_vacios     = false;
            var bool_tiene_registros_repetidos  = false;
            
            //Recorre las tareas dentro del grid
            for(var i = 0; i < storeValida.getCount(); i++)
            {
                var id_tarea = storeValida.getAt(i).data.idTarea;
                var nombre_tarea = storeValida.getAt(i).data.nombreTarea;     
                
                if(id_tarea != "" && nombre_tarea != ""){}
                else 
                {  
                    bool_tiene_registros_vacios = true;
                    break;
                }
                
                
                if(i>0)
                {
                    for(var j = 0; j < i; j++)
                    {
                        var id_tarea_valida = storeValida.getAt(j).data.idTarea;
                        var nombre_tarea_valida = storeValida.getAt(j).data.nombreTarea;

                        if(id_tarea_valida == id_tarea || nombre_tarea_valida == nombre_tarea)
                        {
                            bool_tiene_registros_repetidos = true;
                            break;
                        }
                    }
                }
            }
            
            if(!bool_tiene_registros_vacios && !bool_tiene_registros_repetidos)
            {
                contGridTareasOK++;
            }
            else if(bool_tiene_registros_vacios)
            {
                strMensaje+='Existen tareas vacías en el Mantenimiento '+numMantenimiento;
                Ext.Msg.alert('Alerta ',strMensaje);
                return false;
            }
            else if(bool_tiene_registros_repetidos)
            {
                strMensaje+='No puede ingresar tareas repetidas en el Mantenimiento '+numMantenimiento+' .Debe modificar el registro repetido ';
                Ext.Msg.alert('Alerta ',strMensaje);
                return false;
            }
        }
        else
        {
            bool_esta_vacio = true;
        }
        
        if(bool_esta_vacio)
        {
            strMensaje+='Debe ingresar tareas en el Mantenimiento '+numMantenimiento;
            Ext.Msg.alert('Alerta ',strMensaje);
            return false;
        }
        
    }
    
    if(contGridTareasOK==numTotalMantenimientos)
    {
        return true;
    }
    return false;
}


function eliminarMantenimiento(elemento,numeroMantenimiento)
{
    var strIndicesMantenimientos=$('#indicesMantenimientos').val();
    strIndicesMantenimientos = strIndicesMantenimientos.replace(numeroMantenimiento+",", "");
    $('#indicesMantenimientos').val(strIndicesMantenimientos);
    elemento.parentNode.parentNode.removeChild(elemento.parentNode);
    
    refrescarTitulosMantenimientos();
}

function refrescarTitulosMantenimientos()
{
    var strIndicesMantenimientos=$('#indicesMantenimientos').val();
    var cantidadMantenimientosPlan=$('#mantenimientos_principal .div_mantenimiento').length;
    var arrayIndicesMantenimientos=strIndicesMantenimientos.split(",");
    
    for(var i = 0; i < cantidadMantenimientosPlan; i++)
    {
        var iMantenimiento=i+1;
        var indice_mantenimiento_plan=arrayIndicesMantenimientos[i];
        $("#titulo_mantenimiento_"+indice_mantenimiento_plan).text("MANTENIMIENTO "+iMantenimiento);


    }
}