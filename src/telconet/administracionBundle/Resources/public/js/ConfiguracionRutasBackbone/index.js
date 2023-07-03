$(document).ready(function () {
    $(".spinner_hilo").hide();
    $(".spinner_elementoPasivo").hide(); 
    $(".spinner_elementoActivo").hide(); 
    $(".spinner_ruta").hide(); 
    $('#ruta').val(null); 
    $('#ruta').empty(); 

    $('#elementoPasivo').empty();  
    $('#elementoPasivo').val(null); 

    $('#elementoActivo').empty(); 
    $('#elementoActivo').val(null); 

    $('#puerto1').empty();  
    $('#puerto2').empty();  
    $('#puerto1').val(null);   
    $('#puerto2').val(null);  

    $('#tipoFibraInicio').empty(); 
    $('#bufferInicio').empty();  
    $('#tipoFibraInicio').val(null); 
    $('#bufferInicio').val(null); 
    //Obtiene los tipos de ruta
    $.ajax({
        url: strUrlGetTipoRuta,
        method: 'GET',
        async:true,
        success: function (data) {
            $('#tipoRuta').select({
                multiple:false
             });
            if(data.data.length >0)
            {
                $("#tipoRuta").append('<option value=0>Seleccione</option>');
            }               
            $.each(data.data, function (id, registro) {
                $("#tipoRuta").append('<option value=' + registro.id + '>' + registro.tipoRuta + '</option>');
            });
        }
    });
    //obtiene los elementos pasivos
    $.ajax({
        url: getTiposElementos,
        method: 'GET',
        async:true,
        data: 
        {
            start:0,
            limit:10,
            estado: 'Todos'
        },
        success: function (data) {
            $('#tipoElementoPasivo').select({
                multiple:false
             });             
            $.each(data.encontrados, function (id,registro) {
                if(registro.nombreTipoElemento == 'CPE')
                {
                    // $("#tipoElementoPasivo").append('<option  value=' + registro.nombreTipoElemento + '>' + registro.nombreTipoElemento + '</option>');
                    $("#tipoElementoActivo").append('<option  value=' + registro.nombreTipoElemento + '>' + registro.nombreTipoElemento + '</option>');
                }   
            });
        }
    });
    //obtiene los elementos activos
    $.ajax({
        url: getTiposElementosBackbone,
        method: 'GET',
        async:true,
        success: function (data) {

            $('#tipoElementoActivo').select({
                multiple:false
             });
            if(data.encontrados.length >0)
            {
                 $("#tipoElementoActivo").append('<option value=0>Seleccione</option>');
                 $("#tipoElementoPasivo").append('<option value=0>Seleccione</option>');
            }               
            $.each(data.encontrados, function (id,registro) {
                if(registro.nombreTipoElemento == 'SWITCH' || registro.nombreTipoElemento == 'OLT' ||
                   registro.nombreTipoElemento == 'ROUTER' || registro.nombreTipoElemento == 'ODF' || registro.nombreTipoElemento == 'CASSETTE')
                   {
                    $("#tipoElementoActivo").append('<option  value=' + registro.nombreTipoElemento + '>' + registro.nombreTipoElemento + '</option>');
                    $("#tipoElementoPasivo").append('<option  value=' + registro.nombreTipoElemento + '>' + registro.nombreTipoElemento + '</option>');
                   }
            });
        }
    });
    cargarRuta();
    $(document).on( 'click','#datos', function () {

        //Evita cierre automático de modal al dar click fuera de él
        $('#staticBackdrop').modal({backdrop: 'static'})
    
        $("#staticBackdrop").draggable({
                handle: ".modal-header"
        });
        $("#staticBackdrop").modal("show"); 
    
    });

    $(document).on( 'click','#modalLogin', function () {

        //Evita cierre automático de modal al dar click fuera de él
        $('#loginM').modal({backdrop: 'static'})
    
        $("#loginM").draggable({
                handle: ".modal-header"
        });
        $("#loginM").modal("show"); 
    
    });
    $(document).on( 'click','#modalHilo', function () {

        //Evita cierre automático de modal al dar click fuera de él
        $('#hiloM').modal({backdrop: 'static'})
    
        $("#hiloM").draggable({
                handle: ".modal-header"
        });
        $("#hiloM").modal("show"); 
    
    });

    //consultar por tipo de ruta
    $(document).on( 'change','#tipoRuta', function (e) { 
        $('#ruta').empty();
        cargarRutas(e.target.value);
        $('#tipoElementoPasivo').prop('disabled', true);
        $('#tipoElementoActivo').prop('disabled', true);
    });
    //consultar por elemento pasivo
    $(document).on( 'change','#tipoElementoPasivo', function (e) { 
        $('#elementoPasivo').empty();
        cargarElementoInicio(e.target.value); 

    });
     //consultar por elemento activo
    $(document).on( 'change','#tipoElementoActivo', function (e) { 
        $('#elementoActivo').empty();
        cargarElementoFin(e.target.value);
    });
     //consultar por puerto elemento pasivo
    $(document).on( 'change','#elementoPasivo', function (e) { 
        $('#puerto1').empty();
        cargarPuertoElementoPasivo(e.target.value);
    });
    $(document).on( 'change','#elementoActivo', function (e) { 
        $('#puerto2').empty();
        cargarPuertoElementoActivo(e.target.value);
    });
    $(document).on( 'click','#cerrar', function () {
        $("#staticBackdrop").modal('hide');
    });
    
    $(document).on( 'click','#cerrarLoginM', function () {
        $("#loginM").modal('hide');
    });

    $(document).on( 'click','#cerrarHiloM', function () {
        $("#hiloM").modal('hide');
    });

    $(document).on( 'click','#limpiar_formulario', function () {
        limpiarForm();
    });
    $(document).on( 'change','#puerto2', function (e) { 
        $('#puerto1').prop('disabled', true);
    });
    $(document).on( 'change','#puerto1', function (e) { 
        $('#puerto2').prop('disabled', true);
    });
    
    // carga los elementos de una ruta
    $(document).on( 'change','#ruta', function (e) { 
        $("#elementoActivoOptions").children("option").remove();
        $("#elementoPasivoOptions").children("option").remove();
        cargarTramo(e.target.value);
    });

     //carga los elementos de una ruta
     $(document).on( 'click','#buscar', function (e) {

        puertoInicio   = $('#puerto1').val();
        puertoFinal    = $('#puerto2').val();
        elementoInicio = $('#elementoPasivo').val();
        elementoFin    = $('#elementoActivo').val();
        $(".spinner_elementoPasivo").hide(); 
        $(".spinner_elementoActivo").hide();  
        if(puertoInicio > 0)
        {
            buscarInterfaceInicio(puertoInicio)
        }
        if(puertoFinal >0)
        {
            buscarInterfaceFin(puertoFinal)
        }
        if(puertoInicio > 0 && puertoFinal > 0)
        {
            buscarAmbosPuertos(puertoInicio,puertoFinal);
        }
        if(elementoInicio !== '' && puertoInicio == 0 && elementoFin == '')
        {
            buscarElementoInicio(elementoInicio);
        }
        if(elementoFin !== '' && puertoFinal == 0 && elementoInicio == '')
        {
            buscarElementoFinal(elementoFin);
        } 
        if(elementoInicio !== '' && elementoFin !== '' && puertoFinal == 0 && puertoInicio == 0)
        {
            buscarAmbosElementos(elementoInicio,elementoFin);
        }
       
    });
    $(document).on( 'click','#cancelar', function (e) { 
        $("#infoHilo").dataTable().fnDestroy();
        $("#infoHilo").find("tr:gt(0)").remove(); 
    });

    $(document).on( 'keyup','#elementoPasivo', function (e) { 
        $('#puerto1').empty();
        buscarElementoIni(e.target.value);
    });

    $(document).on( 'keyup','#elementoActivo', function (e) { 
        $('#puerto2').empty();
        buscarElementoFin(e.target.value);
    });
    $(document).on( 'keyup','#ruta', function (e) { 
        $('#elementoPasivo').empty();
        $('#elementoActivo').empty();
        buscarRuta(e.target.value);
    });
});

