var arraySolucion        = [];
var plantillas           = [];
var arrayRecursosHosting = [];
var arrayRecursos        = [];
var arrayProductoInformacion = [];
var arraySubtiposSeleccionados = [];
var identificadorProducto = 0;
var esEdicionProducto = false;
var storageTotal    = 0;
var memoriaTotal    = 0;
var procesadorTotal = 0;
var storageUsado    = 0;
var memoriaUsado    = 0;
var procesadorUsado = 0;
var subgrupoBusqueda = '';
var boolEsPoolRecursosCompleto  = false;
var esMultiCaracteristica       = false;
var boolEsMultiAgrupacion       = false;
var boolCambioDisco             = false;
var boolEsLicencia              = false;
var boolCambioProcesador        = false;
var boolCambioMemoria           = false;
var idLiPreferencial            = '';
var contienePreferencial        = false;
var uiToConfigurate             = null;
var gridMaquinasVirtuales       = null;
var numberRaw                   = 1;
var arrayLicenciasEliminadas    = []; 
var arrayLicenciasEditadas      = [];
var arrayCambioMaquina          = [];
var accion = '';
var arrayMaquinasVirtuales      = [];
var nuevaSolucion               = true;

$( function() {   
               
    if(prefijoEmpresa === 'TN')
     {
         $("#servicio-unico-content").hide();
         $("#bs-content").hide();
     }
     else
     {
         $("#servicio-unico-content").show();
         $("#bs-content").hide();
     }
    
     $( "#tipoSeleccionProducto" ).selectmenu({
        change: function( event, ui ) {
            var id = $(this).val();
            if(id === "servicio_unico")
            {
                $("#servicio-unico-content").show();
                $("#bs-content").hide();
            }
            else if(id === "servicio_bs")
            {
                $("#servicio-unico-content").hide();
                $("#bs-content").show();
                getPropuestasTelcoCRM();
                document.getElementById('objFilaCotizacion').style.display='none';
                getGrupoSubgrupoProductos('GRUPO','','');
            }
         }
    });
    getLicencias();
    //Panel principal
    $( "#accordion" ).accordion({heightStyle: "content"});

    $( "#objSelectPropuestaBS" ).selectmenu({
        change:function(event,ui)
        {
            //Bloque que elimina todos los productos seleccionados, en caso de cambiar la propuesta.
            $("#right-productos").find("li").each(function()
            {
                eliminarProducto($(this));
            });
            $("#content-select-producto").hide();
            //Bloque que carga la lista de cotización de acuerdo a la propuesta seleccionada.
            $('#objSelectCotizacionBS').find('option').remove().end();
            $('#objSelectCotizacionBS').selectmenu('destroy').selectmenu({ style: 'dropdown' });
            var intIdPropuesta = $(this).val();
            getCotizacionTelcoCRM(intIdPropuesta);
            //Bloque que carga la lista de grupo, subgrupo, producto, de acuerdo a la propuesta seleccionada.
            $('#grupo_bs').find('option').remove().end();
            $('#grupo_bs').selectmenu('destroy').selectmenu({ style: 'dropdown' });
            var strOptionGrupo = '<option>Seleccione Grupo</option>';
            $("#grupo_bs").append(strOptionGrupo);
            getGrupoSubgrupoProductos('GRUPO','','');
            $( "#grupo_bs" ).selectmenu({
                change:function(event,ui)
                {
                   var strGrupo = $(this).val();
                   $( ".fd-tipo-subsolucion").hide();
                   getGrupoSubgrupoProductos('SUBGRUPO',strGrupo,'');
                }
            }).selectmenu( "menuWidget" )
                .addClass( "overflow" );
        }
    }).selectmenu( "menuWidget" ).addClass( "overflow" );

    $( "#objSelectCotizacionBS" ).selectmenu({change:function(event,ui){}
    }).selectmenu( "menuWidget" ).addClass( "overflow" );

    //Select de info de grupos existentes
    $( "#grupo_bs" ).selectmenu({
        change:function(event,ui)
        {            
           var grupo = $(this).val();
           $( ".fd-tipo-subsolucion").hide();
           getGrupoSubgrupoProductos('SUBGRUPO',grupo,'');           
        }
    }).selectmenu( "menuWidget" )
        .addClass( "overflow" );
    
    //Select de subgrupos existentes a partie de un grupo seleccionado
    $( "#subgrupo_bs" ).selectmenu();
    

    
    //Seleccion de de tipo de sub solucion configurado por grupo
    $( ".fd-tipo-subsolucion").hide();
    
    //======================================================= DESCRIPCION SOLUCION ===========================================
    $("#descripcion-solucion").html("<i class='fa fa-cubes fa-1x' aria-hidden='true'></i>&nbsp;\n\
                                     <b>Nombre de Solución</b>&nbsp;\n\
                                     <input style='width:300px;' type='text' placeholder='Ingrese el nombre de la Solución' \n\
                                            id='input-nombre-solucion'/>&nbsp;\n\
                                     <i class='fa fa-pencil-square-o' aria-hidden='true'></i>");
    //=======================================================
    
    //Select de frecuencia de facturacion
    $( "#frecuencia-bs" ).selectmenu();
    
    //==Inicializacion de botones principales del formulario===
    $( "#buttonGuardar" ).button().click(function(event)
    {
        //Validar que la informacion a guardar este completa                
        mostraDetalleSolucion();
    });
    
    $( "#buttonRegresar" ).button().click(function(event){
        window.location.replace(urlPathShowPunto);
    });
    //=========================================================
    
    //Panel de seleccion de productos
    $("#content-select-producto").hide();
    
    //Panel de seleccion de producto preferencial de ser necesaria ( HOUSING/HOSTING - Productos TN o Tercerizados )
    $("#content-producto-preferencial").hide();
    
    //Inicializacion de modal message customizado ( waiting/loading message )
    $(".modal-message").dialog({
        autoOpen: false,
        modal: true,
        closeOnEscape: false,
        dialogClass: 'no-close',
        height:80,
        width:'auto'
    });         
    
    //Modal panel para mostrar mensajes genericos
    $("#modal-infoError-message").dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",        
        width:"auto",
        modal: true,
        buttons: {          
          "Cerrar": function() {
            $( this ).dialog( "close" );
          }
        }
    });
    
    //Formulario de resumen de productos previo a guardar la informacion de servicios
    $("#content-resumen").dialog({
        autoOpen: false,
        modal: true,
        height:"auto",
        width:'auto',
        show: {
            effect: "blind",
            duration: 250
        },
        hide: {
            effect: "blind",
            duration: 250
        },
        buttons: [
            {
                id: "button-confirmar",
                click: function() {
                    //realizar submit  
                    guardarSolucion();
                }
            },
            {
                id: "button-cerrar-resumen",
                click: function() {
                    $(this).dialog("close");
                    $("#button-confirmar").find('i').remove();
                    $("#button-cerrar-resumen").find('i').remove();
                }
            }]        
    });  
    
    //Formulario de solución creada.
    $("#content-solucion-creada").dialog({
        dialogClass : "no-close",
        autoOpen    :  false,
        modal       :  true,
        height      : 'auto',
        width       : 'auto',
        show : {
            effect   : "blind",
            duration :  250
        },
        hide : {
            effect   : "blind",
            duration :  250
        },
        buttons : [
            {
                id    : "button-confirmar-solucion",
                click : function() {
                    (new modalLoadingMessage()).show("Redireccionando al listado de servicios...");
                    window.location.replace(urlPathShowPunto);
                }
            }
        ]
    });

    $("#content-relacion-subtiposolucion").dialog({
        autoOpen: false,
        modal: true,
        height:"auto",
        width:'auto',
        show: {
            effect: "blind",
            duration: 250
        },
        hide: {
            effect: "blind",
            duration: 250
        },
        buttons: [
            {
                id: "button-confirmar-relacion",
                text: "Configurar",
                click: function() {
                    configurarRelaciones();
                }
            },
            {
                id: "button-cerrar-relacion",
                text: "Cerrar",
                click: function() {
                    $(this).dialog("close");
                }
            }]        
    });  
    
    //instanciacion de tooltip para mostrar detalles de cada producto seleccionado
//    $( document ).tooltip({
//        items: "[data-position]",
//        content: function() {
//            var element = $( this );
//            if ( element.is( "[data-position]" ) ) {
//              var idProducto = element.attr("id");
//              var htmlProducto = getDetalleProducto(idProducto);
//              return htmlProducto;
//        }
//      }
//    });    
    
});

function beforeStopConnectedList(ui)
{    
    var dataValueProducto  = ui.item.attr("data-value");
    var idLi               = ui.item.attr("id");    
    var dataValueContainer = $($("#" + idLi).parent().get(0)).attr("data-value");
    var clasificacionPref  = '';
    
    $.each(arraySubtiposSeleccionados,function(i, item)
    {
        if(!validarReferenciaRelacionNoPreferencial(item))
        {
            clasificacionPref = item;
        }                
    });
        
    if(dataValueProducto==='P' && dataValueContainer === 'N')
    {
        Ext.Msg.alert("Error","Producto Preferencial debe ir dentro de la agrupación de <b>"+clasificacionPref+"</b>");
        $(ui.item).remove();

         //Se elimina del array la informacion que ya no es requerida
        eliminarProductoCatalogo($(ui.item),ui.item.attr("id"));            
                        
        var grupo    = $("#grupo_bs").val();
         
        $('#listaProductos').find('li').remove().end();
        getGrupoSubgrupoProductos('PRODUCTO',grupo,subgrupoBusqueda,false);
    }        
}

function stopConnectedList(ui)
{
    var idLi       = ui.item.attr("id");
    var esSelected = ui.item.attr("es-selected");
    var aTag       = '';
    
    if(typeof esSelected === 'undefined' || esSelected === 'N')
    {
        esEdicionProducto = false;
        aTag += '<a onclick="configurarProductoNuevo(this,'+ esEdicionProducto +');"><i class="fa fa-cog fa-2x ui-config" aria-hidden="true"></i></a>'+
                '<a onclick="eliminarProducto(this);"><i class="fa fa-close fa-2x ui-delete" aria-hidden="true"></i></a>';
    }

    $(ui.item).append(aTag);

    if($("#" + idLi).parent().get(0))
    {
        var parentNode = $("#" + idLi).parent().get(0).id;

        if(parentNode === 'listaProductos')
        {
            $(ui.item).find("a").remove();
            $(ui.item).find("i").remove();
            $(ui.item).attr("es-selected","N");

            //Se elimina del array la informacion que ya no es requerida
            eliminarProductoCatalogo($(ui.item),idLi);
        }
    }
}

