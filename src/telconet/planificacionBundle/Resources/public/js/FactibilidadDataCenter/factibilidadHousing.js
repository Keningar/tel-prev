
var arraySeleccion = [];
var jsonDatos      = {};
var jsonFilasJaula = {};
var arrayUdRack    = [];
var arrayNombreRack= [];

//Informacion de Seleccion de unidades de rack y rack
var idFilaGestion     = null;
var idRackGestion     = null;
var nombreFilaGestion = null;
var nombreRackGestion = null;
var boolRequiereJaula = false;

//Informacion de validacion de cantidad y descripciones
var cantidad          = null;
var tipoAsignacion    = null;

$(function() {
        
    jsonDatos      = JSON.parse(arrayInformacion);
    jsonFilasJaula = JSON.parse(arrayFilasJaulas);
    
    cantidad       = jsonDatos.cantidad;        //Cantidad elegida por el comercial
    tipoAsignacion = jsonDatos.caracteristica;  //Caracteristica basica
    ciudad         = jsonDatos.canton;
    
    //Obtener la imagen del cuarto de IT=============================================
    drawGrid($("#content-matriz-it"),'factibilidad',jsonDatos,ciudad);        
    //===============================================================================
    
    
    //si la caracteristica contiene JAULA se debe ingresar la cantidad de racka que desea colocar
    $("#td-spinner").hide();
    $(".info-jaulas").hide();
    
    if(tipoAsignacion.includes('JAULA'))
    {
        boolRequiereJaula = true;
        $( "#td-spinner" ).show();
        $(".info-jaulas").show();
        $("#racks-esperados-label").html($( "#spinner-numero-racks" ).val());
        //Inicializacion
        $( "#spinner-numero-racks" ).spinner({
            min: 2
        }).val(2);
        
        $("#racks-esperados-label").html($( "#spinner-numero-racks" ).val());
        
        $('#spinner-numero-racks').on("spinstop", function(){
            var spinnerval = $(this).spinner('value');
            valorUnidadesEsperadas = cantidad * 42 * spinnerval;
            $("#unidades-esperadas-label").html(valorUnidadesEsperadas+" Us ");
            $("#racks-esperados-label").html(spinnerval);
         });
    }
    
    //setear informacion comercial basica
    $("#datacenter-label").html('<label><i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp;'+ciudad+'</label>');
    $("#cliente-label").html("<label>"+jsonDatos.cliente+"</label>");
    $("#login-label").html("<label>"+jsonDatos.login+"</label>");
    $("#producto-label").html("<label>"+jsonDatos.producto+"</label>");
    $("#caracteristica-label").html("<label>"+tipoAsignacion+"</label>");
    $("#cantidad-label").html("<label>"+cantidad+"</label>");
    
    var valorUnidadesEsperadas = 0;
    $("#unidades-esperadas-label").html(0);
    
    //Calcular las cantidad de unidades a seleccionar esperada
    if(tipoAsignacion === 'RACK (42 Us)')
    {
        valorUnidadesEsperadas = cantidad * 42;
    }
    else if(tipoAsignacion === 'RACK (45 Us)')
    {
        valorUnidadesEsperadas = cantidad * 45;
    }
    else if(tipoAsignacion === 'MEDIO RACK (21 Us)')
    {
        valorUnidadesEsperadas = cantidad * 21;
    }
    else if(tipoAsignacion === 'UNIDAD RACK (Us)')
    {
        valorUnidadesEsperadas = cantidad;
    }
    else//JAULA
    {
        valorUnidadesEsperadas = cantidad * 42 * $( "#spinner-numero-racks" ).val();
    }
    
    $("#unidades-esperadas-label").html(valorUnidadesEsperadas+" Us ");
    
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
    
    //Inicializar
    $(".modal-message").dialog({
        autoOpen: false,
        modal: true,
        closeOnEscape: false,
        dialogClass: 'no-close',
        height:80,
        width:'auto'
    }); 
    
    //Panel que muestra la informacion del rack
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
                text: "Agregar",
                disabled: true,
                click: function() {
                    agregarInformacionRack();
                }
            },
            {
                id: "button-cerrar",
                text: "Cerrar",
                click: function() {
                    $(this).dialog("close");
                }
            }]        
    });       
    
    //=====================================================================================
    //                             BIND EVENTOS SOBRE ELEMENTOS
    //=====================================================================================
    
    //Se hace el bind de eventos para filas que pueden tener o lanzar uno
    $("#content-matriz-it").find("td").each(function(){
        var clase = $(this).attr("class")+"";
        if(clase.includes("fila-habilitada"))
        {
            $(this).bind("click",function()
            {
                //Si no requiere JAULA o RACK completo se escoge las unidades de rack requeridas
                if(boolRequiereJaula || tipoAsignacion === 'RACK (42 Us)' || tipoAsignacion === 'RACK (45 Us)' )
                {
                    setInformacionJaulas($(this));
                }
                else
                {
                    verInformacionRack($(this));
                }
            });
        }
    });
    
    //Inicializar Radio select
    $( ".rdbtn-seleccion-rack" ).checkboxradio({icon: false});
    
    $("#radio-marcar").bind("click",function(){
       marcarDesmarcarRack("marcar");
    });
    
    $("#radio-desmarcar").bind("click",function(){
       marcarDesmarcarRack("desmarcar");
    });
    
    //Botones de funcionalidad principal
    $( "#button-guardar" ).button().click(function(){
        guardarFactibilidad();
    });
    
    $( "#button-regresar" ).button().click(function(){
        window.location = urlIndexFactibilidad;
    });
        
    $("#button-guardar").addClass("ui-state-disabled").attr("disabled", true);
    
    $(document).tooltip({
        items: "[data-position]",
        content: function() 
        {
            var element = $(this);
            if(element.is("[data-position]")) 
            {
                var idFila = element.attr("id");
                var fila   = element.attr("data-position");
                var html   = '';
                //recorrer json con informacion de posicion
                $.each(jsonDatos.posiciones, function(k, item)
                {
                    if(item.nombreFila == idFila)
                    {
                        html = '<table><tr><td><i class="fa fa-bars" aria-hidden="true"></i>&nbsp;<b>Fila:</b></td>\n\
                                   <td><b><input style="width:50px;text-align:center;background:#1C94C4;color:white;" \n\
                                       type="text" value="' + fila + '" disabled/></b></td></tr>';
                        html += '<tr><td><i class="fa fa-share" aria-hidden="true"></i>&nbsp;<b>Ocupados:</b></td>\n\
                                   <td><b><input style="width:50px;text-align:center;" \n\
                                       type="text" value="' + item.unidadesDisponibles + '" disabled/></b></td></tr>';
                        html += '<tr><td><i class="fa fa-share" aria-hidden="true"></i>&nbsp;<b>Disponibles:</b></td>\n\
                                   <td><b><input style="width:50px;text-align:center;" \n\
                                       type="text" value="' + item.unidadesUsables + '" disabled/></b></td></tr></table>';
                    }
                });

                return html;
            }
        }
    });
});