function limpiarForm() 
    {
        $('#puerto1').prop('disabled', false);
        $('#puerto2').prop('disabled', false);
        $('#tipoElementoPasivo').prop('disabled', false);
        $('#tipoElementoActivo').prop('disabled', false);
        
        $('#tipoRuta').val(null); 
        $('#tipoElementoPasivo').val(null).trigger('change');        
        $('#tipoElementoActivo').val(null).trigger('change');
        
        $('#ruta').val(null); 
        $('#ruta').empty(); 
        $("#rutaOptions").children("option").remove();
        

        $("#elementoPasivoOptions").children("option").remove();
        $('#elementoPasivo').val(null); 
        $('#elementoPasivo').empty();

        $("#elementoActivoOptions").children("option").remove();
        $('#elementoActivo').empty(); 
        $('#elementoActivo').val(null); 
    
        $('#puerto1').empty();  
        $('#puerto2').empty();  
        $('#puerto1').val(null);   
        $('#puerto2').val(null);  
    
        $('#tipoFibraInicio').empty(); 
        $('#bufferInicio').empty();  
        $('#tipoFibraInicio').val(null); 
        $('#bufferInicio').val(null); 

    }  


function cargarRutas(intTipoRuta)
{  
    $(".spinner_ruta").show(); 
    $("#rutaOptions").children("option").remove();
    dataList =  document.getElementById("rutaOptions")
    $.ajax({
        url: url_getEncontrados,
        method: 'GET',
        async:true,
        data:
        {
            sltClase: intTipoRuta,
            sltTipoElemento:'RUTA',
            strEstado : 'Activo',
            start:0,
            limit:10
        },
        success: function (data) {         
            $.each(data.encontrados, function (id, registro) {
                option = document.createElement("option");
                option.value = registro.nombreElemento;                 
                dataList.append(option); 
                $(".spinner_ruta").hide();  
            });
        }
    });
    
    }

