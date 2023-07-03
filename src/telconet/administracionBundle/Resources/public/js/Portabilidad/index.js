Ext.require([
    '*'
]);


/**
 * @author Jessenia Piloso <jpiloso@telconet.ec>
 * @since 1.0
 * @version 1.1 28-12-2022 
 * Se agrega validación para Identificacion y consulta el Rol de la identificacion enviada.
 *
 *
 */
function validaIdentificacion(isValidarIdentificacionTipo)
{  
    var identificacionEsCorrecta = false;
    currenIdentificacion = $(input).val();
    
    if ($('#clientetype_tipoIdentificacion').val() !== 'Seleccione...' && $('#clientetype_tipoIdentificacion').val() !== '')
    {
        if (strNombrePais === 'PANAMA') {
            identificacionEsCorrecta = true;
        }
        if (strNombrePais === 'GUATEMALA' && $('#clientetype_tipoIdentificacion').val() === 'NIT' && currenIdentificacion === 'C/F') {
            identificacionEsCorrecta = true;
        }        
        if (/^[\w]+$/.test(currenIdentificacion) && ($('#clientetype_tipoIdentificacion').val() === 'PAS')) 
        {
            identificacionEsCorrecta = true;
        }
        if (/^\d+$/.test(currenIdentificacion) && ($('#clientetype_tipoIdentificacion').val() === 'RUC' || $('#clientetype_tipoIdentificacion').val() === 'CED'
                                               || $('#clientetype_tipoIdentificacion').val() === 'NIT'
                                               || $('#clientetype_tipoIdentificacion').val() === 'DPI'))
        {
            identificacionEsCorrecta = true;
        }
    }

    if (identificacionEsCorrecta === true) 
    {    
        ocultarDiv('diverrorident');
        if (isValidarIdentificacionTipo && typeof validarIdentificacionTipo == typeof Function)
        {
            validarIdentificacionTipo();
        } 
        $.ajax({
            type: "POST",
            data: "identificacion=" + currenIdentificacion,
            url: url_valida_identificacion,
            beforeSend: function() 
            {
                $('#img-valida-identificacion').attr("src", url_img_loader);
            },
            success: function(msg) 
            {   
                if (msg != '') {                     
                    if (msg == "no") { 
                        $('#img-valida-identificacion').attr("src", url_img_delete);
                        Ext.Msg.alert('Error','La identificación no pertenece a un cliente o Prospecto.', function(btn){
                            if(btn=='ok'){
                                limpiaCampos(numCorreoAgregado);
                            }
                        });  
                    }else {         
                           
                        //existe en base 
                        $('#img-valida-identificacion').attr("title", "identificacion ya existe");
                        $('#img-valida-identificacion').attr("src", url_img_check); 
                        $(input).focus(); 
      
                        //obtiene roles de la persona
                        var obj = JSON.parse(msg);
                        var roles = obj[0].roles;                       
                        var arr_roles = roles.split("|");
                        if(arr_roles.includes('Cliente')) 
                        {
                            $('#clientetype_rol').val('Cliente');

                        }else if(arr_roles.includes('Pre-cliente'))
                        {
                            $('#clientetype_rol').val('Prospecto');
                        }else
                        {
                            $('#img-valida-identificacion').attr("src", url_img_delete);
                            Ext.Msg.alert('Error','La identificación no pertenece a un cliente o Prospecto.', function(btn){
                                if(btn=='ok'){
                                    limpiaCampos(numCorreoAgregado);
                                }
                            });
                        }
                                                            
                    }
                } else {
                    Ext.Msg.alert('Error','No se pudo validar la identificacion ingresada.', function(btn){
                        if(btn=='ok'){
                            limpiaCampos(numCorreoAgregado);
                        }
                    });
                } 
                
            }
        });
    }
    else 
    {
        if ($('#clientetype_tipoIdentificacion').val() === 'Seleccione...' || $('#clientetype_tipoIdentificacion').val() === '') 
        {
            mostrarDiv('dividentificacion');
            $("#dividentificacion").html("Antes de ingresar identificación seleccione tipo de identificación");
        }
        else 
        {
            $("#diverrorident").html("Identificación es incorrecta por favor vuelva a ingresarla, no se permite caracteres especiales");
            mostrarDiv('diverrorident');
            limpiaCampos(numCorreoAgregado);      
        }
        $(input).val("");
    }
}