//Funciones
function verInformacionRack(el)
{
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

        //Traer informacion de las unidades disponibles
        $.ajax({
            type   : "POST",
            url    : urlGetUnidadesRack,
            data   : 
            {
              'idRack' : idRackGestion
            },
            beforeSend: function() 
            {
                (new modalLoadingMessage()).show("Cargando Información de Rack");                     
            },
            complete: function() 
            {
                (new modalLoadingMessage()).hide();     
            },
            success: function(data)
            {
                //Renderizar información de rack de acuerdo a la fila seleccionada
                var content = $("#panel-rack");
                drawRack(content,nombreRackGestion,data);
                $("#resumen-text").val("");
                $("#panel-seleccion-rack").dialog("open");
            }
        });
    }
}

function setInformacionJaulas(el)
{
    var clase = el.attr("class");
    
    if(!clase.includes("fila-reservada") && !clase.includes("fila-ocupada") )
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

        var json = {};    
        
        //Por default en JAULAS los Racks poseen todas sus unidades reservadas ( 42 Us ) o ( 45 Us )
        if(tipoAsignacion === 'RACK (42 Us)')
        {
            reservados  = 42;
        }
        if(tipoAsignacion === 'RACK (45 Us)')
        {
            reservados  = 45;
        }
        
        disponibles = 0;
    
        json['nombreFila'] = nombreFilaGestion;
        json['idFila']     = idFilaGestion;
        json['idRack']     = idRackGestion;
        json['nombreRack'] = nombreRackGestion;    
        json['reservados'] = reservados;    
        json['unidadesRack'] = [];
        
        arraySeleccion.push(json);
        
        agregarAsignacionResumen();
    }
}

