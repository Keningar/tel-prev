var jsonFilasJaula = {};
var esOcupado      = false;
var arraySeleccion = [];
var elRack         = null;
var jsonInfoRack   = {};

$( function() 
{   
    var option = '<option disabled selected>Escoja el nombre del Data Center</option>';
    if(typeof arrayDataCenters !== 'undefined')
    {
        Ext.each(Ext.JSON.decode(arrayDataCenters),function(json)
        {        
            option += '<option id="'+json.id+'" canton="'+json.canton+'">'+json.nombreElemento+'</option>';        
        });
    }
       
   $('#cmbDataCenter').append(option);
   
   if($("#panel-seleccion-rack").length !== 0) 
   {
        $("#panel-seleccion-rack").dialog({
        autoOpen: false,
        modal: true,
        height:"auto",
        width:600,
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
                text: "Editar",
                disabled: true,
                click: function() 
                {
                    $("#resumen-text-nuevos").val("");
                    $("#resumen-text-liberados").val("");                    
                    editarInformacionRack();
                }
            },
            {
                id: "button-cerrar",
                text: "Cerrar",
                click: function() {
                    arraySeleccion = [];
                    $("#resumen-text-nuevos").val("");
                    $("#resumen-text-liberados").val("");
                    $(this).dialog("close");
                }
            }]        
    });       
   }
});

