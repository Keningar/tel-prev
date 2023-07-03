
$(document).ready(function () {
    
    /**
    * Obtiene las cuentas bancarias
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.0 28-08-2020
    */   
    $.ajax({
        url: strUrlGetCuentasBancarias,
        method: 'GET',
        success: function (data) {
            $.each(data.cuentas_bancarias, function (id, registro) {
                $("#banco_cuenta").append('<option value=' + registro.id + '>' + registro.nombre + '</option>');
            });
        },
        error: function () {            
            $('#modalMensajes .modal-body').html("No se pueden cargar las cuentas bancarias");
            $('#modalMensajes').modal({show: true});
        }
    });
    $('#banco_cuenta').select2({       
        multiple:true,
        placeholder:'Seleccione cuenta bancaria'
     }); 
     
    $(".spinner_subirArchivo").hide();
    /**
     * Valida Campos requeridos 
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 28-08-2020
     */
    var forms = document.getElementsByClassName('formSubirEstadoCta');
    Array.prototype.filter.call(forms, function (form) {
        form.addEventListener('submit', function (event) {
            if (form.checkValidity() === false)
            {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                $('#modalMensajes .modal-body').html("Por favor llenar campos requeridos.");
                $('#modalMensajes').modal({show: true});
            } else
            {
                form.classList.add('was-validated');
                if (!validaBancoCuenta())
                {
                    $('#modalMensajes .modal-body').html("Debe Seleccionar el banco.");
                    $('#modalMensajes').modal({show: true});

                } else if (!validaArchivo())
                {
                    $('#modalMensajes .modal-body').html("Debe ingresar un archivo válido.");
                    $('#modalMensajes').modal({show: true});
                } else
                {
                    subirEstadoCuenta();
                }
            }
        }, false);
    });
    

    function validaBancoCuenta()
    {
        var strBcoCta = $("#banco_cuenta").val();
        
        if (strBcoCta !== null)
        {
            return true;
        }
  
        return false;
    }    
    function validaArchivo() 
    {
        var strNombreArchivo = $("#estado_cta").val();
        var strExtension     = strNombreArchivo.replace(/^.*\./, '');

        if (strNombreArchivo !== null && (strExtension === 'xlsx'  || strExtension === 'xls'))
        {
            return true;
        }
        return false;
    }  

    $("#btnLimpiarForm").click(function () {
        limpiarFormulario();
    });

    function limpiarFormulario() 
    {
        $('#banco_cuenta').val(null).trigger('change');
        $('#estado_cta').val("");
    }

    /**
     * Función para subir archivo de estado de cuenta.
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 02-09-2020
     */
    function subirEstadoCuenta()
    {
        $(".spinner_subirArchivo").show();
        var parametros = 
            {
                "strIdBcoTipoCta"   : $("#banco_cuenta").val(),
                "strNombreArchivo"  : $("#estado_cta").val()
            };

        $.ajax({

            data: new FormData(document.getElementById("formSubirEstadoCta")),
            contentType: false,
            cache: false,
            processData: false,
            url:  strUrlSubirArchivo,
            type: 'post',           
            success: function (response) {
                $(".spinner_subirArchivo").hide();
                $("#subirArchivo").removeAttr("disabled");
                
                if (response === "OK")
                {                
                    $('#modalMensajes .modal-body').html('Se cargó con éxito el estado de cuenta.');
                    $('#modalMensajes').modal({show: true});
                    limpiarFormulario();
                    $('#modalMensajes').on('hidden.bs.modal', function () {
                      window.location.href = strUrlIndex;
                    })                    
                } 
                else if(response === "ErrorA" || response === "ErrorC")
                {
                    $('#modalMensajes .modal-body').html('Error al procesar de archivo.Favor verificar formato de estado de cuenta.');
                    $('#modalMensajes').modal({show: true});
                }
                else if(response === "ErrorB")
                {
                    $('#modalMensajes .modal-body').html('Extensión de archivo no válida.Favor verificar que sea formato excel.');
                    $('#modalMensajes').modal({show: true});
                }
                else if(response === "ErrorD")
                {
                    $('#modalMensajes .modal-body').html('Existen detalles de estado de cuenta ya registrados. Favor Verificar');
                    $('#modalMensajes').modal({show: true});
                }
                else
                {                
                    $('#modalMensajes .modal-body').html('No se pudo cargar el estado de cuenta. Favor verificar formato de estado de cuenta.');
                    $('#modalMensajes').modal({show: true});
                }
            },
            failure: function (response) {
                $(".spinner_subirArchivo").hide();
                $("#subirArchivo").removeAttr("disabled");
                $('#modalMensajes .modal-body').html("No se pudo cargar el estado de cuenta. Por favor consulte con el Administrador.");
                $('#modalMensajes').modal({show: true});            
            }
        });
    }
    
    
});
