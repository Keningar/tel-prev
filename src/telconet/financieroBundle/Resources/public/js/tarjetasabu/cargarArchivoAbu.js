
$(document).ready(function () {
    
    $('#destinatario').val(strDestinatario);
    $('#destinatario').prop('disabled', 'disabled');
    
    $(".spinner_subirArchivo").hide();       
    var formsArchivo = document.getElementsByClassName('formSubirArchivo');
    Array.prototype.filter.call(formsArchivo, function (form) {
        form.addEventListener('submit', function (event) {           
            if (form.checkValidity() === false)
            {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');                
                $('#modalMensajes .modal-body').html(strMsjSinArchivo);
                $('#modalMensajes').modal({show: true});
            } 
            else
            {
                form.classList.add('was-validated');
                if (!validaArchivoAbu())
                {
                    $('#modalMensajes .modal-body').html(strMsjErrorExt);
                    $('#modalMensajes').modal({show: true});
                } 
                else
                {
                    subirArchivoAbu();
                }
            }
        }, false);
    });    
        
    function validaArchivoAbu() 
    {        
        var strNombreArchivo = $("#archivo_abu").val();
        var strExtension     = strNombreArchivo.replace(/^.*\./, '');

        if (strNombreArchivo !== null)
        {
            for(var i = 0; i < arrayExtensiones.length; i++)
            {
                if(strExtension === arrayExtensiones[i])
                {
                    return true;
                }
            }            
        }
        
        return false;
    }

    $("#btnLimpiarForm").click(function () {
        limpiarFormulario();
    });

    function limpiarFormulario() 
    {
        $('#archivo_abu').val("");
    }
    
     /**
     * Función para subir archivo Abu.
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.0 13-09-2022
     */
    function subirArchivoAbu()
    {
        $(".spinner_subirArchivo").show();
        $('#destinatario').prop('disabled', false);
        $.ajax({

            data: new FormData(document.getElementById("formSubirArchivo")),
            contentType: false,
            cache: false,
            processData: false,
            url:  strUrlSubirArchivo,
            type: 'post',           
            success: function (response) {
                $(".spinner_subirArchivo").hide();
                $("#btnSubirArchivo").removeAttr("disabled");
                
                if (response === "OK")
                {                
                    $('#modalMensajes .modal-body').html('Se ha generado un proceso masivo para actualizacion del archivo ABU, una vez procesado le llegara un email');
                    $('#modalMensajes').modal({show: true});
                    limpiarFormulario();
                    $('#modalMensajes').on('hidden.bs.modal', function () {
                      window.location.href = strUrlIndex;
                    })                    
                } 
                else
                {                
                    $('#modalMensajes .modal-body').html(response);
                    $('#modalMensajes').modal({show: true});
                }
            },
            failure: function (response) {
                $(".spinner_subirArchivo").hide();
                $("#btnSubirArchivo").removeAttr("disabled");
                $('#modalMensajes .modal-body').html("Error al cargar archivo excel. Favor verificar.");
                $('#modalMensajes').modal({show: true});            
            }
        });
    }   
    
});
