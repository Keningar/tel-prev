$(document).ready(function () {
    $(".spinner_hilo").hide();
    $(".spinner_elemento").hide();
    $(".spinner_insertar").hide();
    $("#elementoActivo").children("option").remove();
    $('#elementoActivo').empty(); 
    $('#elementoActivo').val(null); 
    cargarRutas();
    cargarElementoPasivo()
    $(document).on( 'change','#ruta', function (e) { 
        $("#elementoPrevio").children("option").remove();
        $("#elementoSiguiente").children("option").remove();
        cargarTramo(e.target.value);
    });
    $(document).on( 'keyup','#elementoActivo', function (e) { 
        buscarElemento(e.target.value);
    });
    $(document).on( 'click','#limpiar_agregar', function () {
        limpiarForm();
        cargarRutas();
        cargarElementoPasivo()
    });

    $(document).on( 'click','#buscar', function (e) {
        elementoPrevio    = $('#elementoPrevio').val(); 
        elementoSiguiente = $('#elementoSiguiente').val(); 

        if(elementoPrevio > 0 && elementoSiguiente > 0)
        {
            $("#infoHilo").dataTable().fnDestroy();
            $('#modalMensajes .modal-body').html('Solo debe estar seleccionado un elemento');
            $('#modalMensajes').modal({show: true});  
        }
        if(elementoPrevio == 0 && elementoSiguiente == 0)
        {
            $("#infoHilo").dataTable().fnDestroy();
            $('#modalMensajes .modal-body').html('Selccionar un elemento');
            $('#modalMensajes').modal({show: true});   
        }
        if(elementoPrevio > 0 && elementoSiguiente == 0)
        {
            buscarElementoInicio(elementoPrevio);
        };
        if(elementoSiguiente > 0 && elementoPrevio == 0)
        {
            buscarElementoFin();
        } 
       
    });
    $(document).on( 'click','#insertar', function (e) {
        nombreElementoInicio = $('#elementoPrevio').val(); 
        nombreElementoFin = $('#elementoSiguiente').val(); 
        nombreElementoInsertar = $('#elementoActivo').val(); 
        if(nombreElementoInicio > 0 && nombreElementoFin > 0 && nombreElementoInsertar !== '')
        {
            actualizarHilo();
        }
        else
        {
            $('#modalMensajes .modal-body').html('Debe seleccionar los tres elementos');
            $('#modalMensajes').modal({show: true}); 
        }
    });
});

function limpiarForm() 
    {   
        $('#ruta').val(null); 
        $('#ruta').empty(); 
        $("#ruta").children("option").remove();


        $("#elementoPrevio").children("option").remove();
        $('#elementoPrevio').val(null); 
        $('#elementoPrevio').empty();

        $("#elementoSiguiente").children("option").remove();
        $('#elementoSiguiente').val(null); 
        $('#elementoSiguiente').empty();

        $("#elementoActivoOptions").children("option").remove();
        $('#elementoActivo').empty(); 
        $('#elementoActivo').val(null);
    }  

function cargarRutas()
{  
    $.ajax({
        url: url_getEncontrados,
        method: 'GET',
        async:true,
        data:
        {
            sltTipoElemento:'RUTA',
            strEstado : 'Activo',
            start:0,
            limit:10
        },
        success: function (data) {         
            $('#ruta').select({
                multiple:false
             });
            if(data.encontrados.length >0)
            {
                $("#ruta").append('<option class="col-sm-5" value=0>Seleccione</option>');
            }               
            $.each(data.encontrados, function (id, registro) {
                $("#ruta").append(`<option class="col_sm_5" value="${registro.idElemento}">${registro.nombreElemento}</option>`);
            });
        }
    });
    
}

function cargarTramo(nombreElemento)
{
        $.ajax({
            url: url_getTramos,
            method: 'GET',
            async:true,
            data:
            {
                intElemento: nombreElemento,
                start:0,
                limit:10
            },
            success: function (data) {
                $('#elementoPrevio').select({
                    multiple:false
                 });
                if(data.encontrados.length >0)
                {
                    $("#elementoPrevio").append('<option class="col-sm-5" value=0>Seleccione</option>');
                    $("#elementoSiguiente").append('<option class="col-sm-5" value=0>Seleccione</option>');
                }               
                $.each(data.encontrados, function (id, registro) {
                    if(registro.tipoElementoA == 'CAJA DISPERSION' || registro.tipoElementoA == 'MANGA')
                    {
                        $("#elementoPrevio").append(`<option class="col_sm_5" value="${registro.idElementoA}">${registro.nombreElementoA}</option>`);
                    };
                });
    
                $.each(data.encontrados, function (id, registro) {
                    if(registro.tipoElementoA == 'CAJA DISPERSION' || registro.tipoElementoA == 'MANGA')
                    {
                        $("#elementoSiguiente").append(`<option class="col_sm_5" value="${registro.idElementoA}">${registro.nombreElementoA}</option>`);
                    };
                });
            }
        }); 
}

function cargarElementoPasivo()
{
    $(".spinner_elemento").show();
    dataList =  document.getElementById("elementoActivoOptions");
    $.ajax({
        url: url_getEncontradosPostes,
        method: 'GET',
        async:true,
        data:
        {
            sltTipoElemento: 252,
            start:0,
            limit:10
        },
        success: function (data) {            
            $.each(data.encontrados, function (id, registro) {
                option = document.createElement("option");
                option.value = registro.nombre_elemento;                 
                dataList.append(option); 
                $(".spinner_elemento").hide();
               // $("#elementoActivo").append(`<option  value="${registro.nombreElemento}">${registro.nombreElemento}</option>`);
            });
        }
    }); 
}