function modalLoadingMessage() 
{
    var modal       = $( ".modal-message" );
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

function modalInfoMessage()
{
    var modal  = $( "#modal-infoError-message" );  
    modal.find("label").remove();
    $(".ui-dialog-titlebar").show();
    
    this.show = function(message)
    {        
        modal.append("<label>"+message+"</label>");
        modal.dialog("open");
    };
    
    
    this.hide = function ()
    {
        modal.dialog("close");
    };
}

function getGrupoSubgrupoProductos(tipo,grupo,subgrupo, showLoading=true)
{   
    var intIndexPropuesta;
    var intIdPropuesta;
    var objPropuesta = document.getElementById("objSelectPropuestaBS");
    if(objPropuesta != null && objPropuesta != undefined)
    {
        intIndexPropuesta = document.getElementById("objSelectPropuestaBS").selectedIndex;
        if(intIndexPropuesta > 0 && intIndexPropuesta != undefined && intIndexPropuesta != null)
        {
            intIdPropuesta = document.getElementById("objSelectPropuestaBS").options[intIndexPropuesta].value;
        }
    }
    $.ajax({
        type   : "POST",
        url    : urlGetGrupoSubgrupo,
        data   : 
        {
          'tipo'  : tipo,
          'grupo' : grupo,
          'subgrupo' : subgrupo,
          'intIdPropuesta':intIdPropuesta
        },
        beforeSend: function() 
        {       
            if(showLoading)
            {
                (new modalLoadingMessage()).show("Cargando Información de "+tipo);           
            }             
        },
        complete: function() 
        {                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
            if(showLoading)
            {
                (new modalLoadingMessage()).hide();
            }            
        },
        success: function(data)
        {
            if(tipo === 'GRUPO')
            {
                var option = '';
                $.each(data.arrayRespuestaGenerica, function(i, item) {
                    option += '<option value="'+item.grupo+'">'+item.grupo+'</option>';
                });
                $("#grupo_bs").append(option);
            }
            else if(tipo === 'SUBGRUPO')
            {
                $('#subgrupo_bs').find('option').remove().end();
                $('#subgrupo_bs').selectmenu('destroy').selectmenu({ style: 'dropdown' });
                var option = '<option value="Seleccione Subgrupo" selected>Seleccione Subgrupo</option>';                
                $.each(data.arrayRespuestaGenerica, function(i, item) {
                    option += '<option value="'+item.subgrupo+'">'+item.subgrupo+'</option>';
                });                
                $("#subgrupo_bs").append(option);
                $('#subgrupo_bs').selectmenu({
                    change:function(event,ui)
                    {
                        obtenerProductos();
                    }
                }).selectmenu( "menuWidget" ).addClass( "overflow" );
                $("#content-select-producto").show();   
                
                $( ".fd-tipo-subsolucion").hide();
                
                //Verificar si el grupo tiene configuraciones de tipo de sub solucion para renderizar y el usuario escoja
                if(data.arraySubSolucion.length > 0)
                {
                    boolEsMultiAgrupacion = true;
                    
                    $("#lbl-titulo-solucion").text("Solución escogida por agrupación");
                    $('#fieldSet-subtipos').find('label').remove().end();
                    $( ".fd-tipo-subsolucion").show();
                    
                    $.each(data.arraySubSolucion, function(i, item) 
                    {
                        var chkBox  = '<label for="checkbox-nested-'+i+'">'+item.subSolucion+
                                        '<input class="chk-subtipos" data-value="'+item.tipo+'" \n\
                                                type="checkbox" name="checkbox-nested-'+i+'" \n\
                                                id="checkbox-nested-'+i+'">'+
                                      '</label>';
                              
                        $("#fieldSet-subtipos").append(chkBox);
                        $("#checkbox-nested-"+i).checkboxradio();
                        $("#checkbox-nested-"+i).bind("change",function()
                        {
                            var idUlConnected   = 'connected-list-'+i;
                            var subTipoSolucion = $(this).parent().text();
                            var tipo            = $(this).attr('data-value');
                            
                            if($(this).is(':checked'))
                            {
                                //Crear una connected list dinamica
                                var subTipoSolucion = $(this).parent().text();                                
                                var clazz           = 'connectedSortable';
                                
                                var ul = '<ul id="'+idUlConnected+'" data-type="'+$.trim(subTipoSolucion)+'"\n\
                                              data-value="'+tipo+'" class="'+clazz+' connectedSortablePreferencial">\n\
                                              <li id="li-title-subsolucion" class="ui-state-disabled">'+subTipoSolucion+'</li>\n\
                                          </ul>';
                                
                                $("#right-productos").append(ul+"<br id='cls-br-"+idUlConnected+"'/>");
                                
                                $( "#listaProductos, #"+idUlConnected ).sortable({
                                    connectWith: ".connectedSortable",
                                    cursor: "move",
                                    beforeStop: function( event, ui ) 
                                    {  
                                        beforeStopConnectedList(ui);
                                    },
                                    stop: function( event, ui ) 
                                    {                           
                                        stopConnectedList(ui);
                                    }
                                }).disableSelection();

                                //Mantener estatica la LI definida como identificador-titulo del sub-bloque
                                $( "#"+idUlConnected ).sortable({
                                    items: "li:not(.ui-state-disabled)"
                                });

                                arraySubtiposSeleccionados.push($.trim(subTipoSolucion));
                            }
                            else
                            {
                                 //Eliminar servicios del array configurado
                                $("#"+idUlConnected).find("li").each(function()
                                {
                                    var ui   = $(this);
                                    var idLi = ui.attr("id");
                                    
                                    if(idLi !== 'li-title-subsolucion')
                                    {
                                        //Se elimina del array la informacion que ya no es requerida
                                        eliminarProductoCatalogo($(ui),idLi);
                                    }
                                });
                                $("#"+idUlConnected).remove();
                                $("#cls-br-"+idUlConnected).remove();
                                
                                //Eliminar del array de subtipos seleccionados
                                arraySubtiposSeleccionados = arraySubtiposSeleccionados.filter(function(elem){        
                                    return elem !== $.trim(subTipoSolucion); 
                                });
                            }
                        });
                    });

                    //Ocultar el panel de productos solucion generico
                    $("#listaProductosEscogidos").hide();
                }
                else
                {
                    boolEsMultiAgrupacion = false;
                    
                    //titulo solucion
                    $("#lbl-titulo-solucion").text("Solución Escogida");
                    
                    $("#listaProductosEscogidos").show();
                    //configuracion normal de lista conectada para grupo generales
                    //Inicializacion de listas conectadas
                    $( "#listaProductos, #listaProductosEscogidos" ).sortable({
                        connectWith: ".connectedSortable",
                        cursor: "move",
                        beforeStop: function( event, ui ) 
                        { 
                             beforeStopConnectedList(ui);
                        },
                        stop: function( event, ui ) 
                        {                           
                            stopConnectedList(ui);
                        }
                    }).disableSelection();
                }
            }
            else//PRODUCTO
            {                    
                //setear combo con opcion seleccionar subgrupo para poder realizar una consulta adicional al mismo valor
                //de subgrupo
                $('#subgrupo_bs').val('NA');
                $("#subgrupo_bs").selectmenu("refresh");
                                
                var li = '';
                $.each(data.arrayRespuestaGenerica, function(i, item) 
                {                    
                    li += '<li id="'+item.idProducto+'" data-value="'+item.tipo+'" class="ui-state-default">'+item.descripcionProducto+
                          '</li>';
                }); 
                //Lista de productos a ser escogidos
                $('#listaProductos').append(li);
                
                //Cargar informacion de productos referenciales
                if(data.arrayProductosReferencia.length > 0)
                {
                    contienePreferencial = true;
                }
            }
        }
    });
}

function eliminarProducto(el)
{
    var ui = $(el).parent();
    
    var idLi       = ui.attr("id");
    $(ui).find("a").remove();
    $(ui).find("i").remove();
    $(ui).attr("es-selected","N");
    $(ui).remove();
    //Se elimina del array la informacion que ya no es requerida
    eliminarProductoCatalogo($(ui),idLi);
}

function obtenerProductos()
{
    var subgrupo = $("#subgrupo_bs").val();
    var grupo    = $("#grupo_bs").val();

    if(grupo == 'Seleccione Grupo')
    {
        (new modalInfoMessage()).show("Debe al menos escoge un grupo para la Consulta");
        return;
    }
    if(subgrupo === 'Seleccione Subgrupo')
    {
        (new modalInfoMessage()).show("Debe al menos escoge un Subgrupo para la Consulta");
        return;
    }

    $('#listaProductos').find('li').remove().end();        
    getGrupoSubgrupoProductos('PRODUCTO',grupo,subgrupo);

    //Mostrar Nombre de la Solucion
    var nombrePropuestoSol = grupo+"-"+subgrupo;
    $("#input-nombre-solucion").val(nombrePropuestoSol);
    
    subgrupoBusqueda = subgrupo;
}

function validarFormulario(winCrearMV, esEdicionProducto, secuencialProducto, esCore)
{     
    var ultimaMilla           = document.getElementById("ultimaMillaIdProd").value;
    var frecuenciaFacturacion = $("#frecuencia-bs").val();
    
    //Validar personal actuante que sea elegido
    for(var i = 0; i < plantillas.length; i++)
    {        
        var objComboValidar  = $("#cmb"+plantillas[i]);

        if( !Ext.isEmpty(objComboValidar) )
        {
            var valorSeleccionado  = parseInt(objComboValidar.val());
            
            if( valorSeleccionado <= 0 )
            {                
                Ext.Msg.alert("Error", "No ha llenado todos los campos requeridos por la plantilla de comisionistas.");
                return false;
            }
        }
        else
        {
            Ext.Msg.alert("Error", "No se ha encontrado item requerido de la plantilla de comisionista.");
            return false;
        }        
    }
        
    //------------------------------------------
    
    var boolValidarUMProd = true;
       
    if(document.getElementById("strRequiereUltimaMillaProducto"))
    {
        var strRequiereUMProd = document.getElementById("strRequiereUltimaMillaProducto").value;
        if(strRequiereUMProd === "NO")
        {
            boolValidarUMProd = false;
        }
    }

    if ( (parseInt(ultimaMilla) <= 0 || isNaN(ultimaMilla) ) && boolValidarUMProd )
    {
        Ext.Msg.alert("Error", "Ingrese la Última Milla");
        return false;
    }
    
    if(frecuenciaFacturacion === '')
    {
        Ext.Msg.alert("Error", "Ingrese la frecuencia de Facturación");
        return false;
    }
    
    var precio_venta                = Number(document.getElementById("precio_venta").value);
    var precio_instalacion          = Number(document.getElementById("precio_instalacion").value); 
    var precio_formula              = Number(document.getElementById('precio_unitario').value);
    var precio_instalacion_formula  = Number(document.getElementById("precio_instalacionf").value);
    var cantidad                    = Number(document.getElementById("cantidad").value);
    var descripcion_producto        = document.getElementById("descripcion_producto").value;
    var estadoInicial               = document.getElementById("estadoInicial").value;
    
    //Validar que si es pool de recursos completos tenga al  menos un registro de cada Caracteristica existente
    if(esMultiCaracteristica)
    {
        var gridRecursos      = Ext.getCmp('gridRecursos');
        var cantidadRegistros = gridRecursos.getStore().getCount();
        
        if(cantidadRegistros===0)        
        {
            Ext.Msg.alert("Error", "Por favor escoja al menos una característica para configurar el Producto");
            return false;
        }
    }
    else
    {
        var caracteristicas_n           = "";
        var caracteristica_nombre_n     = "";
        var valor_caract                = new Array();
        var nombre_caract               = new Array();        
        var cantidad_caracteristicas    = document.getElementById("cantidad_caracteristicas").value;
        var caracteristicas             = "caracteristicas_";
        var caracteristica_nombre       = "caracteristica_nombre_";     
        
        for (var x = 0; x < cantidad_caracteristicas; x++)
        {
            id_caracteristica_n       = "caracteristicas_" + x;
            caracteristicas_n         = caracteristicas + x;
            caracteristica_nombre_n   = caracteristica_nombre + x;
            valor_caract[x]           = eval(caracteristicas_n).value;
            nombre_caract[x]          = eval(caracteristica_nombre_n).value;             

            if (document.getElementById(id_caracteristica_n).value == '')
            {
                Ext.Msg.alert('Error', "Ingrese valor de " + nombre_caract[x]);
                return false;
            }               
        }
    }

    if (isNaN(cantidad) || cantidad <= 0)
    {
        Ext.Msg.alert('Error', "Cantidad debe ser un número mayor que cero");
        return false;
    }
    else if (isNaN(precio_venta) || precio_venta <= 0)
    {
        Ext.Msg.alert('Error', "Ingrese Precio de Negociación,debe ser un valor numérico. Favor Verificar");
        return false;
    }
    else if (precio_instalacion_formula != null && precio_instalacion_formula != 0)
    {
        if (isNaN(precio_instalacion) || precio_instalacion < 0)
        {
            Ext.Msg.alert('Error', "Ingrese Precio de Instalación con valor numérico. Favor Verificar");
            return false;
        }
    }
    else if (descripcion_producto == '' || descripcion_producto == null)
    {
        Ext.Msg.alert('Error', "Ingrese descripción de producto.");
        return false;
    }

    //Mensaje informativos.
    if (estadoInicial=='Activo' && typeof cliente != 'undefined' )
    {
        if(rol=='Cliente')
        {
            Ext.Msg.alert("Error","Producto no requiere flujo. Se realiza Activación automática.");
        }
        else
        {
            Ext.Msg.alert("Error","Producto no requiere flujo. Se realizará Activación automática en la Aprobación del Contrato");
        }
    }

    if (precio_venta < precio_formula)
    {
         Ext.Msg.alert("Error","Precio de Negociación es menor al precio fórmula. Se generará solicitud de descuento");
    }

    if (precio_instalacion < precio_instalacion_formula)
    {
        Ext.Msg.alert("Error","Precio de Instalación es menor a precio de instalación sugerido. Se generará solicitud de instalación");
    }

    //Se genera el arreglo con la informacion a ser guardada posteriormente
    setArrayCatalogo(esEdicionProducto, secuencialProducto, esCore);
    return true;
}

function setArrayCatalogo(esEdicionProducto, secuencialProducto, esCore)
{
    var intIndexPropuesta;
    var intIndexCotizacion;
    var intIdPropuesta;
    var intIdCotizacion;
    var strPropuesta;
    var strCotizacion;
    var objPropuesta  = document.getElementById("objSelectPropuestaBS");
    var objCotizacion = document.getElementById("objSelectCotizacionBS");
    if(objPropuesta != null && objPropuesta != undefined)
    {
        intIndexPropuesta = document.getElementById("objSelectPropuestaBS").selectedIndex;
        if(intIndexPropuesta > 0 && intIndexPropuesta != undefined && intIndexPropuesta != null)
        {
            intIdPropuesta = document.getElementById("objSelectPropuestaBS").options[intIndexPropuesta].value;
            strPropuesta   = document.getElementById("objSelectPropuestaBS").options[intIndexPropuesta].text;
        }
    }
    if(objCotizacion != null && objCotizacion != undefined)
    {
        intIndexCotizacion = document.getElementById("objSelectCotizacionBS").selectedIndex;
        if(intIndexCotizacion >= 0 && intIndexCotizacion != undefined && intIndexCotizacion != null)
        {
            intIdCotizacion = document.getElementById("objSelectCotizacionBS").options[intIndexCotizacion].value;
            strCotizacion   = document.getElementById("objSelectCotizacionBS").options[intIndexCotizacion].text;
        }
    }
    boolEsEditarSolucion = typeof boolEsEditarSolucion !== 'undefined' && boolEsEditarSolucion;
    nuevaSolucion        = typeof nuevaSolucion        !== 'undefined' && nuevaSolucion;

    var jsonCaracteristicas = {};
    
    //Cuando el esquema es muliticaracteristica se guarda la informacion de recursos bajo otro esquema
    if(!esMultiCaracteristica)
    {
        var cantidad_caracteristicas    = document.getElementById("cantidad_caracteristicas").value;
    
        var caracteristicas             = "caracteristicas_";
        var caracteristica_nombre       = "caracteristica_nombre_";
        var caracteristica_id           = "producto_caracteristica_";

        var arrayCaracteristicas = [];

        for (var x = 0; x < cantidad_caracteristicas; x++)
        {
            var id_caractertistica_value  = caracteristicas + x;         //caracteristicas_0
            var id_caracteristicas_descr  = caracteristica_nombre + x;   //caracteristica_nombre_0
            var id_caracteristicas_idPro  = caracteristica_id + x;       //producto_caracteristica_0

            var jsonData = {};

            jsonData['valor']            = $("#"+id_caractertistica_value).val();
            jsonData['descripcion']      = $("#"+id_caracteristicas_descr).val();
            jsonData['idCaracteristica'] = $("#"+id_caracteristicas_idPro).val();

            arrayCaracteristicas.push(jsonData);
        }

        var jsonCaracteristicas = Ext.JSON.encode(arrayCaracteristicas);
    }
    
    var nombre_vendedor          = '';
    var strPlantillaComisionista = '';
    //Informacion de comisionistas
    for(var i = 0; i < plantillas.length; i++)
    {        
        var objInputVendedor = $("#inputVendedor"+plantillas[i]);
        var objComboValidar  = $("#cmb"+plantillas[i]);

        if( !Ext.isEmpty(objComboValidar) )
        {            
            var valorSeleccionado  = parseInt(objComboValidar.val());
            var valorInputVendedor = "N";

            if( typeof objComboValidar.attr('required') !== "undefined" )
            {                
                if( valorSeleccionado > 0 )
                {
                    if( !Ext.isEmpty(objInputVendedor) )
                    {                        
                        valorInputVendedor = objInputVendedor.val();

                        if( valorInputVendedor === "S" )
                        {
                            nombre_vendedor = $("#cmb" + plantillas[i] + " option:selected").text();                          
                        }
                    }
                }           
            }
            else
            {                
                if( !Ext.isEmpty(objInputVendedor) )
                {
                    valorInputVendedor = objInputVendedor.val();

                    if( valorInputVendedor === "S" )
                    {
                        nombre_vendedor = $("#cmb" + plantillas[i] + " option:selected").text();
                    }
                }
            }

            if( !Ext.isEmpty(strPlantillaComisionista) )
            {
                strPlantillaComisionista = strPlantillaComisionista + '|';
            }
            
            strPlantillaComisionista = strPlantillaComisionista + plantillas[i] + '---' + valorSeleccionado;
        }                
    }
    
    //Informacion general del producto
    var jsonInfoGeneral = {};
    
    //Informacion del producto configurado
    if(uiToConfigurate !== null)
    {
        var nombre  = $.trim(uiToConfigurate.text());
        var tipo    = uiToConfigurate.attr("data-value");
        var subtipo = uiToConfigurate.parent().attr("data-type");
    }
    else
    {
        var nombre  = nombreProducto;
        var tipo    = tipoProductoConfigurado;
        var subtipo = tipoSubSolucion;
    }

    if (esEdicionProducto)
    {
        secuencial    = secuencialProducto;
        arraySolucion = arraySolucion.filter(function(elem)
        {
            return elem.secuencial != secuencialProducto;
        });

        arrayMaquinasVirtuales = arrayMaquinasVirtuales.filter(function(elem)
        {
            return elem.secuencial != secuencialProducto;
        });
    }

    //Si es editar solución y la acción es editar, obtenemos los recursos Housing
    //en base al store.
    if (boolEsEditarSolucion && accion == 'editar' && esMultiCaracteristica && boolEsLicencia)
    {
        var arrayRecursos        = []
        var recursosStoreHosting = storeRecursosCaracteristicas.snapshot ||
                                   storeRecursosCaracteristicas.data;

        recursosStoreHosting.each(function(record) {
            var json = {};
            json['idRaw']          = record.data.idRaw;
            json['tipoRecurso']    = record.data.tipoRecurso;
            json['caracteristica'] = record.data.caracteristica;
            json['cantidad']       = record.data.cantidad;
            json['descuento']      = record.data.descuento;
            json['idServicio']     = record.data.idServicio;
            json['esAntiguo']      = record.data.esAntiguo;
            json['idMaquinas']     = [record.data.maquinaVirtual ? record.data.maquinaVirtual : null];
            json['idLicencias']    = [record.data.idRaw];
            arrayRecursos.push(json);
        });

        if (arrayRecursos.length > 0) {
            arrayRecursosHosting = arrayRecursos;
        }
    }

    //Json con la información general de la solución.
    jsonInfoGeneral['secuencial']                  = secuencial;
    jsonInfoGeneral['esCore']                      = esCore;
    jsonInfoGeneral['cantidad']                    = $("#cantidad").val();
    jsonInfoGeneral['caracteristicasProducto']     = jsonCaracteristicas;
    jsonInfoGeneral['codigo']                      = $("#hd_id_producto").val();
    jsonInfoGeneral['nombre']                      = nombre;
    jsonInfoGeneral['descripcion']                 = $("#descripcion_producto").val();
    jsonInfoGeneral['producto']                    = $("#descripcion_producto").val();
    jsonInfoGeneral['frecuencia']                  = $("#frecuencia-bs").val();
    jsonInfoGeneral['info']                        = 'C';
    jsonInfoGeneral['nombre_vendedor']             = nombre_vendedor;
    jsonInfoGeneral['precio_instalacion']          = Number($("#precio_instalacionf").val());
    jsonInfoGeneral['precio_instalacion_pactado']  = Number($("#precio_instalacion").val());
    jsonInfoGeneral['precio_total']                = Number($("#precio_total").val());
    jsonInfoGeneral['precio']                      = Number($("#precio_unitario").val());
    jsonInfoGeneral['precio_venta']                = Number($("#precio_venta").val());
    jsonInfoGeneral['punto']                       = idPunto;
    jsonInfoGeneral['servicio']                    = 0;
    jsonInfoGeneral['tipoOrden']                   = 'N';
    jsonInfoGeneral['ultimaMilla']                 = $("#ultimaMillaIdProd").val();
    jsonInfoGeneral['um_desc']                     = $("#ultimaMillaIdProd option:selected").text();
    jsonInfoGeneral['tipoProducto']                = tipo;//Nuevo parametro a enviar para creacion del Servicio
    jsonInfoGeneral['login_vendedor']              = $('#infopuntoextratype_loginVendedor').val();
    jsonInfoGeneral['strPlantillaComisionista']    = strPlantillaComisionista;
    jsonInfoGeneral['caracteristicasPoolRecursos'] = Ext.JSON.encode(arrayRecursosHosting);
    jsonInfoGeneral['tipoSubSolucion']             = subtipo;
    jsonInfoGeneral['tipoSubSolucionReferencial']  = "";
    jsonInfoGeneral['informacionPorEdicion']       = "";
    jsonInfoGeneral['intIdPropuesta']              = intIdPropuesta;
    jsonInfoGeneral['strPropuesta']                = strPropuesta;
    jsonInfoGeneral['cotizacion']                  = intIdCotizacion;
    jsonInfoGeneral['cot_desc']                    = strCotizacion;

    //Este proceso casusa duplicidad en la edición de solución.
    //por tales motivos no entrará en caso que estemos en la opción antes nombrada.
    var agregar = !(boolEsEditarSolucion && accion == 'editar');
    if(!Ext.isEmpty(arrayInformacion) && agregar) {
        var jsonMaquinasVirtuales                  = {};
        jsonMaquinasVirtuales['secuencial']        = secuencial;
        jsonMaquinasVirtuales['maquinasVirtuales'] = arrayInformacion; 
        if (accion == 'agregar') {
            jsonMaquinasVirtuales['esNuevo'] = true; 
        }
        arrayMaquinasVirtuales.push(jsonMaquinasVirtuales);
    }

    //Ingresamos los datos al array de solución.
    arraySolucion.push(jsonInfoGeneral);

    if (accion == 'editar') {
        ajaxEditarServicioSolucion();
    }

    if (uiToConfigurate !== null) {
        esEdicionProducto = true;
        uiToConfigurate.removeClass();
        uiToConfigurate.find("i[class='fa fa-check-square-o fa-2x ui-check']").remove();
        uiToConfigurate.addClass("item-config"); 
        uiToConfigurate.prepend("<i class='fa fa-check-square-o fa-2x ui-check' aria-hidden='true'></i>&nbsp;");
        uiToConfigurate.find( "a" ).remove();
        uiToConfigurate.attr("data-position",$("#hd_id_producto").val());
        uiToConfigurate.attr("secuencial", secuencial);
        uiToConfigurate.attr("es-selected",'S');
        uiToConfigurate.append('<a onclick="configurarProductoNuevo(this,true);"><i class="fa fa-edit fa-2x ui-config" aria-hidden="true"></i></a>');
    }
}


function eliminarProductoCatalogo(ele,idProducto)
{
    ele.removeClass();
    ele.addClass("ui-state-default");    
    ele.removeAttr("data-position");
    
    arraySolucion = arraySolucion.filter(function(elem){                        
        return parseInt(elem.secuencial) !== parseInt(ele.attr('secuencial')); 
    });
}

function esProductoExistente(idNuevo)
{
    var existe = false;
    var cont = 0;
    $("#listaProductosEscogidos").find("li").each(function(){
       var idExistente = $(this).attr("id");       
       cont++;
       if(idExistente == idNuevo)
       {             
           existe = true;
           return;
       }       
    });
    
    //Si no hay nada , mostrara todos los elementos consultados
    if(cont===0 || !existe)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function llenarGridRecursos(arrayCaracteristicasPoolRecursos, storeRecursosCaracteristicas, rowEditingRecursos, boolEsEdicionProducto)
{   
   if (arrayCaracteristicasPoolRecursos.length > 0)
    {
        $.each(arrayCaracteristicasPoolRecursos, function (index, recurso){
            rowEditingRecursos.cancelEdit();
            
            jsonPreciosMulticaracteristicas = calcularPrecioMultiCaracteristica(recurso.tipoRecurso, 
                                                                              recurso.caracteristica, 
                                                                              recurso.cantidad,
                                                                              recurso.precioNegociado,
                                                                              boolEsEdicionProducto
                                                                             );  
                      
            if(boolEsLicencia)
            {
                var recordParamDetLic = Ext.create('recursosModel', {
                    idRaw          : recurso.idRaw,
                    maquinaVirtual : recurso.maquinaVirtual,
                    tipoRecurso    : recurso.tipoRecurso,
                    caracteristica : recurso.caracteristica,
                    cantidad       : recurso.cantidad,
                    precioUnitario : jsonPreciosMulticaracteristicas['precioUnitario'],
                    precioNegociado: jsonPreciosMulticaracteristicas['precioNegociado'],
                    descuento      : jsonPreciosMulticaracteristicas['descuento'],
                    precioTotal    : jsonPreciosMulticaracteristicas['precioTotal'],
                    hdvalor        : jsonPreciosMulticaracteristicas['hdValor'],
                    hdPorcentaje   : jsonPreciosMulticaracteristicas['hdPorcentaje']
                  
            });            
            
            storeRecursosCaracteristicas.insert(0, recordParamDetLic);
            }else
            {
                var recordParamDet = Ext.create('recursosModel', {                    
                    idRaw          : recurso.idRaw,
                    tipoRecurso    : recurso.tipoRecurso,
                    caracteristica : recurso.caracteristica,
                    cantidad       : recurso.cantidad,
                    precioUnitario : jsonPreciosMulticaracteristicas['precioUnitario'],
                    precioNegociado: jsonPreciosMulticaracteristicas['precioNegociado'],
                    descuento      : jsonPreciosMulticaracteristicas['descuento'],
                    precioTotal    : jsonPreciosMulticaracteristicas['precioTotal'],
                    hdvalor        : jsonPreciosMulticaracteristicas['hdValor'],
                    hdPorcentaje   : jsonPreciosMulticaracteristicas['hdPorcentaje']
                  
            });            
            
            storeRecursosCaracteristicas.insert(0, recordParamDet);
            }
            
            if(boolEsPoolRecursosCompleto)
            {
                if(recurso.tipoRecurso == 'DISCO')
                { 
                    storageTotal = parseInt(recurso.cantidad, 10)  + storageTotal;
                }
                else if(recurso.tipoRecurso == 'PROCESADOR')
                {       
                    procesadorTotal = parseInt(recurso.cantidad, 10)  + procesadorTotal;
                }
                else
                {
                    memoriaTotal = parseInt(recurso.cantidad, 10)  + memoriaTotal;
                }
            }
        });        
        

   }    
}

function getDetalleProducto(id)
{
    var html = "<div class = 'content-detalle-producto'><table width='100%'><tbody>";
    
    $.each(arraySolucion, function(k,item){
        if(item.codigo == id)
        {
            html += "<tr><td><label><b>C&oacute;digo</b></td><td><label>"+id+"</label></td>";
            html += "    <td><label><b>Producto</b></td><td><label>"+item.descripcion+"</label></td></tr>";
            html += "<tr><td><label><b>Ultima Milla</b></td><td><label>"+item.um_desc+"</label></td>";
            html += "    <td><label><b>Cantidad</b></td><td><label>"+item.cantidad+"</label></td></tr>";
            html += "<tr><td><label><b>Frecuencia</b></td><td><label>"+item.frecuencia+"</label></td>";
            html += "    <td><label><b>Precio Unit.</b></td><td><label>\n\
                         <i class='fa fa-usd' aria-hidden='true'></i>&nbsp;"+item.precio+"</label></td></tr>";
            html += "<tr><td><label><b>Precio Total</b></td><td><label>\n\
                         <i class='fa fa-usd' aria-hidden='true'></i>&nbsp;"+item.precio_venta+"</label></td>";
            html += "    <td><label><b>Precio Instalaci&oacute;n</b></td><td><label>\n\
                         <i class='fa fa-usd' aria-hidden='true'></i>&nbsp;"+item.precio_instalacion+"</label></td></tr>";
            html += "<tr><td><label><b>Precio Instalaci&oacute;n Negociado</b></td><td><label>\n\
                         <i class='fa fa-usd' aria-hidden='true'></i>&nbsp;"+item.precio_instalacion_pactado+"</label></td>";
            html += "    <td><label><b>Precio Venta Negociado</b></td><td><label>\n\
                         <i class='fa fa-usd' aria-hidden='true'></i>&nbsp;"+item.precio_total+"</label></td></tr>";
            html += "<tr><td><label><b>Vendedor</b></td><td colspan='2'><label>"+item.nombre_vendedor+"</label></td></tr>";
        }
    });
    
    html+="</tbody></table></div>";
    
    return html;
}

function mostraDetalleSolucion()
{
    var boolTienePreferencialSolucion = false;
    var numProductosSolucion          = 0;
    var boolTieneProductoNormal       = false;
    
    if(Ext.isEmpty(arraySolucion))
    {
        (new modalInfoMessage()).show('Debe ingresar y configurar los productos de la Solución para poder Guardar');
        return false;
    }
        
    $.each(arraySolucion, function(k,item)
    {
        //Si el producto es Preferencial
       if(item.tipoProducto === 'P') 
       {
           boolTienePreferencialSolucion = true;
       }
       else
       {
           boolTieneProductoNormal = true;
       }
    });
       
    if(!boolTienePreferencialSolucion || !boolTieneProductoNormal)
    {
        (new modalInfoMessage()).show('La Solución debe tener productos Preferenciales y Cores para poder Guardar');
        return false;
    }
       
    var contSeleccionados = 0;
    
    if(boolEsMultiAgrupacion)
    {
        $("#right-productos").find("li").each(function()
        {
            var ui = $(this);
            
            var id = ui.attr("id");
            
            if (id!=='li-title-subsolucion')
            {
                contSeleccionados++;
            }
        });
        
        //Verifcar si todos los productos seleccionados se encuentra configurados
        $("#right-productos").find("li").each(function()
        {
            var ui = $(this);            
            var id = ui.attr("id");
            
            if (id !== 'li-title-subsolucion')
            {
                //contar la cantidad de servicios que han sido configurados
                if($(this).attr("es-selected") === 'S')
                {
                    numProductosSolucion++;
                }
            }
        });
    }
    else
    {
        $("#listaProductosEscogidos").find("li").each(function()
        {
            contSeleccionados++;
        });
        
        //Verifcar si todos los productos seleccionados se encuentra configurados
        $("#listaProductosEscogidos").find("li").each(function()
        {
            //contar la cantidad de servicios que han sido configurados
            if($(this).attr("es-selected") === 'S')
            {
                numProductosSolucion++;
            }
        });
    }           
   
    if(numProductosSolucion !== contSeleccionados)
    {
        (new modalInfoMessage()).show("El usuario debe completar el ingreso de las características de los productos para poder\n\
                                        continuar con el flujo de activación");        
        return false;
    }
    
    //Si el Subgrupo esta configurado con producto preferencial se valida que contenga al menos uno en su solucion
    if(contienePreferencial)
    {
        if(!boolTienePreferencialSolucion)
        {
            (new modalInfoMessage()).show("Por favor Configure un Producto <b>Preferencial</b> para crear su Solución");
            return false;
        }
        
        if(!boolTieneProductoNormal)
        {
            (new modalInfoMessage()).show("Por favor Configure al menos un Producto <b>No Referencial</b> para crear su Solución");
            return false;
        }
    }
    
    //Para esquemas con preferencial ( escenarios DATACENTER )
    //Se valida que si existe mas de un core o un preferencial el usuario pueda relacionar un core a un preferencial para
    //poder continuar con el flujo
    if(contienePreferencial && boolEsMultiAgrupacion)
    {
        //mostrar pantalla para realizar gestión
        var arrayPreferentes= [];

        //obtener array de los servicios marcados como comunicaciones
        $.each(arraySolucion, function(i, item) 
        {
            if(item['tipoProducto'] === 'P')
            {
                arrayPreferentes.push(item);
            }
        });

        $("#content-relacion-subtiposolucion").html("");

        //Mostrar ventana de seleccion de relacion entre preferencial y subtipo de solucion
        var htmlTbl = "<table id='tbl-relaciones' class='ui-widget ui-widget-content'><thead><tr class='ui-widget-header'><th></th>";

        $.each(arraySubtiposSeleccionados,function(i, item)
        {            
            if(validarReferenciaRelacionNoPreferencial(item))
            {
                htmlTbl += "<th>"+item+"</th>";
            }            
        });
        htmlTbl += "</tr></thead><tbody>";

        $.each(arrayPreferentes,function(i, item)
        {
            htmlTbl += "<tr><td id='"+item['secuencial']+"'>"+item['nombre']+"</td>";

            $.each(arraySubtiposSeleccionados,function(i, item)
            {
                if(validarReferenciaRelacionNoPreferencial(item))
                {
                    htmlTbl += "<td align='center'><input type='checkbox' name='"+item+"'/></td>";
                }                
            });

            htmlTbl+= "</tr>";
        });
        htmlTbl += "</tbody></table>";

        $("#content-relacion-subtiposolucion").append(htmlTbl);

        $("#content-relacion-subtiposolucion").dialog("open");
    }
    else
    {
        showResumen();
    }              
}

function validarReferenciaRelacionNoPreferencial(item)
{
    //Validar que el segmento de agrupacion a mostrar en la relacion no sea Preferencial
    var isValid = true;
    $("#right-productos").find("ul").each(function()
    {
        var ui = $(this);

        var nombreUl = ui.attr("data-type");
        var tipoUl   = ui.attr("data-value");

        if (nombreUl===item && tipoUl === 'P')
        {
            isValid = false;
            return false;
        }
    });
    
    return isValid;
}

function configurarRelaciones()
{
    var arrayRefChecked         = [];
    var arrayIdsRefConfigurados = [];    
    
    //Guardar los id referentes configurados
    $("#tbl-relaciones").find("tr").each(function()
    {
        $(this).find("td").each(function()
        {
            var id = $(this).attr("id");
            
            if(!Ext.isEmpty(id))
            {
                arrayIdsRefConfigurados.push(id);
            }
        });
    });
    
    //limpiar subgrupo solucion configurado por cada iteracion realizada
    $.each(arraySolucion, function(i, item)
    {        
        arraySolucion[i]['tipoSubSolucionReferencial'] = "";
    });
    
    $("#tbl-relaciones").find("input[type=checkbox]").each(function()
    {
        if($(this).is(':checked'))
        {
            var idRef = $(this).parent().parent().children().first().attr("id");
            var tipo  = $(this).attr("name");
            arrayRefChecked.push(idRef);
            
            $.each(arraySolucion, function(i, item)
            {
                if(parseInt(item['secuencial']) === parseInt(idRef))
                {
                    arraySolucion[i]['tipoSubSolucionReferencial'] = arraySolucion[i]['tipoSubSolucionReferencial'] + tipo + "|";
                }
            });
        }
    });
        
    //Verificar si los preferenciales estan configurados con al  menos una subcolucion normal
    var cantServiciosPreferenciales = arrayIdsRefConfigurados.length;
    var contRepetidos               = 0;
    
    $.each(arrayIdsRefConfigurados, function(i, item){
        $.each(arrayRefChecked, function (j, itemChk){
           if(item === itemChk)
           {
               contRepetidos++;
               return false;
           }
        });
    });
    
    if(cantServiciosPreferenciales !== contRepetidos)
    {
        Ext.alert("Por favor Configure al menos un Producto <b>No Referencial</b> para crear su Solución");
        return false;
    }
    else
    {
        showResumen();
    }
}

function showResumen()
{
    var nombreSolucion = $("#input-nombre-solucion").val();
    
    //Mostrar resumen de productos
    var html = '<div class="secHead hr"><i class="fa fa-cubes fa-1x" aria-hidden="true"></i>&nbsp;<b>Solución : </b>&nbsp;'+nombreSolucion+'</div>';

    html+="<br/>Los Productos para crear su solución son los Siguientes : <br/><br/>";
    
    html+="<ol id='resumen-list'>";
    
    $.each(arraySolucion, function(k,item){
       html+='<li><i class="fa fa-angle-double-right" aria-hidden="true" style="color:#1c94c4;"></i>&nbsp;'+item.descripcion+'</li>';
    });
    
    html+="</ol><br/>";
    
    $("#content-resumen").html(html);
    
    //Se agrega iconos a los botones del modal panel
    $("#button-confirmar").html('<i class="fa fa-check" aria-hidden="true"></i>&nbsp;Confirmar');
    $("#button-cerrar-resumen").html('<i class="fa fa-times" aria-hidden="true"></i>&nbsp;Cerrar');
    
    $("#content-resumen").dialog("open"); 
}

function showSolucionCreada(numeroSolucion)
{
    var html           = '';
    var nombreSolucion = $("#input-nombre-solucion").val();

    html+='<div class="secHead hr"><i class="fa fa-cubes fa-1x" aria-hidden="true"></i>&nbsp;'
            +'<b>Solución: </b>&nbsp;'+nombreSolucion+'</div>';

    html+="<br/>Los servicios se crearon con el siguiente número de solución:<br/>";
    html+="<ol id='resumen-solucion'>";
    html+='<li><i class="fa fa-angle-double-right" aria-hidden="true" style="color:#1c94c4;"></i>&nbsp;'
            +'<b style="color:green;">'+numeroSolucion+'</b>'+
          '</li>';
    html+="</ol>";

    html+="Servicios creados:<br/>";
    html+="<ol id='resumen-servicios'>";
    $.each(arraySolucion, function(k,item){
        html+='<li><i class="fa fa-angle-double-right" aria-hidden="true" style="color:#1c94c4;"></i>&nbsp;'
                +item.descripcion+
              '</li>';
    });
    html+="</ol>";

    $("#button-confirmar-solucion").html('<i class="fa fa-share" aria-hidden="true"></i>&nbsp; Ir a los Servicios');
    $("#content-solucion-creada").html(html);
    $("#content-solucion-creada").dialog("open");
}

function guardarSolucion()
{
    $.ajax({
        type    : "POST",
        url     : urlGuardarSolucion,
        timeout : 999999999,
        data    :
        {
          'data'               : Ext.JSON.encode(arraySolucion),
          'tipoSolucion'       : 'S',
          'nombreSolucion'     : $("#input-nombre-solucion").val(),
          'idPunto'            : idPunto,
          'tipoOrden'          : 'N',
          'numeroSolucion'     : '',
          'maquinasVirtuales'  : Ext.JSON.encode(arrayMaquinasVirtuales)
        },
        beforeSend: function() 
        {
            (new modalLoadingMessage()).show("Guardando Información de Servicios");
        },
        complete: function() 
        {
            (new modalLoadingMessage()).hide();
        },
        success: function(data)
        {
            if(data.status !== 'OK') {
                if (data.boolErrorWs) {
                    $("#modal-infoError-message").dialog({title:"Error al crear la solución"});
                }
                (new modalInfoMessage()).show(data.mensaje);
                $("#modal-infoError-message").dialog({title:"Alerta"});
                return;
            }

            $("#content-resumen").dialog("close");
            $("#content-relacion-subtiposolucion").dialog("close");
            showSolucionCreada(data.numeroSolucion);
        }
    });
}

function soloNumeros(e)
{
    var keynum = window.event ? window.event.keyCode : e.which;
    if ((keynum == 8) || (keynum == 46))
    return true;

    return /\d/.test(String.fromCharCode(keynum));
}

function configurarProductoNuevo(el, esEdicionProducto) {

    var parent         = $(el).parent().get(0);
    var idParent       = parent.id;   //id del producto
    var nombre         = $(el).parent().text().trim();
    storageTotal       = 0;
    memoriaTotal       = 0;
    procesadorTotal    = 0;
    storageUsado       = 0;
    memoriaUsado       = 0;
    procesadorUsado    = 0;
    uiToConfigurate    = $(el).parent();
    tipoProducto       = uiToConfigurate.attr("data-value");
    secuencialProducto = uiToConfigurate.attr('secuencial');
    arrayInformacion   = [];

    $("#button-configurar").attr('disabled', true);
    $.ajax({
        type   : "POST",
        url    : urlGetCaracteristicas,
        data   : 
        {
          'idPunto'            : idPunto,
          'producto'           : idParent,
          'verCaracteristicas' : true,
          'esGrupo'            : 'S',
          'esbusiness'         : true
        },
        beforeSend: function() 
        {            
            (new modalLoadingMessage()).show("Cargando Información del Producto");                     
        },
        complete: function() 
        {
            (new modalLoadingMessage()).hide();     
        },
        success: function(data)
        {     
            esMultiCaracteristica = data.esMultiCaracteristica;
            
            var htmlFormulario = data.div; //Formulario de caracteristicas de cada producto            
                        
            jsonFrecuenciaFact = JSON.parse(frecuenciaItem);
            
            var cmbFrecuencia = '<select name="frecuencia-bs" id="frecuencia-bs"><option value="">Seleccione</option>';
            
            $.each(jsonFrecuenciaFact, function(k,item){
                cmbFrecuencia += "<option value='"+item.valor1+"'>"+item.valor2+"</option>";
            });
            cmbFrecuencia += "</select>";
            
            var trFrecuencia = "<div>"+cmbFrecuencia+"</div>";
            var trProducto   = "<input id='hd_id_producto' type='hidden' value='"+idParent+"' />";
            var trProductName= "<input id='hd_nombre_producto' type='hidden' value='"+nombre+"' />";            
                        
            //Limpiar informacion anterior para reenderizar en limpio el panel
                       
            var divHeader     = "<div style= 'margin-top: -33px; padding-top: 0px;' class='container' id ='divHeader'></div>";
            var divPrincipal  = "<table id='tbl_htmlFormaulario'>"+htmlFormulario+"</table>";
            var divSecundario = "<div  id ='divSec'> </div>";            
            
            
            var contentHtmlProgressBar = Ext.create('Ext.Component', {
                html:   '<div style = "padding-left: 15px; padding-right: 10px;padding-top:15%;">'+
                            '<table style="width:100%;">'+
                                '<tr>'+
                                    '<td style="width:100%">'+
                                        '<div id="progressbar-storage" class="ui-progressbar" align="center">'+
                                            '<div id="progressbar-storage-label" class="progress-label">Storage</div>'+
                                        '</div>'+    
                                    '</td>' +
                                '</tr><tr><td>&nbsp;</td></tr>' +
                                '<tr>'+
                                    '<td style="width:100%">'+
                                        '<div id="progressbar-memoria" class="ui-progressbar"  align="center">'+
                                            '<div id="progressbar-memoria-label" class="progress-label">Storage</div>'+
                                        '</div>'+    
                                    '</td>' +
                                '</tr><tr><td>&nbsp;</td></tr>' +
                                '<tr>'+
                                    '<td style="width:100%">'+
                                        '<div id="progressbar-procesador" class="ui-progressbar" align="center">'+
                                            '<div id="progressbar-procesador-label" class="progress-label">Storage</div>'+
                                        '</div>'+    
                                    '</td>' +

                        '</tr>' +                            
                        '</div>'
            });                              

            var componentDivHeader = Ext.create('Ext.Component', {
                html: divHeader
            });
            
            var componentTrProducto = Ext.create('Ext.Component', {
                html: trProducto                
            });
            
            var componentTrProductName = Ext.create('Ext.Component', {
                html: trProductName               
            });
            
            var componentTrHtmlDivPrinci= Ext.create('Ext.Component', {                
                html: divPrincipal
            }); 
            
            var componentTrHtmlDivSecundario = Ext.create('Ext.Component', {                
                html: divSecundario
            });

            arrayRecursosHosting      = [];
            var gridRecursos          = null;            
            var boolEsPoolCompleto    = false;
            
            if (esMultiCaracteristica)
            {
                gridRecursos       = renderizarConfiguracionMultiCaractetistica(data);
                boolEsPoolCompleto = (data.esPoolCompleto === 'SI' && !data.esLicencia);
                
                gridMaquinasVirtuales = renderizarGridMaquinasVirtuales(data, esEdicionProducto);
            }                        
              
            var formCrearMV = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            id:'panelAgregarMv',
            BodyPadding: 10,
            width: 1000,
            autoScroll: true,
            height: 600,
            frame: true,
            items:
                [
                    {
                        xtype: 'fieldset',                        
                        title:'<b>Información General</b>',
                        layout: {                            
                            columns: 5,
                            type: 'table'
                        },
                        items :
                        [ 
                            componentDivHeader,                           
                            componentTrProducto,
                            componentTrProductName
                        ]
                    },
                    //Configuración de características múltiples
                    {
                        xtype: 'fieldset',
                        id:'fs_gridRecursos',
                        layout: 'fit',
                        collapsible: false,
                        items:
                            [
                                gridRecursos
                            ]
                    },
                    //Configuración de máquinas virtuales
                    {
                        xtype: 'fieldset',
                        id:'fs_gridMaquinasVirtuales',
                        collapsible: false,
                        layout: {
                            type: 'table',
                            columns: 3
                        },                        
                        items:
                            [
                                //Grid máquinas virtuales
                                {
                                    xtype: 'fieldset',                          
                                    height:300,
                                    title:'<b>Gestión de Máquinas Virtuales</b>',                       
                                    items:
                                        [
                                            gridMaquinasVirtuales                                           
                                        ]
                                },
                                Ext.create('Ext.Component', {                
                                    html: '&nbsp;'
                                }),
                                {
                                    xtype: 'fieldset',
                                    height:300,
                                    width:400,
                                    title:'<b>Resumen de Recursos configurados</b>',                
                                    items:
                                        [
                                            contentHtmlProgressBar                                           
                                        ]
                                }
                            ]
                    },
                    {
                        xtype:'fieldset',   
                        layout: 'fit',
                        title:'<b>Resumen de Precios</b>',
                        collapsible: false,                        
                        items :
                        [
                            componentTrHtmlDivPrinci
                        ]
                    },
                    {
                        xtype:'fieldset',   
                        layout: 'fit',
                        hidden: true,
                        collapsible: false,                        
                        items :
                        [
                            componentTrHtmlDivSecundario
                        ]
                    },
                    {
                        xtype:'hidden',                                
                        name: 'txtInfoRecursos',
                        id:   'txtInfoRecursos',
                        value:''
                    }                    
            ],
            buttons: [
                {
                    text: '<i class="fa fa-plus-square" aria-hidden="true"></i>&nbsp;'+(boolEsNuevo?(!esEdicionProducto?'Agregar':'Editar'):'Editar'),
                    id:'btnConfigurar',
                    handler: function() 
                    {
                        //Si no eres edición de producto, generamos el secuencial que es
                        //como referencia del servicio que se esta agregando.
                        if (!esEdicionProducto)
                        {
                            identificadorProducto = identificadorProducto + 1;
                            secuencial            = identificadorProducto;
                        }

                        if (data.esLicencia)
                        {
                            var maquinasMensaje       = '';
                            var arrayMaquinasInvolved = [];
                            var arrayMaquinasSinSO    = [];
                            arrayMaquinasInvolved     = searchMaquinasInvolved(gridRecursos.getStore().data.items);
                            arrayMaquinasSinSO        = verificarSOMaquina(arrayMaquinasInvolved);
                            deleteLicenciasToArrayInformacion(gridRecursos.getStore().data.items);

                            if (arrayMaquinasSinSO.length > 0)
                            {
                                $.each(arrayMaquinasSinSO, function(index, value){
                                    maquinasMensaje = maquinasMensaje  + value + ', ';
                                });

                                Ext.Msg.alert('Error', 'Las siguientes máquinas: '+ maquinasMensaje + 'están sin licencias');
                                return false;
                            }

                            addLicenciasToArrayInformacion(gridRecursos.getStore().data.items);
                        }

                        //Validar los datos.
                        var boolFormValido = validarFormulario(winCrearMV, esEdicionProducto, secuencialProducto);
                        esEdicionProducto  = false;

                        if (boolFormValido)
                        {
                            winCrearMV.close();
                            winCrearMV.destroy();
                        }
                        else
                        {
                            identificadorProducto = identificadorProducto - 1;
                            secuencial            = identificadorProducto;
                        }
                    }
                },
                {
                    text: '<i class="fa fa-window-close-o" aria-hidden="true"></i>&nbsp;Cerrar',
                    handler: function()
                    {                       
                        limpiar_detalle();
                        winCrearMV.destroy();
                        winCrearMV.close();
                        arrayRecursosHosting = [];
                    }
                }
            ]
        }); 
        
            var winCrearMV = Ext.widget('window', {
                id: 'winCrearMV',
                title: 'Configuración del Producto',
                layout: 'fit',
                resizable: true,
                modal: true,
                closable: false,
                width: '500',
                items: [formCrearMV]
            });


            Ext.getCmp('fs_gridRecursos').setVisible(esMultiCaracteristica);
            Ext.getCmp('fs_gridMaquinasVirtuales').setVisible(boolEsPoolCompleto);
            
            winCrearMV.show(); 
            initProgressBar();
            Ext.getCmp('btnConfigurar').setDisabled(true);
            //Inicializador del css
            $('#tbl_htmlFormaulario').find('input[type=text],select').each(function() 
            {
                $(this).addClass("form-control");
                $(this).css("width","50%");
            });

            if (boolEsLicencia)
            {
                $("#cantidad").prop('disabled', true);

            }

            $("#textarea").addClass("form-control");
            $("#textarea").css("width","50%");

            var Frecuencia = attachDiv($(trFrecuencia).find('#frecuencia-bs'), 'Frecuencia de Facturación');

            $("#divHeader").append(Frecuencia);
            $('#frecuencia-bs').addClass('form-control');

            var strPlantillaSeleccionada = '';
            if(!esMultiCaracteristica)
                {                    
                    //Actualizar formulario para escenarios donde no exista un selector de configuraciones de producto   
                    actualizaDescripcion(true);
                }
                if(esMultiCaracteristica)
                {
                    document.getElementById("cantidad").disabled = true;
                }
                else
                {
                    document.getElementById("cantidad").disabled = false;
                }
                    
            // Si es edición se procede a llenar los grids
            if (esEdicionProducto)
            {                  
                secuencialSolucion = uiToConfigurate.attr('secuencial');
                var jsonSolucion   = arraySolucion.filter(solucion => solucion.secuencial == secuencialSolucion);
                var jsonMaquinasVirtuales = arrayMaquinasVirtuales.filter(mv => mv.secuencial == secuencialSolucion);
                if(esMultiCaracteristica)
                {      
                    var arrayCaracteristica = "";
                    //LLenamos Grids
                    if (data.esLicencia)
                    {
                        arrayCaracteristica = agruparLicenciasNuevo(jsonSolucion[0].caracteristicasPoolRecursos);
                        arrayRecursosHosting = JSON.parse(jsonSolucion[0].caracteristicasPoolRecursos);
                    }else 
                    {
                        arrayCaracteristica = JSON.parse(jsonSolucion[0].caracteristicasPoolRecursos);
                        arrayRecursosHosting = JSON.parse(jsonSolucion[0].caracteristicasPoolRecursos);

                    }
                    llenarGridRecursos(arrayCaracteristica, storeRecursosCaracteristicas, rowEditingRecursos, esEdicionProducto);
                    if(boolEsPoolRecursosCompleto)
                    {
                        llenarGridMaquinasVirtuales(jsonMaquinasVirtuales[0].maquinasVirtuales, storeMaquinasVirtualesCaracteristicas, rowMaquinasVirtuales);
                    }
                    actualizarPrecios();
                }                
                $('#frecuencia-bs').val(jsonSolucion[0].frecuencia);
                $("#cantidad").val(jsonSolucion[0].cantidad);
                
                $("#descripcion_producto").val(jsonSolucion[0].descripcion);
                
                //Valores
                $("#precio_unitario").val(jsonSolucion[0].precio);               
                
                document.getElementById('precio_venta').disabled = false;
                
                if(!Ext.isEmpty(jsonSolucion[0].precioInstalacion))
                {
                    $("#precio_instalacion").val(jsonSolucion[0].precioInstalacion);
                }
                
                $("#precio_venta").val(jsonSolucion[0].precio_venta);
                $("#precio_total").val(parseFloat(jsonSolucion[0].precio_venta)*parseInt(jsonSolucion[0].cantidad));
                
                strPlantillaSeleccionada = jsonSolucion[0].strPlantillaComisionista;
                
                //Cargar la información de las características configuradas previamente
                if(!esMultiCaracteristica)
                {                    
                    var arrayCaracteristicaProducto = Ext.JSON.decode(jsonSolucion[0].caracteristicasProducto);
                    
                    $.each(arrayCaracteristicaProducto, function(i, item)
                    {
                        var caract = item.descripcion;
                        var valor  = item.valor;             
                        
                        var arrayElementDom = $( "input[id^='caracteristicas_'],select[id^='caracteristicas_']" );

                        $.each(arrayElementDom,function(i,item)
                        {
                            var value = $("#caracteristica_nombre_"+i).attr('value');                                                        

                            if(value === caract)
                            {
                                $(this).val(valor);
                            }
                        });
                    });
                }        
                
                //Para comunicaciones
                if(!Ext.isEmpty(jsonSolucion[0].ultimaMilla))
                {
                    $("#ultimaMillaIdProd").val(jsonSolucion[0].ultimaMilla);
                    
                    if($("#tipoSolucion").length !== 0)
                    {
                        var text = $("#ultimaMillaIdProd option:selected" ).text();
                        
                        if(text === 'UTP')
                        {
                            $("#tipoSolucion").val('HOUSING');                            
                        }
                        else
                        {
                            $("#tipoSolucion").val('HOSTING');                            
                        }
                    }
                }
            }
            getComisionistas(idParent,(esEdicionProducto?'editarGuardado':''),strPlantillaSeleccionada);
        }        
        
    });
    
}

