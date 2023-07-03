$(document).ready(function () {
    $(".spinner_hilo").hide(); 
    
    $(document).on( 'click','#cerrar-rama', function () {
        $("#infoElementoR").dataTable().fnDestroy();
    });
});
async function cargarElementosAsociados(interfaceInicio)
{
    $("#spinner_hilo").show();
    $("#infoElementoR").dataTable().fnDestroy();
    const response = await fetch(enlaceDerivaciones+"?interfaceElementoIniId="+interfaceInicio+"&start="+0+"&limit="+100,
    {
        method: 'GET',
        headers:{
            'Content-Type' : 'application/json'
        }
    })
    jsonResponse = await response.json();
    
    if(response.status==200)
    {
        if(jsonResponse.total == 0)
        {
            $("#spinner_hilo").hide();
            $('#modalMensajes .modal-body').html("No existen Ramificaciones.");
            $('#modalMensajes').modal({show: true});
        }
        else
        {
            $("#spinner_hilo").hide();
            $('#ramificacion').modal({show: true});
            $("#infoElementoR").dataTable().fnDestroy();
        
            $("#infoElementoR").dataTable({
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
                            title:      'Detalle del hilo',
                            titleAttr:  'Excel',
                            className:  'btn btn-success btn-sm export excel exportExcelPago',
                        }
                    ],
                    select:true,
                },
                data:jsonResponse.data,
                language: {
                    lengthMenu: "Muestra _MENU_ filas por página",
                    zeroRecords: "Cargando datos...",
                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "No hay información disponible",
                    infoFiltered: "(filtrado de _MAX_ total filas)",
                    search: "Buscar:",
                    loadingRecords: "Cargando datos..."
                },
                columns: [
                    {data: "orden"},
                    {data: "tipoElementoIni"},
                    {data: "elementoInicio"},
                    {data: "interfaceInicio"},
                    {data: "interfaceFin"},
                    {data: "elementoFin"},
                    {data: "tipoElementoFin"},
                    {data: "login"},
                    {data: "jurisdiccion"}
                ], 
                
            }); 
        }    
    }
    else
    {
        $('#modalMensajes .modal-body').html("Error en la consula conminicarse con sistemas");
        $('#modalMensajes').modal({show: true});
    }

    $("#selectDetalle").click(function () {
        $("#infoElementoR").dataTable().fnDestroy();
        $('#infoElementoR').DataTable().ajax.reload();
    });  
}

async function cargarHilo(idInterface, itrfin)
{
    $("#spinner_hilo").show();
    ini = document.getElementById("interfaceIni");
    fin = document.getElementById("interfaceFin");

    if(ini == null)
    {
        const response = await fetch(enlaceElementoGetEncontrado+"?interfaceElementoFinId="+idInterface+"&itrIni="+itrfin+"&start="+0+"&limit="+100+"&estado="+"Todos",
        {
            method: 'GET',
            headers:{
                'Content-Type' : 'application/json'
            }
        })
         
        jsonResponse = await response.json();

        if(response.status==200)
        {
            $("#spinner_hilo").hide();
            $("#infoElemento").dataTable().fnDestroy();
        
            $("#infoElemento").dataTable({
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
                            title:      'Detalle del hilo',
                            titleAttr:  'Excel',
                            className:  'btn btn-success btn-sm export excel exportExcelPago',
                        }
                    ],
                    select:true,
                },
                data:jsonResponse.data,
                language: {
                    lengthMenu: "Muestra _MENU_ filas por página",
                    zeroRecords: "Cargando datos...",
                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "No hay información disponible",
                    infoFiltered: "(filtrado de _MAX_ total filas)",
                    search: "Buscar:",
                    loadingRecords: "Cargando datos..."
                },
                columns: [
                    {data: "orden"},
                    {data: "tipoElementoIni"},
                    {data: "elementoInicio"},
                    {data: "interfaceInicio"},
                    {data: "interfaceFin"},
                    {data: "elementoFin"},
                    {data: "tipoElementoFin"},
                    {data: "login"},
                    {data: "jurisdiccion"},
                    {data: "opciones",
                    render : function (data){
                    
                        var strDatoRetorna = '';
                        if(data.tipRuta == 'DERIVACION')
                        {
                            strDatoRetorna += '<button onclick="cargarElementosAsociados('+data.itrIni+')"  class="btn btn-outline-dark btn-sm m-1" id="selectDetalle"  title="Ver Derivaciones"  >'+
                            '<em class="fa fa-random" ></em></button>';
                        }
          
                        return strDatoRetorna;}
                    }
                ], 
                
            });
        } 
        else
        {
            $('#modalMensajes .modal-body').html(jsonResponse.response);
            $('#modalMensajes').modal({show: true});
        }
    }
    
    if(fin == null)
    {
        const response = await fetch(enlaceElementoGetEncontrado+"?interfaceElementoIniId="+idInterface+"&itrFin="+itrfin+"&start="+0+"&limit="+100+"&estado="+"Todos",
        {
            method: 'GET',
            headers:{
                'Content-Type' : 'application/json'
            }
        })
         
        jsonResponse = await response.json();

        if(response.status==200)
        {
            $("#spinner_hilo").hide();
            $("#infoElemento").dataTable().fnDestroy();
        
            $("#infoElemento").dataTable({
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
                            title:      'Detalle del hilo',
                            titleAttr:  'Excel',
                            className:  'btn btn-success btn-sm export excel exportExcelPago',
                        }
                    ],
                    select:true,
                },
                data:jsonResponse.data,
                language: {
                    lengthMenu: "Muestra _MENU_ filas por página",
                    zeroRecords: "Cargando datos...",
                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "No hay información disponible",
                    infoFiltered: "(filtrado de _MAX_ total filas)",
                    search: "Buscar:",
                    loadingRecords: "Cargando datos..."
                },
                columns: [
                    {data: "orden"},
                    {data: "tipoElementoIni"},
                    {data: "elementoInicio"},
                    {data: "interfaceInicio"},
                    {data: "interfaceFin"},
                    {data: "elementoFin"},
                    {data: "tipoElementoFin"},
                    {data: "login"},
                    {data: "jurisdiccion"},
                    {data: "opciones",
                    render : function (data){
                    
                        var strDatoRetorna = '';
                        if(data.tipRuta == 'DERIVACION')
                        {
                            strDatoRetorna += '<button onclick="cargarElementosAsociados('+data.itrIni+')"  class="btn btn-outline-dark btn-sm m-1" id="selectDetalle"  title="Ver Derivaciones"  >'+
                            '<em class="fa fa-random" ></em></button>';
                        }
          
                        return strDatoRetorna;}
                    }
                ], 
                
            });
        } 
        else
        {
            $('#modalMensajes .modal-body').html(jsonResponse.response);
            $('#modalMensajes').modal({show: true});
        }
    }
  
    $("#buscar_hilo").click(function () {
        $('#infoElemento').DataTable().ajax.reload();
    }); 
}
