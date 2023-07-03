
var disponibles = 0;
var reservados  = 0;
var ocupados    = 0;

/**
 * 
 * @param {type} content  ( Elemento contenedor del grid )
 * @param {type} usage    ( Donde sera usado el grid : factibilidad - crearRack )
 * @param {type} jsonData ( json de la informacion de filas y racks mapeados )
 * @returns {undefined}
 */
function drawGrid(content,usage,jsonData,ciudadDataCenter, jsonFactibilidad)
{ 
    var table = '<table id="table-matriz-it" width="100%" height="100%">';
    
    if(ciudadDataCenter === 'GUAYAQUIL')
    {
        var limiteMaxMinLateral  = 31;
        var limiteMaxMinCabecera = 0;
    
        var i = limiteMaxMinLateral;
        
        while(i > 0)
        {
            table += ' <tr>';

            var j            = limiteMaxMinCabecera;
            var numeroFila   = 1;
            var numeroVector = limiteMaxMinCabecera;

            while(j < 45)
            {
                //Cabecera mostrando los valores de la matriz
                if(i === limiteMaxMinLateral && j > limiteMaxMinCabecera)
                {
                    table += '<td align="center" class="cabeceras-td">' + getInformacionPerimetral('cabecera', j , ciudadDataCenter) + '</td>';
                }
                else
                {
                    if(i === limiteMaxMinLateral && j === limiteMaxMinCabecera)
                    {
                        table += '<td class="td-no-class"></td>';
                    }
                    else
                    {
                        if(j === limiteMaxMinCabecera && i < limiteMaxMinLateral)
                        {
                            table += '<td align="center" style="width:10px" class="cabeceras-td">' + 
                                      getInformacionPerimetral('lateral', i, ciudadDataCenter) + '1</td>';
                        }
                        else
                        {
                            //No se coloca los bordes de la separacion de rack a los cuadrantes requeridos
                            //Segun grafico de ejemplo los 3 cuadrantes en las 4 direcciones van vacios
                            if(i !== 0 && i !== 1 && i !== 2 && i !== 3 && i !== 30 && i !== 29 && i !== 28 && i !== 31)
                            {
                                if(j < 35)//Dado el colspan se reduce como limite a 45 dado que una celda siempre es tomada por dos ( a nivel grafico )
                                {
                                    //se determina segun referencia desde donde inicia cada referencia de fila y rack
                                    //va de 3 en 3 dado que es la escala usada por DC en el cuadrante
                                    //tomar en cuenta que el colspan hace que ese segmento de dos celdas sea tomado como un solo indice 
                                    //( que cubre 2 espacios )
                                    if(j === 4 || j === 7 || j === 10 || j === 13 || j === 16 || j === 19 || j === 22 || j === 25 || j === 28 || j === 31)
                                    {
                                        if(numeroFila % 2 === 0)
                                        {
                                            numeroVector = numeroVector + 3;
                                        }
                                        else
                                        {
                                            numeroVector = numeroVector + 5;
                                        }

                                        numeroFila++;

                                        if(j < 41)
                                        {
                                            //Pintar las clases del grid de acuerdo a los estados mapeados previamente
                                            table += obtenerTdDinamico(usage,jsonData,numeroFila-1,i,numeroVector,ciudadDataCenter,jsonFactibilidad);
                                        }
                                        else
                                        {
                                            table += '<td></td>';
                                        }
                                    }
                                    else
                                    {
                                        //Indices en el cuadrante donde se grafica los pasillos entre filas de racks habiles
                                        if(j=== 5 || j=== 6 || j=== 11 || j===12 || j===17 || j===18 || j===23 || j===24 || j===29 || j===30)
                                        {
                                            table += '<td class="fila-entre-racks"></td>';
                                        }
                                        else
                                        {
                                            table += '<td></td>';
                                        }
                                    }
                                }
                            }
                            else
                            {
                                table += '<td></td>';
                            }
                        }
                    }
                }
                j++;
            }
            i--;
        }
    }
    else//UIO
    {
        var limiteMaxMinCabecera = 40;
        var limiteMaxMinLateral  = 29;
        
        var i = limiteMaxMinLateral;
        
        while(i > 0)
        {
            table += ' <tr>';

            var j            = limiteMaxMinCabecera;
            var numeroFila   = 9;
            var numeroVector = limiteMaxMinCabecera + 1;

            while(j > 0)
            {
                //Cabecera mostrando los valores de la matriz
                if(i === limiteMaxMinLateral && j < limiteMaxMinCabecera)
                {
                    table += '<td align="center" class="cabeceras-td">' + getInformacionPerimetral('cabecera', j, ciudadDataCenter) + '</td>';
                }
                else
                {
                    if(i === limiteMaxMinLateral && j === limiteMaxMinCabecera)
                    {
                        table += '<td class="td-no-class"></td>';
                    }
                    else
                    {
                        if(j === limiteMaxMinCabecera && i < limiteMaxMinLateral)
                        {
                            table += '<td align="center" style="width:10px" class="cabeceras-td">' + 
                                     getInformacionPerimetral('lateral', i, ciudadDataCenter)+'</td>';
                        }
                        else
                        {
                            //No se coloca los bordes de la separacion de rack a los cuadrantes requeridos
                            //Segun grafico de ejemplo los 3 cuadrantes en las 4 direcciones van vacios
                            if(i !== 0 && i !== 1 && i !== 2 && i !== 3 && i !== 26 && i !== 27 && i !== 29 && i !== 28)
                            {
                                if(j < 31)
                                {
                                    //se determina segun referencia desde donde inicia cada referencia de fila y rack
                                    //va de 3 en 3 dado que es la escala usada por DC en el cuadrante
                                    //tomar en cuenta que el colspan hace que ese segmento de dos celdas sea tomado como un solo indice 
                                    //( que cubre 2 espacios )
                                    if(j === 3 || j === 6 || j === 9 || j === 12 || j === 15 || j === 18 || j === 21 || j === 25 || j === 28)
                                    {
                                        if(numeroFila === 9)
                                        {
                                            numeroVector = numeroVector - 5;
                                        }
                                        else if(numeroFila === 8)
                                        {
                                            numeroVector = numeroVector - 3;
                                        }
                                        else if(numeroFila === 7)
                                        {
                                            numeroVector = numeroVector - 6;
                                        }
                                        else
                                        {
                                            if(numeroFila % 2 === 0)
                                            {
                                                numeroVector = numeroVector - 3;
                                            }
                                            else
                                            {
                                                numeroVector = numeroVector - 5;
                                            }
                                        }

                                        numeroFila--;
                                        
                                        if(j < limiteMaxMinCabecera)
                                        {
                                            //Pintar las clases del grid de acuerdo a los estados mapeados previamente
                                            table += obtenerTdDinamico(usage,jsonData,9-numeroFila,i,numeroVector,ciudadDataCenter,jsonFactibilidad);
                                        }
                                        else
                                        {
                                            table += '<td></td>';
                                        }
                                    }
                                    else
                                    {
                                        //Indices en el cuadrante donde se grafica los pasillos entre filas de racks habiles
                                        if(j=== 1 || j=== 2 || j=== 8 || j=== 7 || 
                                          ((j=== 14 || j=== 13) ) || 
                                            j=== 20 || j=== 19 || 
                                          ((j=== 27 || j=== 26) ))
                                        {
                                            table += '<td class="fila-entre-racks"></td>';
                                        }
                                        else
                                        {
                                            table += '<td></td>';
                                        }
                                    }
                                }
                            }
                            else
                            {
                                table += '<td></td>';
                            }
                        }
                    }
                }
                j--;
            }
            i--;
        }
    }
    
    table += '</table>';
    
    content.append(table);
}