function initProgressBar(esEdicionProducto)
{  
    var storageTemp    =  0 ;
    var memoriaTemp    =  0 ;
    var procesadorTemp =  0;
    
    if(esEdicionProducto)
    {
        storageTemp    =  storageUsado ;
        memoriaTemp    =  memoriaUsado ;
        procesadorTemp =  procesadorUsado;

        $("#progressbar-storage").progressbar({
            max:storageTotal
        });

        $("#progressbar-memoria").progressbar({
            max:memoriaTotal
        });

        $("#progressbar-procesador").progressbar({
            max:procesadorTotal
        });

        actualizarTotal('storage'   , storageTotal    - storageUsado);
        actualizarTotal('memoria'   , memoriaTotal    - memoriaUsado);
        actualizarTotal('procesador', procesadorTotal - procesadorUsado);
    }
    else
    {
        storageTemp    = storageTotal    - storageUsado;
        memoriaTemp    = memoriaTotal    - memoriaUsado;
        procesadorTemp = procesadorTotal - procesadorUsado;

        $("#progressbar-storage").progressbar({
            max:storageTemp
        });

        $("#progressbar-memoria").progressbar({
            max:memoriaTemp
        });

        $("#progressbar-procesador").progressbar({
            max:procesadorTemp
        });

        $("#progressbar-storage").progressbar("option","value",storageTemp);
        $("#progressbar-memoria").progressbar("option","value",memoriaTemp);
        $("#progressbar-procesador").progressbar("option","value",procesadorTemp);

        $("#progressbar-storage-label").text( 'Storage '+ storageTemp+" (GB)");
        $("#progressbar-memoria-label").text( 'Memoria '+ memoriaTemp+" (GB)");
        $("#progressbar-procesador-label").text('Procesador '+ procesadorTemp+" (Cores)");
    }
}