function cargarElementoInicio(stlElementoInicio)
{  
    $(".spinner_elementoPasivo").show(); 
    dataList =  document.getElementById("elementoPasivoOptions");
    $.ajax({
        url: getElementosPorTipo,
        method: 'GET',
        async:true,
        data:
        {
            idServicio: '',
            tipoElemento: stlElementoInicio,
            start:0,
            limit:100
        },
        success: function (data) {  
            if(data.total == 0)
            {
                $(".spinner_elementoPasivo").hide(); 
            }       
            $.each(data.encontrados, function (id, registro) {
                option = document.createElement("option");
                option.value = registro.nombreElemento;                 
                dataList.append(option);  
                $(".spinner_elementoPasivo").hide(); 
                //$("#elementoPasivo").append(`<option value="${registro.nombreElemento}">${registro.nombreElemento}</option>`);
            });
        }
    });
    
    }

function cargarElementoFin(stlElementoFin)
    {
        $(".spinner_elementoActivo").show(); 
        dataList =  document.getElementById("elementoActivoOptions");
        $.ajax({
            url: getElementosPorTipo,
            method: 'GET',
            async:true,
            data:
            {
                idServicio: '',
                tipoElemento: stlElementoFin,
                start:0,
                limit:100
            },
            success: function (data) {  
                if(data.total == 0)
                {
                    $(".spinner_elementoActivo").hide(); 
                }              
                $.each(data.encontrados, function (id, registro) {
                    option = document.createElement("option");
                    option.value = registro.nombreElemento;                 
                    dataList.append(option); 
                    $(".spinner_elementoActivo").hide();
                   // $("#elementoActivo").append(`<option  value="${registro.nombreElemento}">${registro.nombreElemento}</option>`);
                });
            }
        }); 
    }

