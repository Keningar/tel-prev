$(document).ready(function () {
    $(".spinner_subirArchivo").hide();  
    // cargar archivo 
    var formsArchivo = document.getElementsByClassName('formSubirArchivo');
    Array.prototype.filter.call(formsArchivo, function (form) {
    form.addEventListener('submit', function (event) {           
        if (form.checkValidity() === false)
        {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');                
            $('#modalMensajes .modal-body').html('No existe el archivo');
            $('#modalMensajes').modal({show: true});
        } 
        else
        {
            form.classList.add('was-validated');
            if (!validaArchivoAbu())
            {
                $('#modalMensajes .modal-body').html('Extensión no valida');
                $('#modalMensajes').modal({show: true});
            } 
            else
            {
                subirArchivoHilos();
            }
        }
    }, false);
    });
});
function subirArchivoHilos()
    {
        $(".spinner_subirArchivo").show();
        $.ajax({

            data: new FormData(document.getElementById("formSubirArchivo")),
            contentType: false,
            cache: false,
            processData: false,
            url:  url_uploadFile, //url para cargar el archivo
            type: 'POST',           
            success: function (response) {
                $(".spinner_subirArchivo").hide();
                $("#btnSubirArchivo").removeAttr("disabled");
                         
                $('#modalMensajes .modal-body').html(response);
                $('#modalMensajes').modal({show: true});                 
            },
            failure: function (response) {
                $(".spinner_subirArchivo").hide();
                $("#btnSubirArchivo").removeAttr("disabled");
                $('#modalMensajes .modal-body').html("Error al cargar archivo excel. Favor verificar.");
                $('#modalMensajes').modal({show: true});            
            }
        });
    }   

function validaArchivoAbu() 
    {        
        var strNombreArchivo = $("#archivo_abu").val();
        var strExtension     = strNombreArchivo.replace(/^.*\./, '');
        var arrayExtensiones = ['csv'];
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