function attachDiv(elemento, label)
{   
    var objDiv = $("<div class='col-sm-2'><label> " + label + "</label></div>");
    return objDiv.append(elemento);
}
function attachDivLargo(elemento, label)
{   
    var objDiv = $("<div ><label> " + label + "</label></div><br>");
    return objDiv.append(elemento);
}

function setUM()
{
    var valTipoSolucion = $( "#tipoSolucion" ).val();
    if(valTipoSolucion === 'HOSTING')
    {
        $("#ultimaMillaIdProd").find("option").each(function(){

            if($(this).text() === 'Fibra Optica')
            {
                $('#ultimaMillaIdProd').val($(this).val());                                
                return false;
            }
        });
    }
    else if(valTipoSolucion === 'HOUSING')
    {                        
        $("#ultimaMillaIdProd").find("option").each(function(){

            if($(this).text() === 'UTP')
            {
                $('#ultimaMillaIdProd').val($(this).val());                                
                return false;
            }
        });  
    }
}

function renderizarConfiguracionMultiCaractetistica(data)
{
    boolEsPoolRecursosCompleto           = data.esPoolCompleto==='SI';
    boolEsLicencia                       = data.esLicencia;
    var storeCaracteristicas             = [];
    var storeTipoRecurso                 = [];
    var esEdicion                        = false;
    rawNumber                            = 1;       
    arrayRecursosHosting                 = [];
    var esEdicionProductoCaracteristicas = false;
    var maquinaVirtualAnt                = '';
    var recursoAnterior                  = '';
    
        
    //Asignar Store de acuerdo al tipo de recurso que se requiere
    var array = [];
    $.each(data.arrayJsonCaractMultiple, function(i, item) 
    {
        var json      = {};
        json['id']    = item.tipoCaracteristica;
        json['value'] = item.tipoCaracteristica;
        array.push(json);        
    });
    
    storeTipoRecurso = getStoreCaracteristicas(array);
    
    rowEditingRecursos = Ext.create('Ext.grid.plugin.RowEditing', {
        saveBtnText: '<i class="fa fa-check-square"></i>',
        cancelBtnText: '<i class="fa fa-eraser"></i>',
        clicksToMoveEditor: 1,
        autoCancel: false,
        listeners: {
            canceledit: function(editor, e, eOpts) 
            {
                var rawData = e.record.data;
                if(boolEsPoolRecursosCompleto)
                {
                    existeRecurso = validarMVPoolRecursos(rawData.idRaw); //Si existe recurso
                    if(existeRecurso['existeRecurso'])
                    {
                        Ext.Msg.alert('Atención', 'Este recurso está siendo utilizado por la máquina,'+ existeRecurso['nombreMaquina']+' ');
                        return false;
                    }
                }
                arrayRecursosHosting = arrayRecursosHosting.filter(function(elem)
                {
                    return elem.idRaw !== e.record.data.idRaw;
                });
                
                 arrayResumenGeneralRecursos =  arrayResumenGeneralRecursos.filter(function(elem)
                {
                    return elem.idRecurso !== e.record.data.idRaw;
                });
                json = {};
                json['data'] = rawData;
                deleteLicenciasToArrayInformacion([json]);

                reducirTotales(parseInt(rawData.cantidad, 10), rawData.tipoRecurso);
                e.store.remove(e.record);
                initProgressBar();
                actualizarPrecios();
                calcularTotalesMvs();
            },
            beforeedit:function(editor, e, eOpts) 
            {
                maquinaVirtualAnt = e.record.data.maquinaVirtual;
                esEdicionProductoCaracteristicas = false;
                arrayRecursosHostingFind = [];
                if(e.record.data.caracteristica)
                {
                    recursoAnt = e.record.data.cantidad;
                    esEdicionProductoCaracteristicas = true;
                }                
                
                if(esMultiCaracteristica)
                {
                    var tipoRecurso = e.record.data.tipoRecurso;

                    arrayRecursosHostingFind = arrayRecursosHosting.filter(recurso => recurso.idRaw == e.record.data.idRaw);
                    if(arrayRecursosHostingFind.length > 0 && accion == 'editar')
                    {
                        if(arrayRecursosHostingFind[0].esAntiguo)
                        {
                            Ext.Msg.alert('Error', 'Debe Eliminar y crear una nueva característica del producto');
                            return false
                        }
                    }

                    if(!Ext.isEmpty(tipoRecurso))
                    {
                        $.each(data.arrayJsonCaractMultiple, function(i, item) 
                        {
                            if(item.tipoCaracteristica === tipoRecurso)
                            {
                                storeCaracteristicas = getStoreCaracteristicas(item.arrayCaracteristica);
                                return false;
                            }
                        });
                        
                        Ext.getCmp('cmbCaracteristica').value = "";
                        Ext.getCmp('cmbCaracteristica').setRawValue("");
                        Ext.getCmp('txtCantidad').value = "";
                        Ext.getCmp('txtCantidad').setRawValue("");
                        Ext.getCmp('txtDescuento').value       = "";
                        Ext.getCmp('txtDescuento').setRawValue("");
                        Ext.getCmp('txtPrecioTotal').value       = "";
                        Ext.getCmp('txtPrecioTotal').setRawValue("");
                        Ext.getCmp('txtPrecioUnitario').value       = "";
                        Ext.getCmp('txtPrecioUnitario').setRawValue("");
                        Ext.getCmp('cmbCaracteristica').bindStore(storeCaracteristicas);                        
                        esEdicion = true;
                    }
                }
                
                if(Ext.isEmpty(e.record.data.precioNegociado))
                {
                    Ext.getCmp("txtPrecioNegociado").setDisabled(true);
                }
                else
                {
                    Ext.getCmp("txtPrecioNegociado").setDisabled(false);
                }
            },
            afteredit: function(editor, e, eOpts) 
            {
                var intCountGridDetalle = Ext.getCmp('gridRecursos').getStore().getCount();
                var selectionModel      = Ext.getCmp('gridRecursos').getSelectionModel();
                selectionModel.select(0);
                
                var cantidad       = e.record.data.cantidad;
                var tipoRecurso    = e.record.data.tipoRecurso;
                var caracteristica = e.record.data.caracteristica;

                if (!boolEsPoolRecursosCompleto && !boolEsLicencia) {
                    cantidad = Ext.isEmpty(cantidad) || isNaN(cantidad) || cantidad <= 0 ? 1 : cantidad;
                    e.record.set("cantidad",cantidad);
                }

                if (intCountGridDetalle > 0 )
                {
                    if ((boolEsPoolRecursosCompleto && ((Ext.isEmpty(cantidad) || cantidad === 0 ) || Ext.isEmpty(tipoRecurso))) ||
                         Ext.isEmpty(caracteristica))
                    {
                        Ext.Msg.alert('Error', 'Debe escoger los valores para asignar el recurso');
                        rowEditingRecursos.cancelEdit();
                        selectionModel.select(0);
                        rowEditingRecursos.startEdit(0, 0);
                        return false;
                    }
                    else
                    {
                        var rawData = e.record.data;
                        var numCore = 0;
                        var idRaw = numberRaw;
                        var esAntiguo = false;

                        //Variable compartida con archivos de edicion de soluciones
                        //Se verifica si se está editando se extra el array para posteriormente volverlo a poner
                        if(esEdicionProductoCaracteristicas)
                        {
                            arrayRecursosHosting = arrayRecursosHosting.filter(function(elem)
                            {
                                if(elem.idRaw === rawData.idRaw)
                                {
                                    if(rawData.maquinaVirtual != undefined && typeof elem.esAntiguo != 'undefined')
                                    {
                                        var json = {};
                                        json['idMaquina']   = rawData.maquinaVirtual;
                                        json['idLicencias'] = elem.idLicencias;
                                        arrayLicenciasEditadas.push(json);

                                        esAntiguo = true;
                                        if (maquinaVirtualAnt != rawData.maquinaVirtual)
                                        {
                                            json['idMaquina']   = maquinaVirtualAnt;
                                            json['idLicencias'] = elem.idLicencias;
                                            json['idMaquinaActual'] = rawData.maquinaVirtual;
                                        }
                                    }
                                    idRaw = rawData.idRaw;
                                }
                                return elem.idRaw !== rawData.idRaw;
                            });
                        }
                        else
                        {
                            idRaw = numberRaw; 
                        }
                        
                        jsonResumenRecursos = {};
                        jsonResumenRecursos['idRecurso']      = idRaw;
                        jsonResumenRecursos['tipo']           = rawData.tipoRecurso;
                        jsonResumenRecursos['característica'] = rawData.caracteristica;
                        jsonResumenRecursos['total']          = rawData.cantidad;
                        jsonResumenRecursos['disponible']     = rawData.cantidad;
                        jsonResumenRecursos['idMaquinas']     = [rawData.maquinaVirtual ? rawData.maquinaVirtual : null];
                        jsonResumenRecursos['usado']          = 0;
                        arrayResumenGeneralRecursos.push(jsonResumenRecursos);

                        var json                = {};
                        json['idRaw']           = idRaw;
                        json['tipoRecurso']     = rawData.tipoRecurso;
                        json['caracteristica']  = rawData.caracteristica;
                        json['descuento']       = rawData.hdPorcentaje;
                        json['idMaquinas']      = (rawData.idMaquinas) ? rawData.idMaquinas : [];
                        json['esEdicion']       = esEdicionProductoCaracteristicas;
                        json['idLicencias']     = idRaw;

                        if (esAntiguo) {
                            json['esAntiguo'] = true;
                        }

                        jsonPreciosMulticaracteristicas = calcularPrecioMultiCaracteristica(rawData.tipoRecurso,
                                                                                            rawData.caracteristica,
                                                                                            rawData.cantidad,
                                                                                            rawData.precioNegociado,);

                        e.record.set("precioUnitario",  jsonPreciosMulticaracteristicas['precioUnitario']);
                        e.record.set("precioNegociado", jsonPreciosMulticaracteristicas['precioNegociado']);
                        e.record.set("descuento",       jsonPreciosMulticaracteristicas['descuento']);
                        e.record.set("precioTotal",     jsonPreciosMulticaracteristicas['precioTotal']);
                        e.record.set("hdValor",         jsonPreciosMulticaracteristicas['hdValor']);
                        e.record.set("hdPorcentaje",    jsonPreciosMulticaracteristicas['hdPorcentaje']);
                        e.record.set("idRaw",           numberRaw);
                        json['precioNeg'] =             jsonPreciosMulticaracteristicas['precioNegociado'];
                        json['descuento'] =             jsonPreciosMulticaracteristicas['hdPorcentaje'];

                        //Se añade el primer if para evitar la duplicidad de las licencias.
                        //Esta funcionalidad es solo para la edición.
                        if (boolEsLicencia && boolEsEditarSolucion) {
                            json['cantidad']   = rawData.cantidad;
                            json['idMaquinas'] = [rawData.maquinaVirtual ? rawData.maquinaVirtual : null];
                            arrayRecursosHosting.push(json);
                        } else if (boolEsLicencia){
                            for(let i = 0; i < rawData.cantidad; i++){
                                json['cantidad']   = 1;
                                json['idMaquinas'] = [rawData.maquinaVirtual];
                                arrayRecursosHosting.push(json);
                            }
                        } else {
                            json['cantidad'] = rawData.cantidad;
                            arrayRecursosHosting.push(json);
                        }

                        (esEdicionProductoCaracteristicas) ? '' : numberRaw++ ;

                        actualizarPrecios();
                        esEdicion = false;

                        storageTotal    = isNaN(storageTotal)    || storageTotal    < 0 ? 0 : storageTotal;
                        procesadorTotal = isNaN(procesadorTotal) || procesadorTotal < 0 ? 0 : procesadorTotal;
                        memoriaTotal    = isNaN(memoriaTotal)    || memoriaTotal    < 0 ? 0 : memoriaTotal;

                        //Segmento
                        if (esEdicionProductoCaracteristicas)
                        {
                            if(rawData.tipoRecurso == 'DISCO')
                            {
                                storageTotal = parseInt(rawData.cantidad, 10)  + storageTotal - parseInt(recursoAnt, 10);
                            }
                            else if(rawData.tipoRecurso == 'PROCESADOR')
                            {
                                procesadorTotal = parseInt(rawData.cantidad, 10)  + procesadorTotal - parseInt(recursoAnt, 10);
                            }
                            else
                            {
                                memoriaTotal = parseInt(rawData.cantidad, 10)  + memoriaTotal - parseInt(recursoAnt, 10);
                            }
                        }
                        else
                        {
                            if(rawData.tipoRecurso == 'DISCO')
                            {
                                storageTotal = parseInt(rawData.cantidad, 10)  + storageTotal;
                            }
                            else if(rawData.tipoRecurso == 'PROCESADOR')
                            {
                                procesadorTotal = parseInt(rawData.cantidad, 10)  + procesadorTotal;
                            }
                            else
                            {
                                memoriaTotal = parseInt(rawData.cantidad, 10)  + memoriaTotal;
                            }
                        }

                        initProgressBar();
                        calcularTotalesMvs();
                    }
                }
            }
        }
    });
       
    
    Ext.define('recursosModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idRaw',          type: 'integer'},
            {name: 'maquinaVirtual', type: 'integer'},
            {name: 'tipoRecurso',    type: 'string'},
            {name: 'caracteristica', type: 'string'},
            {name: 'cantidad',       type: 'string'},
            {name: 'precioUnitario', type: 'string'},
            {name: 'precioNegociado',type: 'string'},
            {name: 'descuento',      type: 'string'},
            {name: 'precioTotal',    type: 'string'},
            {name: 'hdvalor',        type: 'float'},
            {name: 'hdPorcentaje',   type: 'float'},
            {name: 'esAntiguo'   ,   type: 'boolean'},
            {name: 'idServicio'   ,  type: 'integer'}
        ]
    });    
      
    storeRecursosCaracteristicas = Ext.create('Ext.data.Store', {
        pageSize: 5,
        autoDestroy: true,
        model: 'recursosModel',
        proxy: {
            type: 'memory'
        }
    });
    
    var toolbarRecursos = Ext.create('Ext.toolbar.Toolbar', {
		dock: 'top',
		align: '->',
                id:'tlbAgregarNuevo' ,
		items   : 
		[ 
			{
				iconCls: 'icon_add',
				text: 'Agregar ' ,
				id: 'btnAgregarRecursoNuevo',
				scope: this,
				handler: function()
                                {                   
                                    agregarRecurso();                                    
                                }
			},
                        {
				iconCls: 'icon_delete',
				text: 'Eliminar Recurso ' ,
				id: 'btnEliminarRecurso',
				scope: this,
				handler: function()
                                {                   
                                    eliminarRecurso();
                                }                                
			}
		]
    });    
    
    
    var gridPanels = Ext.create('Ext.grid.Panel', 
    {
        width: 935,        
        collapsible: false,
        dockedItems: [ toolbarRecursos ], 
        layout:'fit',        
        //renderTo: 'content-multi-caracteristica',        
        title:'Ingreso de Características del Producto',
        store: storeRecursosCaracteristicas,
        plugins: [rowEditingRecursos],
        id: 'gridRecursos',
        height: 250,       
        columns: 
        [
            {
                id: 'idRaw',
                dataIndex: 'idRaw',
                hidden: true,
                hideable: false
            },
            {
                header: "<b>Tipo</b>",
                dataIndex:'tipoRecurso',
                width: 110,
                align: 'left',
                editor: new Ext.form.field.ComboBox({
                    typeAhead: true,
                    id: 'cmbTipoRecurso',
                    name: 'cmbTipoRecurso',
                    valueField: 'id',
                    displayField: 'value',
                    store: storeTipoRecurso,
                    listeners:
                        {
                            select: function(combo, record, index) 
                            {
                                Ext.getCmp('cmbCaracteristica').value = "";
                                Ext.getCmp('cmbCaracteristica').setRawValue("");
                                Ext.getCmp('txtCantidad').value       = "";
                                Ext.getCmp('txtCantidad').setRawValue("");
                                Ext.getCmp('txtPrecioNegociado').value       = "";
                                Ext.getCmp('txtPrecioNegociado').setRawValue("");
                                Ext.getCmp('txtDescuento').value       = "";
                                Ext.getCmp('txtDescuento').setRawValue("");
                                Ext.getCmp('txtPrecioTotal').value       = "";
                                Ext.getCmp('txtPrecioTotal').setRawValue("");
                                Ext.getCmp('txtPrecioUnitario').value       = "";
                                Ext.getCmp('txtPrecioUnitario').setRawValue("");
                                //Obtener el datastore segun tipo
                                var value = combo.getValue();
                                $.each(data.arrayJsonCaractMultiple, function(i, item) 
                                {
                                    if(item.tipoCaracteristica === value)
                                    {
                                        storeCaracteristicas = getStoreCaracteristicas(item.arrayCaracteristica);
                                        return false;
                                    }
                                });
                                
                                Ext.getCmp('cmbCaracteristica').bindStore(storeCaracteristicas);
                                if(boolEsLicencia)
                                {
                                    arrayValidaLicencias = arrayParametrosLicencias[value];
                                     
                                }
                            }
                        },
                    editable: false
                })
            },
            {
                header: "<b>Característica</b>",
                width: 350,
                dataIndex:'caracteristica',
                editor: new Ext.form.field.ComboBox({
                    typeAhead: true,
                    id: 'cmbCaracteristica',
                    name: 'cmbCaracteristica',
                    valueField: 'id',
                    displayField: 'value',
                    editable: false,
                    listeners:
                        {
                            select: function(combo, record, index) 
                            {
                                var nombre = combo.getValue();
                                Ext.getCmp('txtCantidad').value = "";
                                Ext.getCmp('txtCantidad').setRawValue("");
                                Ext.getCmp('txtPrecioNegociado').value       = "";
                                Ext.getCmp('txtPrecioNegociado').setRawValue("");
                                Ext.getCmp('txtDescuento').value       = "";
                                Ext.getCmp('txtDescuento').setRawValue("");
                                Ext.getCmp('txtPrecioTotal').value       = "";
                                Ext.getCmp('txtPrecioTotal').setRawValue("");
                                Ext.getCmp('txtPrecioUnitario').value       = "";
                                Ext.getCmp('txtPrecioUnitario').setRawValue("");

                                if(boolEsLicencia && arrayMaquinasVirtuales.length >0)
                                {
                                    var maquinaVirtual =  Ext.getCmp('cmbMaquinaVirtual').getValue();
                                    numCore =  buscarNumeroCore(maquinaVirtual);
                                    var jsonLicencia = validarLicencia(numCore,nombre);
                                    //Cuando se necesita Small Instance
                                    if(jsonLicencia['esRHSmallInstance'] && numCore > jsonLicencia['numeroCoreReq'] )
                                    {                                        
                                        Ext.Msg.alert('Advertencia',  'Debe seleccionar licencia tipo " ' + jsonLicencia['nombre'] + ' "');
                                    }
                                    else if ( jsonLicencia['esRHLargeInstance'] && numCore <= jsonLicencia['numeroCoreReq'])
                                    {
                                        Ext.Msg.alert('Advertencia',  'Debe seleccionar licencia tipo " ' + jsonLicencia['nombre'] + ' "');
                                    }                 

                                   
                                    Ext.getCmp('txtCantidad').value = jsonLicencia['numeroLicencia'];
                                    Ext.getCmp('txtCantidad').setRawValue(jsonLicencia['numeroLicencia']);
                                 
                                    
                                    
            
                                }
                            }                            
                        }
                })
            },
            {
                header: "<i class='fa fa-hashtag'></i>&nbsp;<b>Recursos</b>",
                dataIndex:'cantidad',
                width: 80,
                align:'center',
                disabled:(!boolEsPoolRecursosCompleto & !boolEsLicencia),
                editor: {
                    xtype: 'numberfield',
                    id:'txtCantidad',
                    hideTrigger: true,
                    disabled:(!boolEsPoolRecursosCompleto & !boolEsLicencia),
                    enableKeyEvents:true,
                    fieldStyle:'font-weight:bold;text-align: center;',
                    allowNegative: false,
                    minValue: 0,
                    listeners:
                        {
                            keyup: function() 
                            {
                                Ext.getCmp('txtPrecioNegociado').value       = "";
                                Ext.getCmp('txtPrecioNegociado').setRawValue("");
                                Ext.getCmp('txtPrecioTotal').value       = "";
                                Ext.getCmp('txtPrecioTotal').setRawValue("");
                            }
                        }
                },
                renderer: function(value, meta, record) 
                {
                    meta.tdCls = 'class-valores-edicion';
                    return value;
                }
            },
            {
                header: "<b>Precio Unitario</b>",
                dataIndex:'precioUnitario',                
                width: 110,
                align:'center',
                renderer: function(value, meta, record) 
                {
                    meta.tdCls = 'class-valores-pre';
                    return value;
                },
                editor: {                    
                    xtype: 'textfield',
                    id:'txtPrecioUnitario',
                    readOnly:true,
                    disabled:true,
                    align:'center',
                    fieldStyle:'color:green;font-weight:bold;text-align: center;'
                }
            },
            {
                header: "<b>Precio Negociado</b>",
                dataIndex:'precioNegociado',                
                width: 110,
                align:'center',
                renderer: function(value, meta, record) 
                {
                    meta.tdCls = 'class-valores-edicion';
                    return value;
                },
                editor: {                    
                    xtype: 'textfield',
                    id:'txtPrecioNegociado',
                    align:'center',
                    fieldStyle:'font-weight:bold;text-align: center;'
                }
            },
            {
                header: "<b>% Dscto.</b>",
                dataIndex:'descuento',
                width: 75,
                align:'center',
                renderer: function(value, meta, record) 
                {
                    meta.tdCls = 'class-valores-pre';
                    return value;
                },
                editor: {
                    xtype: 'numberfield',
                    id:'txtDescuento',
                    align:'center',
                    hideTrigger: true,
                    disabled:true,
                    fieldStyle:'color:green;font-weight:bold;text-align: center;'
                }                
            },
            {
                header: "<b>Precio Total</b>",
                dataIndex:'precioTotal',                
                width: 100,
                align:'center',
                renderer: function(value, meta, record) 
                {
                    meta.tdCls = 'class-valores-final';
                    return value;
                },
                editor: {                    
                    xtype: 'textfield',
                    id:'txtPrecioTotal',
                    readOnly:true,
                    disabled:true,
                    align:'center',
                    fieldStyle:'color:blue;font-weight:bold;text-align: center;'
                }
            },
            {
                id: 'hdValor',
                dataIndex: 'hdValor',
                hidden: true,
                hideable: false
            },
            {
                id: 'hdPorcentaje',
                dataIndex: 'hdPorcentaje',
                hidden: true,
                hideable: false
            }
        ]
    });
    

    if (boolEsLicencia & arrayMaquinasVirtuales.length > 0)
    {
        //Cuando es edición de solución y la acción es 'agregar', solo se permitirá mostrar las
        //maquinas virtuales nuevas por motivos que las existentes son editadas de manera individual
        //con la acción editar en la edición de solución.
        if (boolEsEditarSolucion && accion == 'agregar') {
            var arrayMaquinasVirtualesNuevas = arrayMaquinasVirtuales.filter(mvs => mvs.esNuevo);
            storeMaquinasVirtuales = getStoreMaquinasVirtuales(arrayMaquinasVirtualesNuevas);
        } else {
            storeMaquinasVirtuales = getStoreMaquinasVirtuales(arrayMaquinasVirtuales);
        }

        gridPanels.headerCt.insert(
                1,
                {
                    header    : '<b>Máquina Virtual</b>',
                    dataIndex : 'maquinaVirtual',
                    width     :  110,
                    align     : 'left',
                    renderer  : function(value, metadata, record, rowIndex, colIndex, store) 
                    {
                        var nombre ='';
                        for (var i = 0; i < storeMaquinasVirtuales.data.items.length; i++)
                            {
                                if (storeMaquinasVirtuales.data.items[i].data.id == record.data.maquinaVirtual)
                                {
                                    nombre = storeMaquinasVirtuales.data.items[i].data.value;
                                    break;
                                }
                            }
                        return nombre;
                    },
                    editor : new Ext.form.field.ComboBox({
                        typeAhead    : true,
                        id           : 'cmbMaquinaVirtual',
                        name         : 'cmbMaquinaVirtual',
                        valueField   : 'id',
                        displayField : 'value',
                        store        : storeMaquinasVirtuales,
                        listeners    : {
                            select : function(combo, record, index) {
                                Ext.getCmp('cmbCaracteristica').value = "";
                                Ext.getCmp('cmbCaracteristica').setRawValue("");
                                Ext.getCmp('txtCantidad').value = "";
                                Ext.getCmp('txtCantidad').setRawValue("");
                                Ext.getCmp('txtPrecioNegociado').value = "";
                                Ext.getCmp('txtPrecioNegociado').setRawValue("");
                                Ext.getCmp('txtDescuento').value = "";
                                Ext.getCmp('txtDescuento').setRawValue("");
                                Ext.getCmp('txtPrecioTotal').value = "";
                                Ext.getCmp('txtPrecioTotal').setRawValue("");
                                Ext.getCmp('txtPrecioUnitario').value = "";
                                Ext.getCmp('txtPrecioUnitario').setRawValue("");
                            }
                        },
                        editable: false
                    })
                }
            );
    }
    return gridPanels;
}