function cargarPuertoElementoPasivo(stlElementoPasivo)
    {
        $.ajax({
        url: getInterfacesPorElemento,
        method: 'GET',
        async:true,
        data: 
        {
            nombreElemento: stlElementoPasivo,
            start: 0,
            limit:100
        },
        success: function (data) {
            //puerto elemento pasivo
            $('#puerto1').select({
                multiple:false
                });
            if(data.encontrados.length >0)
            {
                $("#puerto1").append('<option value=0>Seleccione</option>');
            }               
            $.each(data.encontrados, function (id,registro) {
                $("#puerto1").append('<option value=' + registro.idInterface + '>' + registro.nombreInterfaceElemento + '</option>');
            }); 
        }
    });
    }

function cargarPuertoElementoActivo(stlElementoActivo)
    {
        $.ajax({
        url: getInterfacesPorElemento,
        method: 'GET',
        async:true,
        data: 
        {
            nombreElemento: stlElementoActivo,
            start: 0,
            limit:100
        },
        success: function (data) {
  
            //puerto elemento activo
            $('#puerto2').select({
                multiple:false
                });
            if(data.encontrados.length >0)
            {
                $("#puerto2").append('<option value=0>Seleccione</option>');

            }               
            $.each(data.encontrados, function (id,registro) { 
                $("#puerto2").append('<option value=' + registro.idInterface + '>' + registro.nombreInterfaceElemento + '</option>');
            });
        }
    });
    }

function cargarRuta()
    {  
        $.ajax({
            url: url_getEncontrados,
            method: 'GET',
            async:true,
            data:
            {
                sltClase: 288,
                sltTipoElemento:'RUTA',
                strEstado : 'Activo',
                start:0,
                limit:10
            },
            success: function (data) {
                $('#rutaAgregar').select({
                    multiple:false
                 });
                if(data.encontrados.length >0)
                {
                    $("#rutaAgregar").append('<option class="col-sm-5" value=0>Seleccione</option>');
                }               
                $.each(data.encontrados, function (id, registro) {
                    $("#rutaAgregar").append(`<option class="col_sm_5" value="${registro.idElemento}">${registro.nombreElemento}</option>`);
                });
            }
            // error: function () {
            //     $('#modalMensajes .modal-body').html("No se pudieron cargar las rutas. Por favor consulte con el Administrador.");
            //     $('#modalMensajes').modal({show: true});
            // }
        });
        
}
function cargarTramo(nombreElemento)
{
    dataListPasivo =  document.getElementById("elementoPasivoOptions");
    dataListActivo =  document.getElementById("elementoActivoOptions");
        $.ajax({
            url: url_getTramos,
            method: 'GET',
            async:true,
            data:
            {
                nombreElemento: nombreElemento,
                start:0,
                limit:40
            },
            success: function (data) {
                   
                $.each(data.data, function (id, registro) {

                    option = document.createElement("option");
                    option.value = registro.nombreElemento;                
                    dataListPasivo.append(option);
                });
                $.each(data.data, function (id, registro) {

                    option = document.createElement("option");
                    option.value = registro.nombreElemento;                
                    dataListActivo.append(option);
                });
            }
        }); 
}

function buscarElementoIni(stlElementoIni)
    {
        if(stlElementoIni == '')
        {
            tipoElemento = $('#tipoElementoPasivo').val();
            $(".spinner_elementoPasivo").show(); 
            cargarElementoInicio(tipoElemento);
        }
        else
        {
            dataList =  document.getElementById("elementoPasivoOptions");
            $.ajax({
                url: url_getElementos,
                method: 'GET',
                async:true,
                data:
                {
                    nombreElemento: stlElementoIni,
                    estado: 'Activo',
                    start:0,
                    limit:10
                },
                success: function (data) {            
                    $.each(data.data[0], function (id, registro) {
                        $("#elementoPasivoOptions").children("option").remove();
                        option = document.createElement("option");
                        option.value = registro.nombreElemento;                 
                        dataList.append(option); 
                        $(".spinner_elementoPasivo").hide(); 
                       // $("#elementoActivo").append(`<option  value="${registro.nombreElemento}">${registro.nombreElemento}</option>`);
                    });
                }
            }); 
        }
}