function obtenerMatrizGrid()
{   
    var arrayPosicion = [];
    
    $("#content-matriz-it").html("");
    var canton = $( "#cmbDataCenter option:selected" ).attr('canton');
    if(canton==='GUAYAQUIL')
    {
        arrayPosicion  = JSON.parse(arrayPosicionesGye);
        jsonFilasJaula = JSON.parse(arrayFilasJaulasGye);
    }
    else        
    {
        arrayPosicion  = JSON.parse(arrayPosicionesUio);
        jsonFilasJaula = JSON.parse(arrayFilasJaulasUio);
    }
    
    var json           = {};    
    json['posiciones'] = arrayPosicion;

    //Renderizar información de rack de acuerdo a la fila seleccionada                
    drawGrid($("#content-matriz-it"),'regularizacion',json,canton);

    $("#content-matriz-it").find("td").each(function(){
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
    
    var filas = '';
    $.each(jsonFilasJaula, function(i, item) {
        filas = filas + " " + item.valor1;
    });
    
    var textFilas = 'Ninguna';
    
    if(filas!=='')
    {
        textFilas = 'Filas ( '+filas+' )';
    }
    $("#label-filas-jaulas").html(textFilas); 
    
    $("#content-matriz-it").find("td").each(function(){
        var clase = $(this).attr("class")+"";
        if(clase.includes("fila-habilitada"))
        {
            $(this).bind("click",function()
            {
                verInformacionRackSimple($(this),json,'edicion');
            });
        }
    });
}

function verInformacionRackSimple(el,jsonDatos,usage)
{
    elRack       = el;
    jsonInfoRack = jsonDatos;
    var clase = el.attr("class");
    
    if(!clase.includes("fila-reservada") )
    {
        var id         = el.attr("id");
        var posiciones = jsonDatos.posiciones;

        $.each(posiciones, function(i, item) {
            if(item.nombreFila === id) 
            {            
                idFilaGestion     = item.idFila;
                idRackGestion     = item.idRack;
                nombreRackGestion = item.nombreRack;
                nombreFilaGestion = item.nombreFila;
                return false;
            }
        });
        Ext.MessageBox.show({
               msg: 'Cargando información del Rack...',
               progressText: 'Obteniendo...',
               width:300,
               wait:true,
               modal:true
            });
        
        //Traer informacion de las unidades disponibles
        $.ajax({
            type   : "POST",
            url    : urlGetUnidadesRack,
            data   : 
            {
              'idRack' : idRackGestion
            },
            success: function(data)
            {
                Ext.MessageBox.hide();                
                
                //Renderizar información de rack de acuerdo a la fila seleccionada
                var content = $("#panel-rack");
                drawRackSimple(content,nombreRackGestion,data,usage);         
                
                //Cuando se trate de edición se muestra la ventana como un div(html) y no como un componente Extjs
                if(usage === 'edicion')
                {
                    $("#resumen-text").val("");
                    $("#panel-seleccion-rack").dialog("open");
                }
            }
        });
    }
}

function drawRackSimple(content,rackName,data,usage)
{
    arrayUdRack    = [];
    arrayNombreRack= [];
    
    disponibles = 0;
    reservados  = 0;
    ocupados    = 0;
    
    var table = '<table id="table-rack" border=2 width="200px;">'+
		        '<tr>'+
			       '<td class="borde" ></td>'+
			       '<td height="5" align=center class="cabecera"><span class="titulo">'+rackName+'</span></td>'+
			       '<td class="borde" ></td>'+
		        '</tr>'+
		        '<tr>'+
			       '<td colspan="3" class="borde"></td>'+
		        '</tr>';
    //Se consulta los valores de inicio y fin para dibujar el rack
    var arrayData =data;        
    var json={};
    var i=0;
    var estado='';
    var clase  = '';
    switch(arrayData.rangoFinal) 
    {
        case '42':
                        //Se grafica los rack ocupados y disponibles de acuerdo a la data mapeada y generada        
                        i=42;
                        json={};
                        while (i > 0) 
                        {
                            json   = getInformacionUdRack(i,data.arrayUnidadesRack);

                            estado = json.estado;

                            clase  = '';

                            if(estado === 'Disponible')
                            {
                                clase = "desocupado";
                                disponibles++;
                            }
                            else
                            {
                                clase = "ocupadoEdicion";
                                ocupados++;
                            }
                            
                            //Validar si existe factibilidad para marcar la unidad de rack
                            if (typeof jsonFactibilidad !== 'undefined' && !Ext.isEmpty(jsonFactibilidad) && usage === 'consulta')
                            {
                                $.each(jsonFactibilidad, function(j, itemFact)
                                {
                                    var jsonUdRacks = itemFact.unidadesRack;

                                    $.each(jsonUdRacks, function(k, itemUdRack)
                                    {
                                        if(parseInt(itemUdRack.idUdRack) === json.id)
                                        {
                                            clase = 'factible';
                                            return false;
                                        }
                                    });
                                });            
                            }

                            table += ' <tr>'+
                                    '<td width="10%" class="borde" style="height:10px;">'+i+'</td>'+
                                    '<td id="'+json.id+'" width="90%" class="'+clase+'" style="height:10px;" input-val="'+json.nombre+'"></td>'+
                                    '<td width="10%" class="borde" style="height:10px;">&nbsp;&nbsp;</td>'+
                                  '</tr>';	    	    	    
                            i--; 
                        }
                        break;
        case '1':
                    //Se grafica los rack ocupados y disponibles de acuerdo a la data mapeada y generada        
                    i=1;
                    json={};
                    while (i < 43) 
                    {
                        json   = getInformacionUdRack(i,data.arrayUnidadesRack);
                        estado = json.estado;
                        clase  = '';

                        if(estado === 'Disponible')
                        {
                            clase = "desocupado";
                            disponibles++;
                        }
                        else
                        {
                            clase = "ocupadoEdicion";
                            ocupados++;
                        }
                        
                        //Validar si existe factibilidad para marcar la unidad de rack
                        if (typeof jsonFactibilidad !== 'undefined' && !Ext.isEmpty(jsonFactibilidad) && usage === 'consulta')
                        {
                            $.each(jsonFactibilidad, function(j, itemFact)
                            {
                                var jsonUdRacks = itemFact.unidadesRack;

                                $.each(jsonUdRacks, function(k, itemUdRack)
                                {
                                    if(parseInt(itemUdRack.idUdRack) === json.id)
                                    {
                                        clase = 'factible';
                                        return false;
                                    }
                                });
                            });            
                        }

                        table += ' <tr>'+
                                '<td width="10%" class="borde" style="height:10px;">'+i+'</td>'+
                                '<td id="'+json.id+'" width="90%" class="'+clase+'" style="height:10px;" input-val="'+json.nombre+'"></td>'+
                                '<td width="10%" class="borde" style="height:10px;">&nbsp;&nbsp;</td>'+
                              '</tr>';	    	    	    
                        i++; 
                    }
                    break;
        
        default: 
                     //Si la marca del rack seleccionado no está parametrizada en admi_parámetro con rangos mostrará error
                     Ext.Msg.alert('Mensaje', "Rango final para marca de rack no definido");
                     return false;
                     break;
    }
	
	table += '<tr>'+
		        '<td colspan="3" class="borde" style="height: 8px;"></td>'+
		     '</tr>'+
		     '<tr>'+
		        '<td colspan="3" class="borde"></td>'+
		     '</tr>'+
		   '</table>';
       
    if(usage === 'edicion')
    {
        content.html(table);
        $("#table-rack td").mousedown(function(ev) 
        {
            ev.preventDefault(); // this prevents text selection from happening
            var el    = $(this);
            var clase = $(this).attr("class");
            var id    = $(this).attr("id");
            var name  = $(this).attr("input-val");
            var flag  = false;
            var json  = {};

            if(clase.includes("desocupado"))
            {
                if(!Ext.isEmpty(arraySeleccion))
                {               
                    $.each(arraySeleccion,function(i,item){                    
                        if(item.id === id && item.estadoA === 'ocupadoEdicion')
                        {                                                
                            el.removeClass("desocupado");
                            el.addClass("ocupadoEdicion");

                            arraySeleccion = arraySeleccion.filter(function(elem){        
                                        return elem.id !== item.id; 
                                    });
                            flag = true;
                            return false;
                        }                    
                    });

                    if(!flag)
                    {
                        el.removeClass("desocupado");
                        el.addClass("seleccionado");

                        json            = {};
                        json['id']      = id;
                        json['nombre']  = name;
                        json['estadoN'] = 'seleccionado';
                        json['estadoA'] = 'desocupado';
                        arraySeleccion.push(json);
                    }
                }
                else
                {
                    el.removeClass("desocupado");
                    el.addClass("seleccionado");

                    json            = {};
                    json['id']      = id;
                    json['nombre']  = name;
                    json['estadoN'] = 'seleccionado';
                    json['estadoA'] = 'desocupado';
                    arraySeleccion.push(json);
                }
            }
            else if(clase.includes("seleccionado"))
            {
                $.each(arraySeleccion,function(i,item){                    
                    if(item.id === id)
                    {                                                
                        el.removeClass("seleccionado");
                        el.addClass("desocupado");

                        arraySeleccion = arraySeleccion.filter(function(elem){        
                                    return elem.id !== item.id; 
                                });
                        return false;
                    }
                });
            }
            else if(clase.includes("ocupadoEdicion"))
            {
                json           = {};
                json['id']     = id;
                json['nombre'] = name;
                json['estadoN'] = 'desocupado';
                json['estadoA'] = 'ocupadoEdicion';
                arraySeleccion.push(json);

                $(this).removeClass("ocupadoEdicion");
                $(this).addClass("desocupado");
            }

            mostrarDetallesSeleccionados();
        });  

        $("#button-agregar").find("i").remove();
        $("#button-cerrar").find("i").remove();
        $("#button-agregar").prepend('<i class="fa fa-plus" aria-hidden="true"></i>&nbsp;');
        $("#button-cerrar").prepend('<i class="fa fa-times" aria-hidden="true"></i>&nbsp;');
    }
    else//Para usage=consulta se muestra la ventana usando componente Extjs
    {
        //ventana para mostrar el rack como consulta
        var contentHtmlRack = Ext.create('Ext.Component', {
            html: 
               table,
            padding: 1,
            layout: 'anchor'
        });

        var formConsultaRack = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            BodyPadding: 10,
            width:210,
            height:570,
            frame: true,
            items: 
            [
                contentHtmlRack
            ],
            buttons: [
                {
                    text: 'Cerrar',
                    handler: function() 
                    {
                        winConsultarRack.close();
                        winConsultarRack.destroy();
                    }
                }
            ]});

        var winConsultarRack = Ext.widget('window', {
            id:'winConsultarRack',
            title: '<b>Visualización de Espacio en Racks</b>',
            layout: 'fit',
            resizable: true,
            modal: true,
            closable: true,
            width:'auto',
            items: [formConsultaRack]
        }).show();
    }
}