function actualizarTotal(tipo,cambio)
{
    var value = cambio;

    $("#progressbar-" + tipo).progressbar("option", "value", parseInt(value));

    var unidad = '(GB)';
    if(tipo === 'procesador')
    {
        unidad = '(Cores)';
    }
    $("#progressbar-" + tipo + "-label").text(tipo + " " + value + " " + unidad);
}

function agregarRecurso()
{
    rowEditingRecursos.cancelEdit();
    var recordParamDet = Ext.create('recursosModel', {
            idRaw          : rawNumber,
            tipoRecurso    : '',
            caracteristica : '',
            cantidad       : '',
            precioUnitario : '',
            precioNegociado: '',
            descuento      : '',
            precioTotal    : '',
            hdvalor        : 0,
            hdPorcentaje   : 0
        });

    storeRecursosCaracteristicas.insert(0, recordParamDet);
    rowEditingRecursos.startEdit(0, 0);    
}

function eliminarRecurso()
{
    var gridRecursos   = Ext.getCmp('gridRecursos');
    var selectionModel = gridRecursos.getSelectionModel();

    if(selectionModel.getSelection()[0])
    {
        var rawData       = selectionModel.getSelection()[0].data;
        var existeRecurso = false;

        if (boolEsPoolRecursosCompleto)
        {
            existeRecurso = validarMVPoolRecursos(rawData.idRaw); //Si existe recurso 
        }

        //Se verifica si es que una licencia está siendo utilizada por una máquina virtual
        if (boolEsLicencia)
        {
            $.each(arrayMaquinasVirtuales, function(index, maquinasVirtuales){
                $.each(maquinasVirtuales.maquinasVirtuales, function (index, maquina){
                    var arrayRecursos = JSON.parse(maquina.arrayRecursos);
                    recurso = arrayRecursos.find(recurso => recurso.idRecurso == rawData.idRecurso);
                    if(recurso)
                    {
                        Ext.Msg.alert('Atención', 'Este recurso está siendo utilizado por la máquina,'+ maquina.nombre+' ');
                        return false;
                    }
                });
            });

            json = {};
            json['data'] = rawData;
            deleteLicenciasToArrayInformacion([json]);

            if (accion == 'editar')
            {
                var arrayMaquinasConSO;
                var arrayMaquinasInvolved = [];
                var arrayMaquinasSinSO = [];
                arrayMaquinasInvolved = searchMaquinasInvolved(gridRecursos.getStore().data.items);
                arrayMaquinasSinSO    = verificarSOMaquina(arrayMaquinasInvolved);
                arrayMaquinasConSO    = verificarSOMaquinaGuardada(gridRecursos.getStore().data.items,
                                                                   rawData.tipoRecurso,
                                                                   rawData.maquinaVirtual);

                if (arrayMaquinasConSO && rawData.tipoRecurso == 'SISTEMA OPERATIVO')
                {
                    $.each(arrayMaquinasSinSO, function(index, value){
                        maquinasMensaje = maquinasMensaje  + value + ', ';
                    });

                    Ext.Msg.alert('Error', 'La máquina solo tiene un sistema operativo, ingrese uno '+
                                           'nuevo para poder eliminar el seleccionado.');
                    return false;
                }
            }
        }
        
        if(!existeRecurso['existeRecurso'])
        {
            var intIdMaquinaVIrtual = rawData.maquinaVirtual && rawData.maquinaVirtual > 0 ?
                                      rawData.maquinaVirtual : null;

            if(accion == 'editar')
            {
                $.each(arrayRecursosHosting, function(index, recurso){

                    if(recurso.idRaw          == rawData.idRaw          &&
                       recurso.tipoRecurso    == rawData.tipoRecurso    &&
                       recurso.caracteristica == rawData.caracteristica &&
                       recurso.cantidad       == rawData.cantidad       &&
                       recurso.idMaquinas[0]  == intIdMaquinaVIrtual)
                    {
                        var jsonLicenciaMaquina = {};
                        jsonLicenciaMaquina['idMaquina']      = intIdMaquinaVIrtual;
                        jsonLicenciaMaquina['idRaw']          = rawData.idRaw;
                        jsonLicenciaMaquina['tipoRecurso']    = rawData.tipoRecurso;
                        jsonLicenciaMaquina['caracteristica'] = rawData.caracteristica;
                        jsonLicenciaMaquina['cantidad']       = rawData.cantidad;
                        jsonLicenciaMaquina['idLicencias']    = recurso.idLicencias;
                        if(typeof recurso.esAntiguo != 'undefined')
                        {
                            arrayLicenciasEliminadas.push(jsonLicenciaMaquina);
                        }
                    }
                });
            }

            if (boolEsEditarSolucion && boolEsLicencia)
            {
                arrayRecursosHosting = arrayRecursosHosting.filter(function(elem)
                {
                    var idMaquina = elem.idMaquinas[0] ? elem.idMaquinas[0] : null;

                    return !(elem.idRaw          == rawData.idRaw          &&
                             elem.tipoRecurso    == rawData.tipoRecurso    &&
                             elem.caracteristica == rawData.caracteristica &&
                             elem.cantidad       == rawData.cantidad       &&
                             idMaquina           == intIdMaquinaVIrtual);
                });

                arrayResumenGeneralRecursos = arrayResumenGeneralRecursos.filter(function(elem)
                {
                    var idMaquina = elem.idMaquinas[0] ? elem.idMaquinas[0] : null;

                    return !(elem.idRecurso      == rawData.idRaw          &&
                             elem.tipo           == rawData.tipoRecurso    &&
                             elem.característica == rawData.caracteristica &&
                             elem.total          == rawData.cantidad       &&
                             idMaquina           == intIdMaquinaVIrtual);
                });
            }
            else
            {
                arrayRecursosHosting = arrayRecursosHosting.filter(function(elem)
                {
                    return elem.idRaw !== rawData.idRaw;
                });

                arrayResumenGeneralRecursos = arrayResumenGeneralRecursos.filter(function(elem)
                {
                    return elem.idRecurso !== rawData.idRaw;
                });
            }

            rowEditingRecursos.cancelEdit();
            storeRecursosCaracteristicas.remove(selectionModel.getSelection());

            if (storeRecursosCaracteristicas.getCount() > 0)
            {
                selectionModel.select(0);
            }

            reducirTotales(parseInt(rawData.cantidad, 10), rawData.tipoRecurso,);
            actualizarPrecios();
        }
        else
        {
          Ext.Msg.alert('Atención', 'Este recurso está siendo utilizado por la máquina,'+ existeRecurso['nombreMaquina']+' ');
        }

        initProgressBar();
    }
    else
    {
        Ext.Msg.alert('Atención', 'Por favor, seleccione un registro para ser eliminado');
    }
}