function buscarElementoFin(stlElementoFin)
    {
        if(stlElementoFin == '')
        {
            tipoElemento = $('#tipoElementoActivo').val();
            $(".spinner_elementoActivo").show();
            cargarElementoFin(tipoElemento);
        }
        else
        {
            $(".spinner_elementoActivo").show();  
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
                        $(".spinner_elementoActivo").hide();   
                    // $("#elementoActivo").append(`<option  value="${registro.nombreElemento}">${registro.nombreElemento}</option>`);
                    });
                }
            }); 
        }
}

function buscarRuta(stlElementoIni)
    {
        if(stlElementoIni == '')
        {
            tipoElemento = $('#tipoRuta').val();
            $(".spinner_ruta").show(); 
            cargarRutas(tipoElemento);
        }
        else
        {
            $(".spinner_ruta").show(); 
            dataList =  document.getElementById("rutaOptions");
            $.ajax({
                url: url_getElementos,
                method: 'GET',
                async:true,
                data:
                {
                    nombreElemento: stlElementoIni,
                    estado: 'Activo',
                    start:0,
                    limit:10
                },
                success: function (data) {  
                    if(data.total == 0)
                    {
                        $(".spinner_ruta").hide(); 
                    }             
                    $.each(data.data[0], function (id, registro) {
                        $("#rutaOptions").children("option").remove();
                        option = document.createElement("option");
                        option.value = registro.nombreElemento;                 
                        dataList.append(option); 
                        $(".spinner_ruta").hide(); 
                    });
                }
            }); 
        }
}
// busquedas por los filtros
function buscarInterfaceInicio(puertoInicio)
{
    //puerto inicio
    $("#infoHilo").dataTable().fnDestroy();
    $("#spinner_hilo").show();
    start = 0;
    limit = 10;
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
                    title:      'Listado_de_Hilos',
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
                    param.interfaceElementoIniId = puertoInicio;
                    param.start = start;
                    param.limit = limit;
                    param.estado= 'Todos';
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
            {"data": "elementoIniNombre"},
            {"data": "interfaceElementoIni"},
            {"data": "hiloNumero"},
            {"data": "hiloColor"},
            {"data": "buffer"},
            {"data": "login"},
            {"data": "elementoFinNombre"},
            {"data": "interfaceElementoFin"},
            {"data": "opciones",
            "render": function (data){
                    var strDatoRetorna = '';
                    if(data.fin == null)
                    {
                        $("#spinner_hilo").hide();
                        strDatoRetorna += '  <a class="btn btn-success btn-sm  m-2" id="limpiar_formulario" href='+editRuta+'?itrFin='+data.itrFin+' >' + '<em class="fa fa-pencil"></em></a>' +
                        '<a class="btn btn-secondary btn-sm  m-2" id="limpiar_formulario" href='+showEnlace+'?idIni='+data.inicio+'&itrFin='+data.itrFin+'><em class="fa fa-bars"></em></a> '
                            return strDatoRetorna;  
                    }
                    else
                    {
                        $("#spinner_hilo").hide();
                        strDatoRetorna += '  <a class="btn btn-success btn-sm  m-2" id="limpiar_formulario" href='+editRuta+'?itrFin='+data.itrFin+' >' + '<em class="fa fa-pencil"></em></a>' +
                        '<a class="btn btn-secondary btn-sm  m-2" id="limpiar_formulario" href='+showEnlace+'?idFin='+data.fin+'&itrFin='+data.itrFin+'><em class="fa fa-bars"></em></a> '
                            return strDatoRetorna; 
                    }            
                }
            }
        ],
        "error": function() {
            $('#modalMensajes .modal-body').html('Enlaces mal relacionados, por favor revisar los enlaces creados o comunicarse con sistemas.');
            $('#modalMensajes').modal({show: true});    
        }

    });
    
    $("#buscar").click(function () {
        $("#spinner_hilo").hide();
        $('#infoHilo').DataTable().ajax.reload();
    }); 
}
function buscarInterfaceFin(puertoFinal)
{
    //puerto final 
    start = 0;
    limit = 10;
    $("#spinner_hilo").show();
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
                    title:      'Listado_de_Hilos',
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
                    param.interfaceElementoFinId = puertoFinal;
                    param.start = start;
                    param.limit = limit;
                    param.estado= 'Todos';
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
            {"data": "elementoIniNombre"},
            {"data": "interfaceElementoIni"},
            {"data": "hiloNumero"},
            {"data": "hiloColor"},
            {"data": "buffer"},
            {"data": "login"},
            {"data": "elementoFinNombre"},
            {"data": "interfaceElementoFin"},
            {"data": "opciones",
            "render": function (data){
                    var strDatoRetorna = '';
                    if(data.fin == null)
                    {
                        $("#spinner_hilo").hide();
                        strDatoRetorna += '  <a class="btn btn-success btn-sm  m-2" id="limpiar_formulario" href='+editRuta+'?itrFin='+data.itrFin+' >' + '<em class="fa fa-pencil"></em></a>' +
                        '<a class="btn btn-secondary btn-sm  m-2" id="limpiar_formulario" href='+showEnlace+'?idIni='+data.inicio+'&itrFin='+data.itrFin+'><em class="fa fa-bars"></em></a> '
                            return strDatoRetorna;  
                    }
                    else
                    {
                        $("#spinner_hilo").hide();
                        strDatoRetorna += '  <a class="btn btn-success btn-sm  m-2" id="limpiar_formulario" href='+editRuta+'?itrFin='+data.itrFin+' >' + '<em class="fa fa-pencil"></em></a>' +
                        '<a class="btn btn-secondary btn-sm  m-2" id="limpiar_formulario" href='+showEnlace+'?idFin='+data.fin+'&itrFin='+data.itrFin+'><em class="fa fa-bars"></em></a> '
                            return strDatoRetorna; 
                    }            
                }
            }
        ],
        "error": function() {
            $('#modalMensajes .modal-body').html('Enlaces mal relacionados, por favor revisar los enlaces creados o comunicarse con sistemas.');
            $('#modalMensajes').modal({show: true});    
        }

    });
    
    $("#buscar").click(function () {
        $("#spinner_hilo").hide();
        $('#infoHilo').DataTable().ajax.reload();
    }); 

}
function buscarAmbosPuertos(puertoInicio,puertoFinal)
{
    start = 0;
    limit = 10;
    $("#spinner_hilo").show();
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
                    title:      'Listado_de_Hilos',
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
                    param.interfaceElementoIniId = puertoInicio;
                    param.interfaceElementoFinId = puertoFinal;
                    param.start = start;
                    param.limit = limit;
                    param.estado= 'Todos';
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
            {"data": "elementoIniNombre"},
            {"data": "interfaceElementoIni"},
            {"data": "hiloNumero"},
            {"data": "hiloColor"},
            {"data": "buffer"},
            {"data": "login"},
            {"data": "elementoFinNombre"},
            {"data": "interfaceElementoFin"},
            {"data": "opciones",
            "render": function (data){
                    var strDatoRetorna = '';
                    if(data.fin == null)
                    {
                        $("#spinner_hilo").hide();
                        strDatoRetorna += '  <a class="btn btn-success btn-sm  m-2" id="limpiar_formulario" href='+editRuta+'?itrFin='+data.itrFin+' >' + '<em class="fa fa-pencil"></em></a>' +
                        '<a class="btn btn-secondary btn-sm  m-2" id="limpiar_formulario" href='+showEnlace+'?idIni='+data.inicio+'&itrFin='+data.itrFin+'><em class="fa fa-bars"></em></a> '
                            return strDatoRetorna;  
                    }
                    else
                    {
                        $("#spinner_hilo").hide();
                        strDatoRetorna += '  <a class="btn btn-success btn-sm  m-2" id="limpiar_formulario" href='+editRuta+'?itrFin='+data.itrFin+' >' + '<em class="fa fa-pencil"></em></a>' +
                        '<a class="btn btn-secondary btn-sm  m-2" id="limpiar_formulario" href='+showEnlace+'?idFin='+data.fin+'&itrFin='+data.itrFin+'><em class="fa fa-bars"></em></a> '
                            return strDatoRetorna; 
                    }            
                }
            }
        ],
        "error": function() {
            $('#modalMensajes .modal-body').html('Enlaces mal relacionados, por favor revisar los enlaces creados o comunicarse con sistemas.');
            $('#modalMensajes').modal({show: true});    
        }

    });
    
    $("#buscar").click(function () {   
        $("#spinner_hilo").hide();        
        $('#infoHilo').DataTable().ajax.reload();
    }); 
    
}
function buscarElementoInicio(elementoInicio)
{
    $("#spinner_hilo").show();
    elementoInicio = $('#elementoPasivo').val();
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
                    title:      'Listado_de_Hilos',
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
                    param.elementoIniNombre = elementoInicio;
                    param.start = start;
                    param.limit = limit;
                    param.estado= 'Todos';
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
            {"data": "elementoIniNombre"},
            {"data": "interfaceElementoIni"},
            {"data": "hiloNumero"},
            {"data": "hiloColor"},
            {"data": "buffer"},
            {"data": "login"},
            {"data": "elementoFinNombre"},
            {"data": "interfaceElementoFin"},
            {"data": "opciones",
            "render": function (data){
                    var strDatoRetorna = '';
                    if(data.fin == null)
                    {
                        $("#spinner_hilo").hide();
                        strDatoRetorna += '  <a class="btn btn-success btn-sm  m-2" id="limpiar_formulario" href='+editRuta+'?itrFin='+data.itrFin+' >' + '<em class="fa fa-pencil"></em></a>' +
                        '<a class="btn btn-secondary btn-sm  m-2" id="limpiar_formulario" href='+showEnlace+'?idIni='+data.inicio+'&itrFin='+data.itrFin+'><em class="fa fa-bars"></em></a> '
                            return strDatoRetorna;  
                    }
                    else
                    {
                        $("#spinner_hilo").hide();
                        strDatoRetorna += '  <a class="btn btn-success btn-sm  m-2" id="limpiar_formulario" href='+editRuta+'?itrFin='+data.itrFin+' >' + '<em class="fa fa-pencil"></em></a>' +
                        '<a class="btn btn-secondary btn-sm  m-2" id="limpiar_formulario" href='+showEnlace+'?idFin='+data.fin+'&itrFin='+data.itrFin+'><em class="fa fa-bars"></em></a> '
                            return strDatoRetorna; 
                    }            
                }
            }
        ],
        "error": function() {
            $('#modalMensajes .modal-body').html('Enlaces mal relacionados, por favor revisar los enlaces creados o comunicarse con sistemas.');
            $('#modalMensajes').modal({show: true});    
        }

    });
    
    $("#buscar").click(function () {
        $("#spinner_hilo").hide();
        $('#infoHilo').DataTable().ajax.reload();
    });             
}
function buscarElementoFinal(elementoFin)
{
    $("#spinner_hilo").show();
    elementoFin = $('#elementoActivo').val();
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
                    title:      'Listado_de_Hilos',
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
                    param.elementoFinNombre = elementoFin;
                    param.start = start;
                    param.limit = limit;
                    param.estado= 'Todos';
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
            {"data": "elementoIniNombre"},
            {"data": "interfaceElementoIni"},
            {"data": "hiloNumero"},
            {"data": "hiloColor"},
            {"data": "buffer"},
            {"data": "login"},
            {"data": "elementoFinNombre"},
            {"data": "interfaceElementoFin"},
            {"data": "opciones",
            "render": function (data){
                    var strDatoRetorna = '';
                    if(data.fin == null)
                    {
                        $("#spinner_hilo").hide();
                        strDatoRetorna += '  <a class="btn btn-success btn-sm  m-2" id="limpiar_formulario" href='+editRuta+'?itrFin='+data.itrFin+' >' + '<em class="fa fa-pencil"></em></a>' +
                        '<a class="btn btn-secondary btn-sm  m-2" id="limpiar_formulario" href='+showEnlace+'?idIni='+data.inicio+'&itrFin='+data.itrFin+'><em class="fa fa-bars"></em></a> '
                            return strDatoRetorna;  
                    }
                    else
                    {
                        $("#spinner_hilo").hide();
                        strDatoRetorna += '  <a class="btn btn-success btn-sm  m-2" id="limpiar_formulario" href='+editRuta+'?itrFin='+data.itrFin+' >' + '<em class="fa fa-pencil"></em></a>' +
                        '<a class="btn btn-secondary btn-sm  m-2" id="limpiar_formulario" href='+showEnlace+'?idFin='+data.fin+'&itrFin='+data.itrFin+'><em class="fa fa-bars"></em></a> '
                            return strDatoRetorna; 
                    }            
                }
            }
        ],
        "error": function() {
            $('#modalMensajes .modal-body').html('Enlaces mal relacionados, por favor revisar los enlaces creados o comunicarse con sistemas.');
            $('#modalMensajes').modal({show: true});    
        }

    });
    
    $("#buscar").click(function () {
        $("#spinner_hilo").hide();
        $('#infoHilo').DataTable().ajax.reload();
    });             
}
function buscarAmbosElementos(elementoInicio,elementoFin)
{
    $("#spinner_hilo").show();
    elementoInicio = $('#elementoPasivo').val();
    elementoFin = $('#elementoActivo').val();
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
                    title:      'Listado_de_Hilos',
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
                    param.elementoIniNombre = elementoInicio;
                    param.elementoFinNombre = elementoFin;
                    param.start = start;
                    param.limit = limit;
                    param.estado= 'Todos';
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
            {"data": "elementoIniNombre"},
            {"data": "interfaceElementoIni"},
            {"data": "hiloNumero"},
            {"data": "hiloColor"},
            {"data": "buffer"},
            {"data": "login"},
            {"data": "elementoFinNombre"},
            {"data": "interfaceElementoFin"},
            {"data": "opciones",
            "render": function (data){
                    var strDatoRetorna = '';
                    if(data.fin == null)
                    {
                        $("#spinner_hilo").hide();
                        strDatoRetorna += '  <a class="btn btn-success btn-sm  m-2" id="limpiar_formulario" href='+editRuta+'?itrFin='+data.itrFin+' >' + '<em class="fa fa-pencil"></em></a>' +
                        '<a class="btn btn-secondary btn-sm  m-2" id="limpiar_formulario" href='+showEnlace+'?idIni='+data.inicio+'&itrFin='+data.itrFin+'><em class="fa fa-bars"></em></a> '
                            return strDatoRetorna;  
                    }
                    else
                    {
                        $("#spinner_hilo").hide();
                        strDatoRetorna += '  <a class="btn btn-success btn-sm  m-2" id="limpiar_formulario" href='+editRuta+'?itrFin='+data.itrFin+' >' + '<em class="fa fa-pencil"></em></a>' +
                        '<a class="btn btn-secondary btn-sm  m-2" id="limpiar_formulario" href='+showEnlace+'?idFin='+data.fin+'&itrFin='+data.itrFin+'><em class="fa fa-bars"></em></a> '
                            return strDatoRetorna; 
                    }            
                }
            }
        ],
        "error": function() {
            $('#modalMensajes .modal-body').html('Enlaces mal relacionados, por favor revisar los enlaces creados o comunicarse con sistemas.');
            $('#modalMensajes').modal({show: true});    
        }

    });
    
    $("#buscar").click(function () {
        $("#spinner_hilo").hide();
        $('#infoHilo').DataTable().ajax.reload();
    });             
}