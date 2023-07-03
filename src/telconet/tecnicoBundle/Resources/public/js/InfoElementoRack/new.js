/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var arraySeleccionados = [];
var filaSeleccionada   = null;
var tipoRack           = 'S';//Tipo Standard de Rack ( Nodo )
var marca              = '';//variable que almacenará la marca seleccionada

Ext.onReady(function() {

    var Url = location.href;
    UrlUrl = Url.replace(/.*\?(.*?)/, "$1");
    Variables = Url.split("/");
    var n = Variables.length;
    var idDslam = Variables[n - 2];
    
    //storeMarcas llenará el combo con las marcas de modelos tipo rack
     var storeMarcas = new Ext.data.Store({
        total: 'total',
        proxy: 
        {
            type: 'ajax',
            url: '../../../administracion/tecnico/admi_marca_elemento/getMarcasElementosTipo',
            extraParams: {
                tipoElemento: 'RACK'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            }
        },
        fields:
            [
                {name: 'nombreMarcaElemento', mapping: 'nombreMarcaElemento'},
                {name: 'idMarcaElemento', mapping: 'idMarcaElemento'}
            ]
    });
    

    var storeNodo = new Ext.data.Store({
        total: 'total',
        autoLoad: false,
        proxy: {
            timeout: 400000,
            type: 'ajax',
            url: url_getEncontradosNodo,
            extraParams: {
                nombreElemento: '',
                modeloElemento: '',
                marcaElemento: '',
                canton: '',
                jurisdiccion: '',
                estadoNodo: 'Activo'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            limitParam: undefined,
            startParam: undefined,
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'}
            ]
    });
    combo_nodos = new Ext.form.ComboBox({
        id: 'combo_nodos',
        name: 'combo_nodos',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        width: 250,
        emptyText: 'Seleccione Nodo',
        store: storeNodo,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'combo_nodos',
        listeners: {
            select: {fn: function(combo, value) {
                    jsonR = value[0].raw;
                    $("#hd-canton").val(jsonR.nombreCanton);
                    var nombreNodoSeleccionado = jsonR.nombreElemento;

                    $('#telconet_schemabundle_infoelementoracktype_nodoElementoId').val(combo.getValue());
                    if(nombreNodoSeleccionado.includes("Data Center") && $("#cmb-tipo-rack").val() === 'DC')
                    {
                        obtenerMatrizGrid(jsonR.nombreCanton);
                    }
                    }}
                   }
                        });
        
    //combo_marca se llenará con el storeMarcas 
    combo_marca = new Ext.form.ComboBox({
        id: 'combo_marca',
        name: 'combo_marca',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        width: 250,
        emptyText: 'Seleccione Marca',
        store: storeMarcas,
        displayField: 'nombreMarcaElemento',
        valueField: 'idMarcaElemento',
        renderTo: 'combo_marca',
        listeners: {
            select: {fn: function(combo, value) {             
                    marca = combo.getValue();
                    storeModelos.proxy.extraParams = {idMarca: marca, tipoElemento: 'RACK', limite: 100};
                    storeModelos.load({params: {}});
                }}
        }
    }); 
    
    //StoreModelos se llena dinámicamente en base a la marca seleccionada, solo funcionanrá así para marcas DC                       
    storeModelos = new Ext.data.Store({
        total: 'total',
        proxy: {
            type: 'ajax',
            url: '../../../administracion/tecnico/admi_modelo_elemento/getModelosElementosPorMarca',
            extraParams: {
                idMarca: marca,
                tipoElemento: 'RACK'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
                    }
        },
        fields:
            [
                {name: 'nombreModeloElemento', mapping: 'nombreModeloElemento'},
                {name: 'idModeloElemento', mapping: 'idModeloElemento'}
            ]
    });
    
    combo_modelos = new Ext.form.ComboBox({
        id: 'combo_modelos',
        name: 'combo_modelos',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'remote',
        width: 250,
        emptyText: 'Seleccione Modelo',
        store: storeModelos,
        displayField: 'nombreModeloElemento',
        valueField: 'idModeloElemento',
        renderTo: 'combo_modelos',
        forceSelection: true,
        listeners: {
            select: {fn: function(combo, value) {
            $('#telconet_schemabundle_infoelementoracktype_modeloElementoId').val(combo.getValue());
                }}
        }
    });       
    
    //Bloque para seleccion de ubicacion de rack para Data Center
    $(".content-rack-dc").hide();
    
    $("#cmb-tipo-rack").bind("change",function(){
       var value = $(this).val();
       tipoRack = value;
       $("#hd-tipo-rack").val(tipoRack);
       if(value === 'S')
       {
           $("#tr-rack-nodo").show();
           $(".content-rack-dc").hide();          
       }
       else
       {
           $("#tr-rack-nodo").hide();
       }
    });        
    
    //Inicializar panel
    $("#panel-agregar-rack").dialog({
        autoOpen: false,
        modal: true,
        height:"auto",
        width:400,
        show: {
            effect: "blind",
            duration: 500
        },
        hide: {
            effect: "blind",
            duration: 250
        },
        buttons: [
            {
                id: "button-agregar",
                text: "Guardar",
                disabled: true,
                click: function() {
                    validarFormularioRacks();
                }
            },
            {
                id: "button-cerrar",
                text: "Cerrar",
                click: function() {
                    $("#button-agregar").addClass("ui-state-disabled").attr("disabled", true);
                    $(this).dialog("close");
                }
            }]        
    });  
    
    //Esconde por default formulario de ingreso de nuevo rack para DC
    $("#table-add-rack").hide();
    
    //Inicializar checkboxpanel
    $( ".rdb-btn-opciones" ).checkboxradio({icon: false});

    $("#radio-add-rack").bind("click",function(){
       $("#button-agregar").removeClass("ui-state-disabled").attr("disabled", false);
       $("#table-add-rack").show();
    });
    
    //Opcion para eliminar filas que seran destinadas como divisoras entre racks ( espacios de piso vacios )
    $("#radio-delete-fila").bind("click",function(){
       //agregar al ArraySeleccion lo que necesita hacer
       var json                = {};
       json['idFila']          = filaSeleccionada;
       json['accion']          = 'eliminar';
       json['nombreRack']      = '';
       json['descripcionRack'] = '';
       
       $("#table-matriz-it td").each(function() {
            var id = $(this).attr("id");
            if(id === filaSeleccionada)
            {
                $(this).addClass("fila-eliminada");
            }
        });
       
       arraySeleccionados.push(json);
       
       //cerrar panel de ingreso de nuevo rack
       $("#panel-agregar-rack").dialog("close");
       
       //Agregar informacion en resumen
       agregarResumenRacks();
    });
    
    $("#radio-pasillo-fila").bind("click",function(){
       //agregar al ArraySeleccion lo que necesita hacer
       var json                = {};
       json['idFila']          = filaSeleccionada;
       json['accion']          = 'bloquear';
       json['nombreRack']      = '';
       json['descripcionRack'] = '';
       
       $("#table-matriz-it td").each(function() {
            var id = $(this).attr("id");
            if(id === filaSeleccionada)
            {
                $(this).addClass("fila-bloqueada");
            }
        });
       
       arraySeleccionados.push(json);
       
       //cerrar panel de ingreso de nuevo rack
       $("#panel-agregar-rack").dialog("close");
       
       //Agregar informacion en resumen
       agregarResumenRacks();
    });
});


function validador(e, tipo) {

    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    
    var optTipo = $("#cmb-tipo-rack").val();
       
    if (tipo == 'numeros') {
        letras = "0123456789";
        especiales = [45, 46];
    } else if (tipo == 'letras') {        
        especiales = [8, 36, 35, 45, 47, 40, 41, 46, 32, 37, 39];        
    }
    else if (tipo == 'ip') {
        letras = "0123456789";
        especiales = [8, 46];
    }
    else {
        letras = "abcdefghijklmnopqrstuvwxyz0123456789";
        especiales = [8, 36, 35, 45, 47, 40, 41, 46];
        if(optTipo === 'DC')
        {
            especiales.push(32);
        }
    }

    tecla_especial = false
    for (var i in especiales) {
        if (key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }


    if (letras.indexOf(tecla) == -1 && !tecla_especial)
        return false;
}

function validacionesForm() {
    
    if(tipoRack === 'S')
    {
        //validar nombre caja
        if (document.getElementById("telconet_schemabundle_infoelementoracktype_nombreElemento").value == "") {
            alert("Falta llenar algunos campos");
            return false;
        }       
    }
    else
    {
        if(arraySeleccionados.length === 0)
        {
            Ext.Msg.show({
                title: 'Alerta',
                msg: 'Debe escoger al menos una opción ( Fila ) para continuar',
                buttons: Ext.Msg.OK                    
            });
            return false;
        }
    }
    
    //validar modelo
    if (document.getElementById("telconet_schemabundle_infoelementoracktype_modeloElementoId").value == "") {
        alert("Falta llenar algunos campos");
        return false;
    }

    //validar nodo
    if (combo_nodos.value == "" || combo_nodos.value == null) {
        alert("Falta llenar algunos campos");
        return false;
    }


    return true;
}

function obtenerMatrizGrid(canton)
{    
    $.ajax({
        type: "POST",
        url: url_getInformacionRacksDC,
        data:
            {
                'nombreCanton': canton
            },
        beforeSend: function()
        {
            Ext.get(document.body).mask('Cargando información de Filas y Racks');
        },
        complete: function()
        {
            Ext.get(document.body).unmask();
        },
        success: function(data)
        {
            if(data.length === 0)
            {
                Ext.Msg.show({
                    title: 'Alerta',
                    msg: 'No existe Información de Racks para el Data Center Requerido',
                    buttons: Ext.Msg.OK                    
                });
            }
            else
            {
                $(".content-rack-dc").show();
                
                $("#content-seleccion-rack-dc").find("tr").remove();
                
                //Renderizar información de rack de acuerdo a la fila seleccionada                
                drawGrid($("#content-seleccion-rack-dc"),'crearRack',data,canton);
                
                $("#content-seleccion-rack-dc").find("td").each(function(){
                    var clase = $(this).attr("class");
                    if(clase === 'fila-habilitada' )
                    {                                
                        $(this).bind("click",function(){
                            filaSeleccionada = $(this).attr("id");
                            if($(this).attr("class") === 'fila-habilitada')
                            {
                                $("#button-agregar").removeClass("ui-state-disabled").attr("disabled", false);
                                $("#input-nombre-rack").val("");
                                $("#input-descripcion-rack").val("");
                                $("#table-add-rack").hide();
                                $("#panel-agregar-rack").dialog("open");
                            }
                        });
                    }
                });
            }
        }
    });
}

function validarFormularioRacks()
{
    var nombre = $("#input-nombre-rack").val();
    var descr  = $("#input-descripcion-rack").val();
    var dimen  = $("#input-dimensiones-rack").val();
    
    if(Ext.isEmpty(nombre))
    {
        Ext.Msg.show({
            title: 'Alerta',
            msg: 'Debe llenar todos los campos para la creación de Rack',
            buttons: Ext.Msg.OK                      
        });
    }
    else
    {
       var json                = {};
       json['idFila']          = filaSeleccionada;
       json['accion']          = 'agregar';
       json['nombreRack']      = nombre;
       json['descripcionRack'] = descr;
       json['dimensiones']     = dimen;
       
        $("#table-matriz-it td").each(function() {
            var id = $(this).attr("id");
            if(id === filaSeleccionada)
            {
                $(this).addClass("fila-reservada");
            }
        });
       
       arraySeleccionados.push(json);
       $("#panel-agregar-rack").dialog("close");
       agregarResumenRacks();
       
       $("#input-dimensiones-rack").val("");
    }
}

function agregarResumenRacks()
{
    var html = '';
    
    $.each(arraySeleccionados, function(i, item) 
    {        
        html = '<tr id="'+item.idFila+'">';    
        html += '<td>'+item.idFila+'</td>';
        html += '<td>'+item.nombreRack+'</td>';
        html += '<td>'+item.descripcionRack+'</td>';
        html += '<td>'+item.accion+'</td>';
        html += '<td><i class="fa fa-times eliminar-seleccion" aria-hidden="true" title="Eliminar Selección" \n\
                     onclick="eliminarSeleccionNuevoRack(\''+item.idFila+'\')"></i></td>';
        html += '</tr>';
    });
      
    $("#table-content-resumen-nuevo-rack").append(html);
        
    //Agregar al input hidden el valor del json que sera enviado a guardar
    $("#hd-racks-dc").val(Ext.JSON.encode(arraySeleccionados));
}

function eliminarSeleccionNuevoRack(idFila)
{
    //Eliminar del grid de resumen
    $("#table-content-resumen-nuevo-rack tr").each(function(){
        var id = $(this).attr("id");
        if(id === idFila)
        {
            $(this).remove();
        }
    });
    
    //Eliminar de la lista final el registro
    arraySeleccionados = arraySeleccionados.filter(function(elem){        
        return elem.idFila !== idFila; 
    });
    
    //Eliminar clase de reservacion
    $("#table-matriz-it td").each(function(){
       var id = $(this).attr("id");       
       if(id === idFila)
       {           
           $(this).removeClass("fila-reservada");
           $(this).removeClass("fila-eliminada");
           $(this).removeClass("fila-bloqueada");
       }
    });
    
    //Agregar al input hidden el valor del json que sera enviado a guardar
    $("#hd-racks-dc").val(Ext.JSON.encode(arraySeleccionados));
        
}