function reducirTotales(cantidad, tipoRecurso)
{    
    if(tipoRecurso == 'DISCO')
    { 
        storageTotal    =  storageTotal - cantidad;
    }
    else if(tipoRecurso == 'PROCESADOR')
    {       
        procesadorTotal =  procesadorTotal - cantidad  ;
    }
    else
    {
        memoriaTotal    =  memoriaTotal - cantidad;
    }
}

function getStoreCaracteristicas(array)
{
    var store = new Ext.data.Store({
        fields: ['id','value'],
        data: array
    });
    
    return store;
}

function calcularPrecioMultiCaracteristica(caract, valor, cantidad, precioNegociado, esEdicionProducto)
{
    if(typeof(precioNegociado) =='string')
    {
        precioNegociado = precioNegociado.replace('$','');
    }
    
    var jsonPrecios = {};

    var funcion_precio = $("#funcion_precio").val();
    funcion_precio     = replaceAll(funcion_precio, "["+caract+"]", valor);
    var precioUnitario = eval(funcion_precio);
    
    var precioTotal    = 0;
    
    if(isNaN(precioUnitario))
    {
        Ext.Msg.alert('Atención', 'Los valores ingresados no cumplen la función precio, favor verificar');
    }
    
    if(boolEsPoolRecursosCompleto || boolEsLicencia)
    {
        //Precio Total de Venta
        precioTotal    = (parseFloat(precioUnitario) * parseFloat(cantidad)).toFixed(2);
    }
    else
    {
        precioTotal = (parseFloat(precioUnitario)).toFixed(2);
    }
        
    var porcentajeDscto    = '0';
    var procentajeReal     = '0';
        
    if(!Ext.isEmpty(precioNegociado))
    {
        if(boolEsPoolRecursosCompleto || boolEsLicencia)
        {
            precioTotal = (parseFloat(precioNegociado) * parseFloat(cantidad)).toFixed(2);
        }
        else
        {
            precioTotal = precioNegociado;
        }
        
        if(precioNegociado < precioUnitario)
        {
            //Si es distinto calcular el procentaje de descuento
            procentajeReal  = parseFloat(100 - ((100 * parseFloat(precioNegociado))/ parseFloat(precioUnitario)));
            porcentajeDscto = procentajeReal.toFixed(2);           
        }
    }
    else
    {
        precioNegociado = precioUnitario;
    } 
    if ( accion == 'editar' || esEdicionProducto)
    {
            precioNegociado =   Math.round((precioUnitario * cantidad - ((precioUnitario * cantidad * parseFloat(porcentajeDscto) ) / 100)) / parseInt(cantidad));
    }

    jsonPrecios['precioUnitario']  = "$ " + precioUnitario;
    jsonPrecios['precioNegociado'] = "$ " + precioNegociado;
    jsonPrecios['descuento']       = "% " + porcentajeDscto;
    jsonPrecios['precioTotal']     = "$ " + precioTotal;
    jsonPrecios['hdValor']         = "$ " + precioTotal;
    jsonPrecios['hdPorcentaje']    = "$ " + procentajeReal;

    //Calculo de valores totales
    
    return jsonPrecios;
}