Ext.onReady(function() 
{
   
    
});



 

function trimAll(texto)
{
    return texto.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ');
}


//Agregar caja de Textos que permite ingresar mas correos
const contenedor = document.querySelector('#correo_0');
const btnAgregar = document.querySelector('#agregar');
// Variable para el total de elementos agregados
let numCorreoAgregado = 1;
/**
 * Método que se ejecuta cuando se da clic al botón de agregar
 */
btnAgregar.addEventListener('click', e => {
    var correoSelect     = document.getElementById("clientetype_correo_electronico_0").value;
    if(correoSelect !== ''){
        let div = document.createElement('div');
        div.innerHTML = `<label>* Correo Electrónico:</label>`+
                        `<input type="text" id="clientetype_correo_electronico_${numCorreoAgregado}" name="clientetype_correo_electronico_${numCorreoAgregado}" required="required" class="campo-obligatorio" onchange=validaCorreo(${numCorreoAgregado})>`+
                        `<img id="img-valida-correo-${numCorreoAgregado}" src="/public/images/check.png"  title="correo valido" width="25" height="25"/>`+
                        `<button type="button" class="addDetalle btn btn-outline-dark btn-sm" onclick="eliminar(this)" title="Eliminar Detalle"><i class="fa fa-trash-o"></i></button>`;
        contenedor.appendChild(div);
        
        numCorreoAgregado++;
    }
    else{
        Ext.Msg.alert('Error', 'Ingrese Correo Electrónico para agregar otro correo'); 
    }
})


/**
 * Método para eliminar el div contenedor del input
 * @param {this} e 
 */
const eliminar = (e) => {
    const divPadre = e.parentNode;
    contenedor.removeChild(divPadre);
    numCorreoAgregado--;
};

function guardar(){ 
    //Valida campos vacios
    if(validaCamposVacios(numCorreoAgregado))
    {   
        var strListaCorreos = '';
        strListaCorreos = obtenerCorreos(numCorreoAgregado);
        Ext.Msg.confirm('Alerta','¿Está seguro de ejecutar el '+
        'derecho de portabilidad del titular '+ $(input).val()+
        ' con correo '+strListaCorreos+'?', function(btn){
        if(btn=='yes'){
           
            ejecutaPortabilidad();
        }
        });
    }
}

function ejecutaPortabilidad(){
    var strListaCorreos = '';
    Ext.MessageBox.wait("Guardando datos...");
    let tipoidentificacion = $('#clientetype_tipoIdentificacion').val() ; 
    let identificacion = $(input).val(); 
    strListaCorreos = obtenerCorreos(numCorreoAgregado);

    $.ajax({
        type: "POST",
        data: { 
            correo: strListaCorreos,
            identificacion: identificacion,
            tipoidentificacion: tipoidentificacion
        },
        url: url_ejecuta_portabilidad,
        success: function(response)
        {
            Ext.MessageBox.hide();
            var obj = JSON.parse(response); 
            var strMsjValidacion = obj.strMsjValidacion; 
            if(strMsjValidacion === "OK")
            {
                Ext.Msg.alert('Información: ', 'Se guardaron los cambios con éxito', function(btn){
                    if(btn=='ok'){
                        limpiaCampos(numCorreoAgregado);
                    }
                });
            }
            else
            {
                Ext.Msg.alert('Error: ', obj.strMsjObservacion, function(btn){
                    if(btn=='ok'){
                        limpiaCampos(numCorreoAgregado);
                    }
                }); 
            }
        },
        failure: function(response)
        {
            var obj = JSON.parse(response); 
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error ',obj.strMsjObservacion); 
            limpiaCampos(numCorreoAgregado);
        }
    });
}