function buscarElemento(stlElementoFin)
    {
        if(stlElementoFin == '')
        {
            $(".spinner_elemento").show();
            $("#elementoActivoOptions").children("option").remove();
            cargarElementoPasivo();
        }
        else
        {
            $(".spinner_elemento").show();
            dataList =  document.getElementById("elementoActivoOptions");
            $.ajax({
                url: url_getElementos,
                method: 'GET',
                async:true,
                data:
                {
                    nombreElemento: stlElementoFin,
                    estado: 'Activo',
                    start:0,
                    limit:10
                },
                success: function (data) {            
                    $.each(data.data[0], function (id, registro) {
                        $("#elementoActivoOptions").children("option").remove();
                        option = document.createElement("option");
                        option.value = registro.nombreElemento;                 
                        dataList.append(option); 
                        $(".spinner_elemento").hide();
                    // $("#elementoActivo").append(`<option  value="${registro.nombreElemento}">${registro.nombreElemento}</option>`);
                    });
                }
            }); 
        }
        
}

function buscarElementoInicio(intElementoPrevio)
{
    // $("#spinner_hilo").show();
    start = 0;
    limit = 100;

    $("#infoHilo").dataTable().fnDestroy();
    $('#infoHilo').DataTable({
        dom: "Bfrtip",
        select:true,
        buttons: {
            dom: {
                container:{
                    tag:'div',
                    className:'flexcontent'
                },
                buttonLiner: {
                  tag: null
                }
            },
            buttons: [
                {
                    extend:     'excelHtml5',
                    text:       '<i class="fa fa-file-excel-o"></i>Excel',
                    title:      'Hilos',
                    titleAttr:  'Excel',
                    className:  'btn btn-success btn-sm export excel exportExcelPago',
                }
            ],
            select:true,
        },
        "ajax": {
            "url": enlaceElementoGetEncontrados,
            "type": "GET",
            "data": function (param) {
                    param.intElementoPrevio = intElementoPrevio;
                    //param.intElementoSiguiente = '';
            }
        },
        "searching":true,
        "language": {
            "lengthMenu": "Muestra _MENU_ filas por página",
            "zeroRecords": "Cargando datos...",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay información disponible",
            "infoFiltered": "(filtrado de _MAX_ total filas)",
            "search": "Buscar:",
            "loadingRecords": "Cargando datos..."
        },
        "columns": [
            {"data": "hilo"},
            {"data": "login"},
            {"data": "mangaInicio"},
            {"data": "elementoInicio"},
            {"data": "interfaceIni"}
        ]
    });
    
    $("#buscar").click(function () {
        $('#infoHilo').DataTable().ajax.reload();
    });             
}
function buscarElementoFin()
{
    // $("#spinner_hilo").show();
    start = 0;
    limit = 100;
    intElementoSiguiente = $('#elementoSiguiente').val(); 

    $("#infoHilo").dataTable().fnDestroy();
    $('#infoHilo').DataTable({
        dom: "Bfrtip",
        select:true,
        buttons: {
            dom: {
                container:{
                    tag:'div',
                    className:'flexcontent'
                },
                buttonLiner: {
                  tag: null
                }
            },
            buttons: [
                {
                    extend:     'excelHtml5',
                    text:       '<i class="fa fa-file-excel-o"></i>Excel',
                    title:      'Hilos',
                    titleAttr:  'Excel',
                    className:  'btn btn-success btn-sm export excel exportExcelPago',
                }
            ],
            select:true,
        },
        "ajax": {
            "url": enlaceElementoGetEncontrados,
            "type": "GET",
            "data": function (param) {
                    //param.intElementoPrevio = '';
                    param.intElementoSiguiente = intElementoSiguiente;           
            }
        },
        "searching":true,
        "language": {
            "lengthMenu": "Muestra _MENU_ filas por página",
            "zeroRecords": "Cargando datos...",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "No hay información disponible",
            "infoFiltered": "(filtrado de _MAX_ total filas)",
            "search": "Buscar:",
            "loadingRecords": "Cargando datos..."
        },
        "columns": [
            {"data": "hilo"},
            {"data": "login"},
            {"data": "mangaFin"},
            {"data": "elementoFin"},
            {"data": "interfaceFin"}
        ]
    });
    
    $("#buscar").click(function () {
        $('#infoHilo').DataTable().ajax.reload();
    });             
}

function actualizarHilo()
{
    nombreElementoInicio = $('#elementoPrevio').val(); 
    nombreElementoFin = $('#elementoSiguiente').val(); 
    nombreElementoInsertar = $('#elementoActivo').val(); 
    $("#spinner_insertar").show();
    $.ajax({
        url: url_putHilos,
        method: 'GET',
        async:true,
        data:
        {
            strElementoInicio: nombreElementoInicio,
            strElementoFin: nombreElementoFin,
            strElementoInsertar: nombreElementoInsertar
        },
        success: function (data) {
           $("#spinner_insertar").hide();
           $('#modalMensajes .modal-body').html(data.data);
           $('#modalMensajes').modal({show: true});         
        },
        failure: function (data) {
            $("#spinner_insertar").hide();
            $('#modalMensajes .modal-body').html(data.data);
            $('#modalMensajes').modal({show: true});            
        }
    }); 
}