function actualizarPrecios()
{
    //Calculo de valores totales
    var gridRecursos = Ext.getCmp('gridRecursos');
    var total        = 0;
    var pUnitario    = 0;
    var hdValor      = 0;
    
    for (var i = 0; i < gridRecursos.getStore().getCount(); i++)
    {        
        var data = gridRecursos.getStore().getAt(i).data;
        if (typeof  data.hdValor == 'string')
        {
            hdValor = parseFloat(data.hdValor.replace('$',''));
        }
        else
        {
            hdValor = data.hdvalor;
        }
        total    = parseFloat(total)     + hdValor;
        
        if(data.cantidad!=='-' && !isNaN(parseInt(data.cantidad)))
        {
            pUnitario= parseFloat(pUnitario) + (parseFloat(data.precioUnitario.replace("$ ", "")) * parseInt(data.cantidad));
        }
        else
        {
            pUnitario= parseFloat(pUnitario) + parseFloat(data.precioUnitario.replace("$ ", ""));
        }
    }
    
    var precioUnitarioFinal = total;
    var precioTotal         = (precioUnitarioFinal * parseInt($("#cantidad").val()));
    
    if(document.getElementById('precio_venta'))
    {
        document.getElementById('precio_venta').value = precioUnitarioFinal.toFixed(2);
    }

    if(document.getElementById('precio_unitario'))
    {
        document.getElementById('precio_unitario').value = pUnitario.toFixed(2);
    }

    if(document.getElementById('precio_total'))
    {
        document.getElementById('precio_total').value = precioTotal.toFixed(2);
    }

    if(document.getElementById('precio_venta'))
    {
        document.getElementById('precio_venta').disabled = false;
    }
}