function guardarFactibilidad()
{
    var totalUnidadesRequeridas = 0;
    //Validar que se cumpla el requerimiento de cantidad vs tipo de caracteristica
    //se obtiene el numero total de unidades de rack que necesita contratar
    switch(tipoAsignacion)
    {
        case 'RACK (42 Us)':
            totalUnidadesRequeridas = cantidad * 42;
            break;
            
        case 'RACK (45 Us)':
            totalUnidadesRequeridas = cantidad * 45;
            break;
            
        case 'MEDIO RACK (21 Us)':
            totalUnidadesRequeridas = cantidad * 21;
            break;
            
        case 'UNIDAD RACK (Us)':
            totalUnidadesRequeridas = cantidad;
            break;
            
        default:
            totalUnidadesRequeridas = cantidad * 42 * $( "#spinner-numero-racks" ).val();
            break;
    }
    
    var contarReservados = 0;
    
    $.each(arraySeleccion, function(i, item) {              
        contarReservados = contarReservados + item.reservados;
    });
    
    
    if(contarReservados !== totalUnidadesRequeridas)
    {
        var mensaje = boolRequiereJaula?'Cantidades de Rack para la Jaula no cumple con el valor ingresado ('+$( "#spinner-numero-racks" ).val()+')':
                      'Cantidad de Unidades de Racks escogidas no cumple con la cantidad Requerida por el Cliente';
        Ext.Msg.show({
            title: 'Alerta',
            msg: mensaje,
            buttons: Ext.Msg.OK,                    
            icon: Ext.MessageBox.ALERT
        });
        return false;
    }
    else
    {
        $.ajax({
            type: "POST",
            url: urlGuardar,
            data:
                {
                    'idServicio': jsonDatos.servicio,
                    'datos'     : Ext.JSON.encode(arraySeleccion),
                    'tipo'      : boolRequiereJaula?'JAULA':'RACKS',
                    'ciudad'    : ciudad,
                    'tipoAsignacion': tipoAsignacion
                },
            beforeSend: function()
            {
                (new modalLoadingMessage()).show("Guardando Factibilidad Housing");
            },
            complete: function()
            {
                (new modalLoadingMessage()).hide();
            },
            success: function(data)
            {
                if(data.status === 'OK')
                {
                    Ext.MessageBox.show({
                                    title: 'Cargando Información Housing',
                                    msg: 'Cargando Información...',
                                    progressText: 'Redireccionando.',
                                    progress: true,
                                    closable: false,
                                    width: 300,
                                    wait: true,
                                    waitConfig: {interval: 200}
                                });
                    window.location = urlIndexFactibilidad;
                }
                else
                {
                    Ext.Msg.show({
                        title: 'Error',
                        msg: data.mensaje,
                        buttons: Ext.Msg.OK,                    
                        icon: Ext.MessageBox.ERROR
                    });
                }                
            }
        });
    }
}

//Validar que se respete el concepto de que una Jaula debe siempre tener racks en pares y que se vean uno frente al otro
function validarJaula()
{
    if(!boolRequiereJaula)return true;
    else
    {
        var boolValido = false;
        var contCoincidencias = 0;

        $.each(arraySeleccion, function(i, item) 
        {
            var arrayfila = item.nombreFila.split(".");
            var x = arrayfila[0];
            var j = arrayfila[1];

            $.each(arraySeleccion, function(i, item) 
            {
                var arrayfila2 = item.nombreFila.split(".");
                var xk = arrayfila2[0];
                var jk = arrayfila2[1];

                if(x === xk && j !== jk)
                {
                    var dif = j - jk;

                    if(Math.abs(dif) === 3)
                    {
                        contCoincidencias = contCoincidencias + 1;                        
                    }
                }
            });
        });
        
        if((contCoincidencias) == $( "#spinner-numero-racks" ).val())
        {
            boolValido = true;
        }
        
        return boolValido;
    }
}