function drawRack(content,rackName,data)
{   
    arrayUdRack    = [];
    arrayNombreRack= [];
    var arrayData =data;  
    
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
    switch(arrayData.rangoFinal) {
        case '42':
                            //Se grafica los rack ocupados y disponibles de acuerdo a la data mapeada y generada 
                            var i=42;
                            while (i > 0) 
                            {
                                var json   = getInformacionUdRack(i,data.arrayUnidadesRack);

                                var estado = json.estado;

                                var clase  = '';

                                if(estado === 'Disponible')
                                {
                                    clase = "desocupado";
                                    disponibles++;
                                }
                                else
                                {
                                    clase = "ocupado";
                                    ocupados++;
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
                        var i=1;
                            while (i < 43) 
                            {
                        var json   = getInformacionUdRack(i,data.arrayUnidadesRack);

                                var estado = json.estado;

                                var clase  = '';

                                if(estado === 'Disponible')
                                {
                                    clase = "desocupado";
                                    disponibles++;
                                }
                                else
                                {
                                    clase = "ocupado";
                                    ocupados++;
                                }

                                    table += ' <tr>'+
                                            '<td width="10%" class="borde" style="height:10px;">'+i+'</td>'+
                                            '<td id="'+json.id+'" width="90%" class="'+clase+'" style="height:10px;" input-val="'+json.nombre+'"></td>'+
                                            '<td width="10%" class="borde" style="height:10px;">&nbsp;&nbsp;</td>'+
                                          '</tr>';	    	    	    
                                    i++; 
                            }
                            break;      
    }

	table += '<tr>'+
		        '<td colspan="3" class="borde" style="height: 8px;"></td>'+
		     '</tr>'+
		     '<tr>'+
		        '<td colspan="3" class="borde"></td>'+
		     '</tr>'+
		   '</table>';
           
    content.html(table);
    
    //Mostrar los numeros de disponibilidad
    $("#label-disponibles").val(disponibles);
    $("#label-reservados").val(reservados);
    $("#label-ocupados").val(ocupados);
    
    //Funcionalidad para seleccion dinamica de posiciones de rack
    var active = false;
    
    $("#table-rack td").mousedown(function(ev) 
    {
        active = true;
        ev.preventDefault(); // this prevents text selection from happening

        var clase = $(this).attr("class");
        var id    = $(this).attr("id");
        var name  = $(this).attr("input-val");

        if(clase.includes("desocupado"))
        {
            arrayUdRack.push(id);
            arrayNombreRack.push(name);
            
            $(this).removeClass("desocupado");
            $(this).addClass("seleccionado");
            
            disponibles--;
            reservados++;
            
            $("#label-disponibles").val(disponibles);
            $("#label-reservados").val(reservados);
        }
        else if(clase.includes("seleccionado"))
        {
            var index     = arrayUdRack.indexOf(id);
            var indexName = arrayNombreRack.indexOf(name);
            
            arrayUdRack.splice(index, 1);
            arrayNombreRack.splice(indexName, 1);
            
            $(this).removeClass("seleccionado");
            $(this).addClass("desocupado");
            
            disponibles++;
            reservados--;
            
            $("#label-disponibles").val(disponibles);
            $("#label-reservados").val(reservados);
        }
        
        mostrarDetallesSeleccionados();
        
        var clase = $(this).attr("class");
    });

    $("#table-rack td").mousemove(function(ev) 
    {
        var clase = $(this).attr("class");
        var id    = $(this).attr("id");
        var name  = $(this).attr("input-val");
        
        if(clase.includes("desocupado"))
        {
            if(active) 
            {                
                disponibles--;
                reservados++;
                
                $("#label-disponibles").val(disponibles);
                $("#label-reservados").val(reservados);
                
                $(this).removeClass("desocupado");
                $(this).addClass("seleccionado");
                
                arrayUdRack.push(id);
                arrayNombreRack.push(name);
                               
                mostrarDetallesSeleccionados();
            }
        }
    });
    
    $(document).mouseup(function(ev) 
    {
        active = false;
    });
    
    $("#button-agregar").find("i").remove();
    $("#button-cerrar").find("i").remove();
    $("#button-agregar").prepend('<i class="fa fa-plus" aria-hidden="true"></i>&nbsp;');
    $("#button-cerrar").prepend('<i class="fa fa-times" aria-hidden="true"></i>&nbsp;');
}

function agregarInformacionRack()
{
    var cantidadUnidades = arrayUdRack.length;
    
    switch(tipoAsignacion)
    {
        case 'RACK (42 Us)':
            if(cantidadUnidades !== 42)
            {
                Ext.Msg.show({
                    title: 'Alerta',
                    msg: 'Debe escoger todas las unidades del Rack requerido',
                    buttons: Ext.Msg.OK,                    
                    icon: Ext.MessageBox.ALERT
                });
                return;
            }
            break;
            
        case 'RACK (45 Us)':
            if(cantidadUnidades !== 45)
            {
                Ext.Msg.show({
                    title: 'Alerta',
                    msg: 'Debe escoger todas las unidades del Rack requerido',
                    buttons: Ext.Msg.OK,                    
                    icon: Ext.MessageBox.ALERT
                });
                return;
            }
            break;
            
        case 'MEDIO RACK (21 Us)':
            if(cantidadUnidades < 21)
            {
                Ext.Msg.show({
                    title: 'Alerta',
                    msg: 'Debe escoger 21 unidades de Rack',
                    buttons: Ext.Msg.OK,                    
                    icon: Ext.MessageBox.ALERT
                });
                return;
            }
            break;
            
        default:
            break;
    }
    
    var json = {};    
    
    json['nombreFila'] = nombreFilaGestion;
    json['idFila']     = idFilaGestion;
    json['idRack']     = idRackGestion;
    json['nombreRack'] = nombreRackGestion;    
    json['reservados'] = reservados;    
    json['unidadesRack'] = [];
    
    var jsonUdRack = {};
    
    $.each(arrayUdRack, function(i, item) {
        jsonUdRack = {};
        jsonUdRack['idUdRack']   = item;
        jsonUdRack['numeroRack'] = i;
        json['unidadesRack'].push(jsonUdRack);
    });
    
    arraySeleccion.push(json);
    
    $("#panel-seleccion-rack").dialog("close");
    
    agregarAsignacionResumen();
}

function agregarAsignacionResumen()
{
    var html='';
    //Graficar informacion en el grid
    html += '<tr id="'+nombreFilaGestion+'">';    
    html += '<td>'+nombreRackGestion+'</td>';
    html += '<td>'+nombreFilaGestion+'</td>';
    html += '<td>'+reservados+'</td>';
    html += '<td>'+disponibles+'</td>';
    html += '<td><i class="fa fa-times eliminar-seleccion" aria-hidden="true" title="Eliminar Selección" \n\
                 onclick="eliminarAsignacion(\''+nombreFilaGestion+'\')"></i></td>';
    html += '</tr>';
    
    $("#table-content-resumen-factibilidad-rack").append(html);
    
    //Poner como bloqueado la fila/rack seleccionado
    $("#table-matriz-it td").each(function(){
       var id = $(this).attr("id");
       if(id === nombreFilaGestion)
       {           
           $(this).addClass("fila-reservada");
       }
    });
    
    $("#button-guardar").removeClass("ui-state-disabled").attr("disabled", false);
}

function eliminarAsignacion(nombre)
{    
    //Eliminar del grid de resumen
    $("#table-content-resumen-factibilidad-rack tr").each(function(){
        var id = $(this).attr("id");
        if(id === nombre)
        {
            $(this).remove();
        }
    });
    
    //Eliminar de la lista final el registro
    arraySeleccion = arraySeleccion.filter(function(elem){        
        return elem.nombreFila !== nombre; 
    });
    
    //Eliminar clase de reservacion
    $("#table-matriz-it td").each(function(){
       var id = $(this).attr("id");
       if(id === nombre)
       {           
           $(this).removeClass("fila-reservada");
       }
    });
    
    if(arraySeleccion.length === 0)
    {
        $("#button-guardar").addClass("ui-state-disabled").attr("disabled", true);
    }
}

function mostrarDetallesSeleccionados()
{
    var text = '';
    $.each(arrayNombreRack, function(i, item) {              
        var udr = 'UDR-'+item;
        text = text + " " + udr;
    });
    
    $("#resumen-text").val(text);
    
    if(text !== '')
    {
        $("#button-agregar").removeClass("ui-state-disabled").attr("disabled", false);
    }
    else
    {
        $("#button-agregar").addClass("ui-state-disabled").attr("disabled", true);
    }
}

function marcarDesmarcarRack(tipo)
{    
    $("#table-rack td").each(function()
    {
        var clase = $(this).attr("class");
        var id    = $(this).attr("id");
        var name  = $(this).attr("input-val");

        if(tipo === 'marcar' && clase.includes("desocupado"))
        {
            arrayUdRack.push(id);
            arrayNombreRack.push(name);

            $(this).removeClass("desocupado");
            $(this).addClass("seleccionado");

            disponibles--;
            reservados++;

            $("#label-disponibles").val(disponibles);
            $("#label-reservados").val(reservados);
        }
        else if(tipo === 'desmarcar' && clase.includes("seleccionado"))
        {
            var index     = arrayUdRack.indexOf(id);
            var indexName = arrayNombreRack.indexOf(name);

            arrayUdRack.splice(index, 1);
            arrayNombreRack.splice(indexName, 1);

            $(this).removeClass("seleccionado");
            $(this).addClass("desocupado");

            disponibles++;
            reservados--;

            $("#label-disponibles").val(disponibles);
            $("#label-reservados").val(reservados);
        }
        
        mostrarDetallesSeleccionados();
    });   
}

//Funciones de renderizado
function getInformacionPerimetral(tipo, value, ciudad)
{
    var valorReferencia = '';
    var val             = '';
    
    if(ciudad === 'GUAYAQUIL')
    {
        val = value;//Se inicializa desde 0 dado que grafico DC-GYE son dimensiones que van de izq-der / abajo-arriba
    }
    else
    {
        val = 29-value;//valor lateral definido segun caracteristicas en dimensiones maximas ( height ) del grafico IT para DC-UIO
    }

    if(tipo === 'cabecera')
    {
        var valorReferencia = value;

        if((valorReferencia.toString().length) === 1)
        {
            valorReferencia = '0' + valorReferencia;
        }
    }
    else
    {
        var valorReferencia = String.fromCharCode(val + 64);

        if(valorReferencia === '[')valorReferencia = 'AA';
        else if(valorReferencia === '\\')valorReferencia = 'AB';
        else if(valorReferencia === ']')valorReferencia = 'AC';
        else if(valorReferencia === '^')valorReferencia = 'AD';
    }

    return valorReferencia;
}

function getInformacionRack(jsonData,id)
{
    var info = {};
    
    $.each(jsonData, function(i, item) {
        if(item.nombreFila === id) 
        {
            info['idRack'] = item.idRack;
            info['estado'] = item.estadoFila;
            info['nombre'] = item.nombreRack;  
            info['numero'] = item.unidadesDisponibles;
            return false;
        }
    });
    
    return info;
}

function getInformacionUdRack(id,data)
{
    var info = {};
   
    $.each(data, function(i, item) {        
        if(parseInt(item.nombreUdRack) === id) 
        {
            info['id']     = item.idUdRack;
            info['estado'] = item.estado;       
            info['nombre'] = item.nombreUdRack;
            return false;
        }
    });
    
    return info;
}

function modalLoadingMessage() 
{
    var modal       = $(".modal-message" );
    var contentText = $("#modal-content-message");

    this.show = function(message)
    {
        $(".ui-dialog-titlebar").hide();
        contentText.text(message);
        modal.dialog("open");
    };
        
    this.hide = function ()
    {
        $(".ui-dialog-titlebar").show();
        contentText.text("");
        modal.dialog("close");
    };
}

function obtenerTdDinamico(usage,jsonData,fila,i,j,ciudad,jsonFactibilidad)
{
    var tdHtml = '';
    var clase  = '';
    var separador = '.';    
    var claseCantidad = '';
    
    if(ciudad === 'GUAYAQUIL')
    {
        separador = '1.';
    }
    var idFila = getInformacionPerimetral('lateral', i, ciudad) + separador + getInformacionPerimetral('cabecera', j, ciudad);
    var json   = {};
    
    if(usage === 'factibilidad')
    {        
        json       = getInformacionRack(jsonData.posiciones,idFila);
        var nombre = json.nombre!==null?json.nombre:'';
        var numero = json.numero;

        //Determinar si la fila es designada para Jaula
        var esJaula       = false;
        var requiereJaula = false;
        var boolEsActiva  = false;

        $.each(jsonFilasJaula, function(i, item) {
            if(item.valor1 == fila)
            {
                esJaula = true;
                return false;
            }
        });
        
        if(numero === 0 )
        {
            claseCantidad = 'fila-all';
        }
        else if(numero === 21)
        {
            claseCantidad = 'fila-midle';
        }
        
        if(tipoAsignacion.includes("JAULA"))
        {
            requiereJaula = true;
            
            //Como en quito no esta definido que filas son jaulas, se da la opcion para que puedan seleccionar jaulas
            //donde se factible
            if(ciudad === 'QUITO')
            {
                esJaula = true;
            }
        }
        
        //FILAS USADAS COMO ESPACIOS DE PASO
        if(json.estado === 'InactivoEspacio')
        {
            clase = "fila-entre-racks";
        }

        //Si no requiere jaula entra en escenario de seleccion de racks standares
        if(!requiereJaula)
        {
            if(esJaula) //Todas las filas determinadas como JAULA son inhabilitadas en eventos de seleccion standard
            {
                if(json.estado === 'Activo' || json.estado === 'Inactivo')
                {
                    clase = "fila-no-habilitada";
                }
                else if(json.estado === 'Ocupado')
                {
                    clase = "fila-ocupada";
                }
                else
                {
                    clase = "td-no-class";
                    nombre = '';
                }
            }
            else
            {
                if(json.estado === 'Activo')
                {
                    clase        = "fila-habilitada";
                    boolEsActiva = true;
                }
                else if(json.estado === 'Inactivo')
                {
                    clase = "fila-no-habilitada";
                }
                else if(json.estado === 'Ocupado')
                {
                    clase = "fila-ocupada";
                }
                else
                {
                    clase = "td-no-class";
                    nombre = '';
                }
            }
        }
        else //Si requiere JAULA
        {
            if(!esJaula) //Si no es Jaula se inhabilitan las celdas a ser seleccionables
            {
                if(json.estado === 'Activo' || json.estado === 'Inactivo')
                {
                    clase = "fila-no-habilitada";
                }
                else //Si no tiene ningun estado ( filas elminidas o que no contienen ningun elemento )
                {
                    clase = "td-no-class";
                    nombre = '';
                }
            }
            else
            {
                if(json.estado === 'Activo')
                {
                    clase        = "fila-habilitada";
                    boolEsActiva = true;
                }
                else if(json.estado === 'Inactivo')
                {
                    clase = "fila-no-habilitada";
                }
                else if(json.estado === 'Ocupado')
                {
                    clase = "fila-ocupada";
                }
                else
                {
                    clase = "td-no-class";
                    nombre = '';
                }
            }
        }

        if(numero === 42)
        {
            clase = clase + " rack-completo";
        }

        if(boolEsActiva)
        {
            clase = clase + " " + claseCantidad;
            tdHtml = '<td title="FILA ' + fila + '" id="' + idFila + '" class="' + clase + '" colspan=2 \n\
                 data-position="'+fila+'">' + nombre + '</td>';
        }
        else
        {
            tdHtml = '<td title="FILA ' + fila + '" id="' + idFila + '" class="' + clase + '" colspan=2>' + nombre + '</td>';
        }
        
    }
    else if(usage === 'crearRack')
    {
        json       = getInformacionRack(jsonData,idFila);
        
        //FILAS USADAS COMO ESPACIOS DE PASO
        if(json.estado === 'InactivoEspacio')
        {
            clase = "fila-entre-racks";
            idFila= '';
        }
        //Si no requiere jaula entra en escenario de seleccion de racks standares
        else if(json.estado === 'Activo' || json.estado === 'Ocupado')
        {
            clase = "fila-no-habilitada";
        }
        else if(json.estado === 'Inactivo')
        {
            clase = "fila-habilitada";
        }
        else
        {
            clase  = "td-no-class";
            idFila = '';
        }

        tdHtml = '<td title="FILA '+fila+'" id="'+idFila+'" class="'+clase+'" colspan=2>'+idFila+'</td>';
    }
    else if(usage === 'mostrarResumen')
    {
        json       = getInformacionRack(jsonData,idFila);
        
        if(json.estado === 'InactivoEspacio')
        {
            clase = "fila-entre-racks";
            idFila= '';
        }
        //Si no requiere jaula entra en escenario de seleccion de racks standares
        else if(json.estado === 'Activo' || json.estado === 'Ocupado' || json.estado === 'Inactivo')
        {
            clase = "fila-ocupada";
        }
        else
        {
            clase  = "td-no-class";
            idFila = '';
        }
        
        tdHtml = '<td title="FILA '+fila+'" id="'+idFila+'" class="'+clase+'" colspan=2>'+idFila+'</td>';
    }
    else if(usage === 'regularizacion')
    {
        json       = getInformacionRack(jsonData.posiciones,idFila);
        var nombre = json.nombre!==null?json.nombre:'';
        var numero = json.numero;

        //Determinar si la fila es designada para Jaula
        var esJaula       = false;
        var requiereJaula = false;
        var boolEsActiva  = false;

        $.each(jsonFilasJaula, function(i, item) {
            if(item.valor1 == fila)
            {
                esJaula = true;
                return false;
            }
        });
        
        if(numero === 0 )
        {
            claseCantidad = 'fila-all';
        }
        else if(numero === 21)
        {
            claseCantidad = 'fila-midle';
        }                
        
        //FILAS USADAS COMO ESPACIOS DE PASO
        if(json.estado === 'InactivoEspacio')
        {
            clase = "fila-entre-racks";
        }

        //Si no requiere jaula entra en escenario de seleccion de racks standares
        if(!requiereJaula)
        {
            if(esJaula) //Todas las filas determinadas como JAULA son inhabilitadas en eventos de seleccion standard
            {
                if(json.estado === 'Activo' || json.estado === 'Inactivo')
                {
                    clase = "fila-no-habilitada";
                }
                else if(json.estado === 'Ocupado')
                {
                    clase = "fila-ocupada";
                }
                else
                {
                    clase = "td-no-class";
                    nombre = '';
                }
            }
            else
            {
                if(json.estado === 'Activo')
                {
                    clase        = "fila-habilitada";
                    boolEsActiva = true;
                }
                else if(json.estado === 'Inactivo')
                {
                    clase = "fila-no-habilitada";
                }
                else if(json.estado === 'Ocupado')
                {
                    clase = "fila-ocupada";
                }
                else
                {
                    clase = "td-no-class";
                    nombre = '';
                }
            }
        }
        else //Si requiere JAULA
        {
            if(!esJaula) //Si no es Jaula se inhabilitan las celdas a ser seleccionables
            {
                if(json.estado === 'Activo' || json.estado === 'Inactivo')
                {
                    clase = "fila-no-habilitada";
                }
                else //Si no tiene ningun estado ( filas elminidas o que no contienen ningun elemento )
                {
                    clase = "td-no-class";
                    nombre = '';
                }
            }
            else
            {
                if(json.estado === 'Activo')
                {
                    clase        = "fila-habilitada";
                    boolEsActiva = true;
                }
                else if(json.estado === 'Inactivo')
                {
                    clase = "fila-no-habilitada";
                }
                else if(json.estado === 'Ocupado')
                {
                    clase = "fila-ocupada";
                }
                else
                {
                    clase = "td-no-class";
                    nombre = '';
                }
            }
        }

        if(numero === 42)
        {
            clase = clase + " rack-completo";
        }
        
        //Mostrar/marcar los racks que son escogidos por el usuario en caso de que ya posea
        if(!Ext.isEmpty(jsonFactibilidad))
        {
            $.each(jsonFactibilidad, function(i, item)
            {
                if(json.idRack === item.idRack)
                {
                    clase = "fila-factible";
                    return false;
                }
            });
        }

        if(boolEsActiva)
        {
            clase = clase + " " + claseCantidad;
            tdHtml = '<td title="FILA ' + fila + '" id="' + idFila + '" class="' + clase + '" colspan=2 \n\
                 data-position="'+fila+'">' + nombre + '</td>';
        }
        else
        {
            tdHtml = '<td title="FILA ' + fila + '" id="' + idFila + '" class="' + clase + '" colspan=2>' + nombre + '</td>';
        }
    }
    return tdHtml;
}