function getComisionistas(idProducto,accion,strPlantillaSeleccionada)
{
    $.ajax(
    {
        type: "POST",
        data: 
        {
            "intIdProducto" : idProducto
        },
        url:  urlGetComisionistas,
        beforeSend: function() 
        {
            if($("#button-editar").length > 0)
            {
                $("#button-editar").addClass("ui-state-disabled").attr("disabled", true);
            }
            
            $('#divHeader').append('<label id="lbl_spinner">'+
                                   '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>&nbsp;Cargando información de comisionistas...</label>');
        },
        success: function(response)
        {            
            $('#lbl_spinner').remove();            
            
            if(Ext.getCmp('btnConfigurar'))
            {
                Ext.getCmp('btnConfigurar').setDisabled(false);
            }

            if(Ext.getCmp('botonEditarSolucion'))
            {
                Ext.getCmp('botonEditarSolucion').setDisabled(false);
            }

            if(response.strMensaje === 'OK')
            {
                plantillas = response.strCombosValidar.split("|");
                
                var htmlComisionista = "<div align='left' class='secHead hr'><b>\n\
                                       <i class='fa fa-users' aria-hidden='true'></i>&nbsp;Informaci&oacute;n de Comisionistas</b></div>"+
                                       response.strPlantillaComisionista;

                //Por la necesidad de renderizar de forma correcta se tuvo que
                //remover de htmlContentNoe 
                $(htmlComisionista).find("select").each(function()
                {
                    var select = $(this);
                    var nombre = '';
                    $(this).remove();
                   if(select.attr('name') == 'cmb557')
                   {
                       nombre = 'Gerente de Producto';
                   }else{
                       nombre = 'Vendedor';
                   }
                    $('#divHeader').append(attachDiv(select, nombre)) ;
                });
                $('#divHeader').find("select").each(function()
                {              
                    Ext.fly(this).addCls('form-control col-sm-4');
                });
                $(".content-comisionistas").find("div").each(function()
                {
                    $(this).removeAttr("style");
                });
                
                //Transformar a UI ( se lo hace de esta manera sabiendo que es el primer elemento de mi DOM ( table )
                //el resto de componentes ya estan inicializados como UI
                //si se esta realizando una edicion, una vez que se termina de cargar la informacion 
                //de vendedores de manera normal, se precargara los registros ingresados en la creacion del servicio}                
                if(accion === 'editar')
                {
                    var arrayInfo = arrayInformacionServicio.arrayInfoBasica[0];
                    
                    if(!Ext.isEmpty(arrayInfo.gerente))
                    {
                        var array = arrayInfo.gerente.split("-");
                        
                        $("#cmb"+array[1]).val(array[0]);
                    }
                    
                    if(!Ext.isEmpty(arrayInfo.vendedor))
                    {
                        var array = arrayInfo.vendedor.split("-");
                        
                        $("#cmb"+array[1]).val(array[0]);
                    }
                }
                //Editar y cargar comisionista seleccionado previo a guardar una solución por primera vez
                if(accion === 'editarGuardado' && !Ext.isEmpty(strPlantillaSeleccionada))
                {                    
                    var arrayComsionistas = strPlantillaSeleccionada.split("|");

                    if(!Ext.isEmpty(arrayComsionistas))
                    {
                        $.each(arrayComsionistas,function(i, item){
                            var array = item.split("---");
                            $("#cmb"+array[0]).val(array[1]);
                        });
                    }
                }
            }            
            else if(response.strMensaje === 'SIN_PLANTILLA')
            {
                $('#divHeader').append('<label>El producto no requiere plantilla de comisionista</label>');
            }
        }
    });
}

    /**
     * Documentación para la función 'getPropuestasTelcoCRM'.
     *
     * Función que llena el combo de las propuestas.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 05-02-2021
     *
     */
    function getPropuestasTelcoCRM()
    {
        $.ajax(
        {
            url: urlGetPropuestasTelcoCRM,
            type: 'post',
            success: function (objResponse) 
            {
                if(objResponse!= null && objResponse!= undefined)
                {
                    var arrayPropuesta = objResponse.arrayPropuesta;
                    if(arrayPropuesta!= null && arrayPropuesta!= undefined)
                    {
                        if(arrayPropuesta.length > 0)
                        {
                            var strOption = '';
                            $.each(arrayPropuesta, function(i, item)
                            {
                                strOption += '<option value="'+item.intIdPropuesta+'">'+item.strPropuesta+'</option>';
                            });
                            $("#objSelectPropuestaBS").append(strOption);
                        }
                        else
                        {
                            document.getElementById('objFilaPropuesta').style.display='none';
                        }
                    }
                    else
                    {
                        document.getElementById('objFilaPropuesta').style.display='none';
                    }
                }
                else
                {
                    document.getElementById('objFilaPropuesta').style.display='none';
                }
            },
            failure: function (response)
            {
                Ext.Msg.alert('Error', 'Ocurrió un error. Por favor comuníquese con Sistemas.');
            }
        });
    }

    /**
     * Documentación para la función 'getCotizacionTelcoCRM'.
     *
     * Función que llena el combo de las cotizaciones.
     *
     * @param int intIdPropuesta Contiene el id de la propuesta seleccionada.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 08-02-2021
     *
     */
    function getCotizacionTelcoCRM(intIdPropuesta)
    {
        var intIdCotizacion;
        $.ajax(
        {
            url: urlGetCotizacionTelcoCRM,
            type: 'post',
            data   : 
            {
                'intIdPropuesta':intIdPropuesta
            },
            success: function (objResponse) 
            {
                if(objResponse!= null && objResponse!= undefined)
                {
                    var arrayCotizacion = objResponse.arrayCotizacion;
                    if(arrayCotizacion!= null && arrayCotizacion!= undefined)
                    {
                        if(arrayCotizacion.length > 0)
                        {
                            var strOption = '';
                            $.each(arrayCotizacion, function(i, item)
                            {
                                intIdCotizacion = item.intIdCotizacion;
                                strOption      += '<option value="'+item.intIdCotizacion+'">'+item.strCotizacion+'</option>';
                            });
                            $("#objSelectCotizacionBS").append(strOption);
                            $('#objSelectCotizacionBS').val(intIdCotizacion);
                            $("#objSelectCotizacionBS").selectmenu("refresh");
                            document.getElementById('objFilaCotizacion').style.display='inline';
                        }
                        else
                        {
                            document.getElementById('objFilaCotizacion').style.display='none';
                        }
                    }
                    else
                    {
                        document.getElementById('objFilaCotizacion').style.display='none';
                    }
                }
                else
                {
                    document.getElementById('objFilaCotizacion').style.display='none';
                }
            },
            failure: function (response)
            {
                Ext.Msg.alert('Error', 'Ocurrió un error. Por favor comuníquese con Sistemas.');
            }
        });
    }