function mostrarDetallesSeleccionados()
{
    $("#resumen-text-nuevos").val("");
    $("#resumen-text-liberados").val("");
    var textSeleccionado = '';
    var textDesocupado   = '';
    $.each(arraySeleccion, function(i, item) {              
        var udr = 'UDR-'+item.nombre;        
        
        if(item.estadoN === 'seleccionado')
        {
            textSeleccionado = textSeleccionado + " " + udr;
            $("#resumen-text-nuevos").val(textSeleccionado);
        }
        
        if(item.estadoN === 'desocupado' && item.estadoA === 'ocupadoEdicion')
        {
            textDesocupado = textDesocupado + " " + udr;
            $("#resumen-text-liberados").val(textDesocupado);
        }
    });
            
    if(textSeleccionado !== '' || textDesocupado !== '')
    {
        $("#button-agregar").removeClass("ui-state-disabled").attr("disabled", false);
    }
    else
    {
        $("#button-agregar").addClass("ui-state-disabled").attr("disabled", true);
    }
}

function editarInformacionRack()
{
    Ext.MessageBox.show({
        msg: 'Editando información del Rack...',
        progressText: 'Editando...',
        width:300,
        wait:true,
        modal:true
    });
            
    $.ajax({
        type   : "POST",
        url    : urlEditarElementosPasivosDC,
        data   : 
        {
          'jsonEdicion' : Ext.JSON.encode(arraySeleccion)
        },
        success: function(data)
        {
            Ext.MessageBox.hide();                
            var mensaje = '';
            if(data.status === 'OK')
            {
                mensaje = data.mensaje;
                var arrayJsonConectados = Ext.JSON.decode(data.arrayConectados);
                
                if(!Ext.isEmpty(arrayJsonConectados))
                {
                    mensaje += '<br><br>Las siguientes unidades de Rack no fueron liberadas, porque se encuentran ocupadas:<br>'
                    Ext.each(arrayJsonConectados,function(item)
                    {
                        mensaje += '<br><i class="fa fa-share" aria-hidden="true"></i>'+
                                   '<b>Espacio</b>: UDRACK-'+item.nombreElemento+' ('+item.login+')';
                    });
                }                                                
                                                                
                Ext.Msg.alert('Mensaje', mensaje, function(btn){
			    if(btn=='ok')
                {
                    $("#panel-seleccion-rack").dialog("close");
                    verInformacionRackSimple(elRack,jsonInfoRack,'edicion');
			    }});
                
            }
            
            arraySeleccion = [];
            
        }
    });
}