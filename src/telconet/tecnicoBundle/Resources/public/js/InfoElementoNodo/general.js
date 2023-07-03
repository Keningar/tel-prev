/**
 * 
 * 1.0 version inicial
 * 
 * version 1.1  
 * @author Antonio Ayala <afayala@telconet.ec>
 * Se agregó campo de medidor eléctrico en Datos General
 * 
 */
function validarFormulario()
{        
    
    //*********************************************
    //     Validaciones de Tab Datos General    
    //*********************************************
    
    motivo                  = document.getElementById('telconet_schemabundle_admimotivotype_nombreMotivo').value;
    modelo                  = document.getElementById('telconet_schemabundle_infoelementonodotype_modeloElementoId').value;
    nombre                  = document.getElementById('telconet_schemabundle_infoelementonodotype_nombreElemento').value;
    factTorre               = document.getElementById('cmb_es_edificio').value;
    alturaMax               = document.getElementById('txt_altura_maxima').value;
    claseNodo               = document.getElementById('cmb_clase_nodo').value;
    numeroMedidor           = document.getElementById('txt_numero_medidor').value;
    claseMedidor            = document.getElementById('cmb_clase_medidor').value;
    tipoMedidor             = document.getElementById('cmb_tipo_medidor').value;
        
    contTipoMedio = 0;    
    tipoMedio = "";
    var cicloMantenimiento = '';
    var fechaMantenimiento = '';
    $('input[name="chk_tipo_medio"]:checked').each(function() {   
        tipoMedio += $(this).val()+"|";              
        contTipoMedio++;
    }); 
    nombre_nodo = "";
    
    if(contTipoMedio!==0)
    {        
        document.getElementById('hd_info_tipoMedio').value = tipoMedio.trim().substring(0, tipoMedio.trim().length-1);
        nombre_nodo = document.getElementById('hd_info_tipoMedio').value;
    }
    else
    {
        document.getElementById('hd_info_tipoMedio').value = "";
    }
    
    //Mensajes de error        
    if(motivo === 'Seleccione')
    {
        Ext.Msg.alert("Advertencia","Debe escoger el Motivo de creación de la Solicitud");
        return false;
    }
    if(nombre === '')
    {
        Ext.Msg.alert("Advertencia","Debe ingresar un nombre para el Nodo");
        return false;
    }
    if(newNodo)
    {
        cicloMantenimiento = document.getElementById('cmb_ciclo_mantenimiento').value;
    }
    else
    {
        fechaMantenimiento  = document.getElementById('txt_fecha_mantenimiento').value;
    }
    
    if(factTorre === 'Seleccione')
    {
        Ext.Msg.alert("Advertencia","Debe indicar se Es Factible Torre");
        return false;
    }
    else
    {
        if(factTorre==='SI')
        {
            if(alturaMax==='')
            {
                Ext.Msg.alert("Advertencia","Debe ingresar la altura Maxima de la Torre");
                return false;
            }
                    
            if(newNodo)
            {
                if (cicloMantenimiento === 'Seleccione')
                {
                    Ext.Msg.alert("Advertencia", "Debe ingresar el ciclo de mantenimiento de la Torre");
                    return false;
                }
            }
            else
            {
                if (fechaMantenimiento === '') 
                {
                    Ext.Msg.alert("Advertencia", "Debe ingresar la fecha del próximo mantenimiento");
                    return false;
                }
            }
            
            tiposNodo = tipoMedio.trim().substring(0, tipoMedio.trim().length - 1).toUpperCase();
            boolRadio = tiposNodo.indexOf('RADIO');
            if (boolRadio === -1)
            {
                Ext.Msg.alert("Advertencia", "Verifique información. El ciclo de mantenimiento solo aplica para Nodos tipo Radio");
                return false;
            }
        }
    }
    if(claseNodo === 'Seleccione')
    {
        Ext.Msg.alert("Advertencia","Debe escoger la Clase del Nodo");
        return false;
    }
    if(contTipoMedio === 0)
    {
        Ext.Msg.alert("Advertencia","Debe escoger el Tipo de Nodo");
        return false;
    }
    if(numeroMedidor === '')
    {
        Ext.Msg.alert("Advertencia","Debe ingresar el número del Medidor");
        return false;
    }
    if(claseMedidor === 'Seleccione')
    {
        Ext.Msg.alert("Advertencia","Debe escoger la Clase del Medidor");
        return false;
    }
    if(tipoMedidor === 'Seleccione')
    {
        Ext.Msg.alert("Advertencia","Debe escoger el Tipo del Medidor");
        return false;
    }
    
    //*********************************************
    //     Validaciones de Tab Datos Local    
    //*********************************************
    
    accesoPermanente  = document.getElementById('telconet_schemabundle_infoelementonodotype_accesoPermanente').value;
    if(newNodo)
    {
        alturaSnm         = document.getElementById('telconet_schemabundle_infoelementonodotype_alturaSnm').value;
    }
    else
    {
        alturaSnm         = document.getElementById('alturaSnm').value;
    }
    region            = document.getElementById('telconet_schemabundle_infoelementonodotype_regionId').value;
    provincia         = document.getElementById('telconet_schemabundle_infoelementonodotype_provinciaId').value;
    canton            = document.getElementById('telconet_schemabundle_infoelementonodotype_cantonId').value;
    parroquia         = document.getElementById('telconet_schemabundle_infoelementonodotype_parroquiaId').value;
    if(newNodo)
    {
        direccion         = document.getElementById('telconet_schemabundle_infoelementonodotype_direccionUbicacion').value;        
    }
    else
    {
        direccion         = document.getElementById('direccionUbicacion').value;        
    }
    
    latitudUbicacion = '';
    longitudUbicacion= '';
    
    if(newNodo)
    {
    
        if(validacionesForm())
        {
            longitudUbicacion = document.getElementById('longitudUbicacion').value;
            latitudUbicacion  = document.getElementById('latitudUbicacion').value;
        }    
        else return false;
    
    }
    else
    {
        longitudUbicacion = document.getElementById('longitudUbicacion').value;
        latitudUbicacion  = document.getElementById('latitudUbicacion').value;        
    }
    
    //Mensajes de Error    
    if(region === '' || region === 'Seleccione')
    {
        Ext.Msg.alert("Advertencia","Debe escoger la Region");
        return false;
    }
    if(provincia === "0" || provincia === '')
    {
        Ext.Msg.alert("Advertencia","Debe escoger la Provincia");
        return false;
    }
    if(canton === "0" || canton === '')
    {
        Ext.Msg.alert("Advertencia","Debe escoger el Canton");
        return false;
    }
    if(parroquia === "0"|| parroquia === '')
    {
        Ext.Msg.alert("Advertencia","Debe escoger la Parroquia");
        return false;
    }
    if(direccion === '')
    {
        Ext.Msg.alert("Advertencia","Debe ingresar la Direccion del Nodo");
        return false;
    }
    if(latitudUbicacion === '')
    {
        Ext.Msg.alert("Advertencia","Debe ingresar la latitud del Nodo");
        return false;
    }
    if(longitudUbicacion === '')
    {
        Ext.Msg.alert("Advertencia","Debe ingresar la longitud del Nodo");
        return false;
    }
    if(alturaSnm === '')
    {
        Ext.Msg.alert("Advertencia","Debe ingresar campo de Altura sobre el nivel del mar");
        return false;
    }
    if(accesoPermanente === 'Seleccione')
    {
        Ext.Msg.alert("Advertencia","Debe escoger si el Nodo es 24x7");
        return false;
    }
    
    //Se valida informacion de espacio
    infoEspacio = obtenerInformacionGridInformacionEspacio();        
        
    if(infoEspacio)
    {        
        document.getElementById('hd_info_espacio').value = infoEspacio;
    }
    else
    {
        document.getElementById('hd_info_espacio').value = "";
        return false;
    }
    
    
    //*********************************************
    //     Validaciones de Tab Datos Contacto 
    //     solo cuando se crea nuevo Nodo   
    //*********************************************
    
    if(newNodo)
    {
        
        tipoContacto   = document.getElementById('cmb_tipo_contacto_nodo').value;
        identificacion = document.getElementById('preclientetype_identificacionCliente').value;
        tipoTributario = document.getElementById('preclientetype_tipoTributario').value;
        nacionalidad   = document.getElementById('preclientetype_nacionalidad').value;
        nombres        = document.getElementById('preclientetype_nombres').value;
        apellidos      = document.getElementById('preclientetype_apellidos').value;
        razonSocial    = document.getElementById('razonSocial').value;        
        tipoIdent      = document.getElementById('preclientetype_tipoIdentificacion').value;        
                
        //Mensajes de Error      
        if(tipoContacto === 'Seleccione')
        {
            Ext.Msg.alert("Advertencia","Debe escoger el tipo de Contacto del Nodo");
            return false;
        }
        if(identificacion === '')
        {
            Ext.Msg.alert("Advertencia","Debe ingresar la identificacion del Contacto");
            return false;
        }
        if(tipoTributario === '')
        {
            Ext.Msg.alert("Advertencia","Debe escoger el tipo tributario del Contacto");
            return false;
        }                
        if(nacionalidad === '')
        {
            Ext.Msg.alert("Advertencia","Debe escoger la nacionalidad del Contacto");
            return false;
        }
        if(nombres === '' && tipoTributario!=='JUR')
        {
            Ext.Msg.alert("Advertencia","Debe ingresar los nombres del Contacto");
            return false;
        }
        if(apellidos === '' && tipoTributario!=='JUR')
        {
            Ext.Msg.alert("Advertencia","Debe ingresar los apellidos del Contacto");
            return false;
        }
        if(razonSocial === '' && tipoTributario === 'JUR')
        {
            Ext.Msg.alert("Advertencia","Debe ingresar la razon social del Contacto");
            return false;
        }

        if(validaFormasContacto())
        {    
            infoContacto = obtenerInformacionGridInformacionContacto();
            
            if(infoContacto)
            {
                document.getElementById('hd_info_contacto').value = infoContacto;
            }   
            else
            {
                document.getElementById('hd_info_contacto').value = "";
                return false;
            }

        }else return false;
    
    }
    
    return true;    
    
   
}

/**
 * 
 * 1.0 version inicial
 * 
 * version 1.1  
 * @author Antonio Ayala <afayala@telconet.ec>
 * 12-08-2020 - Si el contacto existe no se podrán editar los Campos Nombres, Apellidos, Razón Social
 * 
 */

function validaIdentificacion(isValidarIdentificacionTipo) 
{
    yaTieneElRol = false;    
    var identificacionEsCorrecta = false;
    currenIdentificacion = $(input).val();
    
    if ($('#preclientetype_tipoIdentificacion').val() !== 'Seleccione...' && $('#preclientetype_tipoIdentificacion').val() !== '')
    {
        if (/^[\w]+$/.test(currenIdentificacion) && ($('#preclientetype_tipoIdentificacion').val() === 'PAS')) 
        {
            identificacionEsCorrecta = true;
        }
        if (/^\d+$/.test(currenIdentificacion) && ($('#preclientetype_tipoIdentificacion').val() === 'RUC' || 
            $('#preclientetype_tipoIdentificacion').val() === 'CED'))
        {
            identificacionEsCorrecta = true;
        }
    }
    
    if (identificacionEsCorrecta === true) 
    {
        ocultarDiv('diverrorident');
        $.ajax({
            type: "POST",
            data: "identificacion=" + currenIdentificacion,
            url: url_valida_identificacion,
            beforeSend: function() {
                $('#img-valida-identificacion').attr("src", url_img_loader);
            },
            success: function(msg) 
            {
                if (msg !== '') 
                {
                    if (msg === "no") 
                    {
                        flagIdentificacionCorrecta = 1;
                        $('#img-valida-identificacion').attr("title", "Identificacion disponible");
                        $('#img-valida-identificacion').attr("src", url_img_check);
                        store.removeAll();
                        limpiaCampos();
                        habilitaCampos();
                        $("#" + formname + "_yaexiste").val('N');
                        ocultarDiv('divroles');
                    } 
                    else
                    {
                        flagIdentificacionCorrecta = 0;
                        $('#img-valida-identificacion').attr("title", "identificacion ya existe");
                        $('#img-valida-identificacion').attr("src", url_img_delete);
                        $(input).focus();
                        var obj = JSON.parse(msg);                                                

                        //obtiene roles de la persona
                        var roles = obj[0].roles;                        
                        arr_roles = roles.split("|");
                        
                        for (var i = 0; i < arr_roles.length; i++) 
                        {                                                                            
                            if (rol === arr_roles[i]) 
                            {
                                yaTieneElRol = true;
                            }
                        }

                        if (yaTieneElRol) 
                        {                            
                            $("#divroles").html("Identificacion ya existente con rol : " + rol + ", los datos seran cargados");
                            mostrarDiv('divroles');
                            $("#" + formname + "_yaexisteRol").val('S');
                            //Si ya existe entonces no se puede editar los campos Nombres, Apellidos, Razón Social
                            $('#' + formname + '_nombres').attr('readonly', 'readonly');
                            $('#' + formname + '_apellidos').attr('readonly', 'readonly');
                            $('#razonSocial').attr('readonly', 'readonly');
                        }
                        else
                        {
                            $("#divroles").html("Identificacion ya existente. Los datos seran cargados en el formulario para que sea ingresarlo como " + rol + ".");
                            mostrarDiv('divroles');
                            $("#" + formname + "_yaexisteRol").val('N');
                            //Si ya existe entonces no se puede editar los campos Nombres, Apellidos, Razón Social
                            $('#' + formname + '_nombres').attr('readonly', 'readonly');
                            $('#' + formname + '_apellidos').attr('readonly', 'readonly');
                            $('#razonSocial').attr('readonly', 'readonly');
                        }

                        $("#" + formname + "_nombres").val(obj[0].nombres);
                        $("#" + formname + "_apellidos").val(obj[0].apellidos);
                        $("#razonSocial").val(obj[0].razonSocial);
                        $("#" + formname + "_tipoIdentificacion").val(obj[0].tipoIdentificacion);
                        $("#" + formname + "_tipoTributario").val(obj[0].tipoTributario);
                        $("#" + formname + "_nacionalidad").val(obj[0].nacionalidad);
                                                                                               
                        store.removeAll();
                        store.load({params: {personaid: obj[0].id}});
                        $("#" + formname + "_yaexiste").val('S');
                        $("#" + formname + "_id").val(obj[0].id);
                        
                        esTipoNatural();
                    }

                }
                else
                {                    
                    Ext.Msg.alert('Error', "No se pudo validar la identificacion ingresada.");                                                            
                }
                if (isValidarIdentificacionTipo && typeof validarIdentificacionTipo == typeof Function)
                {
                    validarIdentificacionTipo();
                }
            }
        });
    }
    else {
        if ($('#preclientetype_tipoIdentificacion').val() === 'Seleccione...' || $('#preclientetype_tipoIdentificacion').val() === '') {
            mostrarDiv('dividentificacion');
            $("#dividentificacion").html("Antes de ingresar identificacion seleccione tipo de identificacion");
        }
        else {
            $("#diverrorident").html("Identificacion es incorrecta por favor vuelva a ingresarla, no se permite caracteres especiales");
            mostrarDiv('diverrorident');
        }
        $(input).val("");
    }

}

function deshabilitaCampos() {
    $('#' + formname + '_nombres').attr('readonly', 'readonly');
    $('#' + formname + '_apellidos').attr('readonly', 'readonly');
    $('#' + formname + '_tipoEmpresa').attr('disabled', 'disabled');
    $('#razonSocial').attr('readonly', 'readonly');
    $('#' + formname + '_tipoIdentificacion').attr('disabled', 'disabled');
}

function limpiaCampos() {

    $('#' + formname + '_nombres').val('');
    $('#' + formname + '_apellidos').val('');
    $('#razonSocial').val('');
    $('#' + formname + '_tipoTributario').val('');
    $('#' + formname + '_nacionalidad').val('');
    $('#' + formname + '_estadoCivil').val('');
}

function habilitaCampos() {

    $('#' + formname + '_nombres').removeAttr('readonly');
    $('#' + formname + '_apellidos').removeAttr('readonly');
    $('#razonSocial').removeAttr('readonly');
    $('#' + formname + '_tipoIdentificacion').removeAttr('disabled');
    $('#' + formname + '_tipoTributario').removeAttr('disabled');
    $('#' + formname + '_nacionalidad').removeAttr('disabled');
    
}
function validaIdentificacionCorrecta() {
    if (flagIdentificacionCorrecta == 1) 
    {
        return true;
    } 
    else 
    {
        Ext.Msg.alert("Advertencia","Identificacion ya existente. Favor Corregir para poder ingresar el Nuevo Cliente");        
        $(input).focus();
        return false;
    }
}

function validaFormasContacto() 
{
    var array_telefonos = new Array();
    var array_correos   = new Array();
        
    var telefonosOk = false;
    var correosOk   = false;
    
    var existenCorreosFallidos = false;
    var existenFonosFallidos   = false;    
    var existeFormaContacto    = false;   
    
    for (var i = 0; i < gridContactoNodo.getStore().getCount(); i++)
    {
        var variable = gridContactoNodo.getStore().getAt(i).data;  

        if (variable.formaContacto.toUpperCase().match(/^TELEFONO.*$/)) 
        {                    
            array_telefonos.push(variable.valor);       
            existeFormaContacto = true;
        }
        if (variable.formaContacto.toUpperCase().match(/^CORREO.*$/)) 
        { 
            array_correos.push(variable.valor);                              
            existeFormaContacto = true;
        }               
    }
    
    var valorErroneo = '';
    //Valida que exista al menos una forma de contacto
    if (existeFormaContacto) 
    {
        //Verificar si existen correos con errores
        for (i = 0; i < array_correos.length; i++) 
        {              
            correosOk = validaCorreo(array_correos[i].toLowerCase());
            //Si existe al menos un correo erroneo no deja continuar
            if(!correosOk)
            {
                valorErroneo = array_correos[i];
                existenCorreosFallidos = true;
                break;
            }
        }
        
        if(existenCorreosFallidos)
        {
            if(valorErroneo!=='')
            {
                Ext.Msg.alert("Error", "El correo <b>"+valorErroneo+"</b> contiene errores o está mal formado, por favor corregir.");
            }
            else
            {
                Ext.Msg.alert("Error", "Ingresar el valor del correo a agregar");
            }
            return false;
        }
        
        //Verificar si existen telefonos con errores
        for (i = 0; i < array_telefonos.length; i++) 
        {                
            telefonosOk = validaTelefono(array_telefonos[i]);

            if(!telefonosOk)
            {
                valorErroneo = array_telefonos[i];
                existenFonosFallidos = true;
                break;
            }
        }                
        
        if(existenFonosFallidos)
        {
            if(valorErroneo!=='')
            {
                Ext.Msg.alert("Error", "El Teléfono <b>"+valorErroneo+"</b> está mal formado, por favor corregir.");
            }
            else
            {
                Ext.Msg.alert("Error", "Ingresar el valor del Teléfono a agregar");
            }            
            return false;
        }
        
        return true;
    }
    else
    {
        Ext.Msg.alert("Error", "Debe Ingresar al menos una Forma de Contacto");
        return false;
    }			
}
function validaTelefono(telefono){
    var RegExPattern = Utils.REGEX_FONE_MIN8MAX10;       
    if(telefono.indexOf("593") === 0)
    {
        telefono = telefono.replace("593", "0");
    }
    if ((telefono.match(RegExPattern)) && (telefono.value!='') && (telefono.length > 8)) {
		return true;
    } else {
		return false;
    } 
}

function validaCorreo(correo){
    var RegExPattern = Utils.REGEX_MAIL;
    if ((correo.match(RegExPattern)) && (correo.value!='')) {
        return true;
    } else {
		return false;
    } 
}

function esRuc() {
    if ($('#preclientetype_tipoIdentificacion').val() == 'RUC') 
    {
        $('#preclientetype_identificacionCliente').removeAttr('maxlength');
        $('#preclientetype_identificacionCliente').attr('maxlength', '13');        
    } 
}

function cambiarObligatorio(id_condicion, id_validado, label) {
    if ($('#' + id_condicion).val() != '') {
        $('#' + id_validado).attr('required', 'required');
        $('label[for=' + id_validado + ']').addClass('campo-obligatorio');
        $('label[for=' + id_validado + ']').html('* ' + label);

    } else
    {
        $('#' + id_validado).removeAttr('required');
        $('label[for=' + id_validado + ']').removeClass('campo-obligatorio');
        $('label[for=' + id_validado + ']').html(label);
    }
}

function mostrarDiv(div) {
    capa = document.getElementById(div);
    capa.style.display = 'block';
}
function ocultarDiv(div) {
    capa = document.getElementById(div);
    capa.style.display = 'none';
}

function isNumeric(event) 
{
    e = event.charCode; 
    c = event.keyCode;        

    if( (e > 47 && e < 60) || c === 190 || c === 35 || c === 36 || e === 46 || c === 8 || e === 32 )
    {
        return true;
    }
    else
    {        
        return false;
    }
}

function obtenerInformacionGridInformacionEspacio()
{       
    var array = new Object();
    
    var grid = gridInformacionEspacio;
    
    array['total'] =  grid.getStore().getCount();
    array['data']  = new Array();
    
    if(grid.getStore().getCount()!==0)
    {    
        var array_data = new Array();
        
        for (var i = 0; i < grid.getStore().getCount(); i++)
        {                        
            if(grid.getStore().getAt(i).data.tipoEspacioId === null || grid.getStore().getAt(i).data.tipoEspacioId === "" )
            {
                Ext.Msg.alert("Advertencia","Debe elegir un tipo de Espacio del Nodo");
                return false;
            }
            if(grid.getStore().getAt(i).data.largo === "0")
            {
                Ext.Msg.alert("Advertencia","Existen valores de medida de Largo sin llenar a la Informacion de espacio");
                return false;
            }
            if(grid.getStore().getAt(i).data.ancho === "0")
            {
                Ext.Msg.alert("Advertencia","Existen valores de medida de Ancho sin llenar a la Informacion de espacio");
                return false;
            }
            if(grid.getStore().getAt(i).data.alto === "0")
            {
                Ext.Msg.alert("Advertencia","Existen valores de medida de Alto sin llenar a la Informacion de espacio");
                return false;
            }
            if(grid.getStore().getAt(i).data.valor === "0")
            {
                Ext.Msg.alert("Advertencia","Existen valores de unidad de Valor sin llenar a la Informacion de espacio");
                return false;
            }
            else
            {
               array_data.push(grid.getStore().getAt(i).data);
            }
        }
        array['data'] = array_data;          

        return Ext.JSON.encode(array);     
    }
    else
    {
        Ext.Msg.alert("Advertencia","No ha ingresado la Informacion de espacio del Nodo");
        return false;
    }

}

function obtenerInformacionGridInformacionContacto()
{       
    var array = new Object();
    
    var grid = gridContactoNodo;
    
    array['total'] =  grid.getStore().getCount();
    array['data']  = new Array();
    
    if(grid.getStore().getCount()!==0)
    {    
        var array_data = new Array();
        
        for (var i = 0; i < grid.getStore().getCount(); i++)
        {                                                
            if(grid.getStore().getAt(i).data.formaContacto === null || grid.getStore().getAt(i).data.formaContacto === "" )
            {
                Ext.Msg.alert("Advertencia","Debe elegir una forma de contacto del Nodo");
                return false;
            }
            if(grid.getStore().getAt(i).data.valor === "")
            {
                Ext.Msg.alert("Advertencia","Existen valores de forma de contacto sin llenar");
                return false;
            }            
            else
            {
               array_data.push(grid.getStore().getAt(i).data);
            }
        }
        array['data'] = array_data;          

        return Ext.JSON.encode(array);     
    }
    else
    {
        Ext.Msg.alert("Advertencia","No ha ingresado la Informacion de Contacto del Nodo");
        return false;
    }

}

function verAlturaMaxima()
{            
    if(document.getElementById("cmb_es_edificio").value === 'SI')
    {
        $(".altMax").show();
    }
    else
    {
        $(".altMax").hide();
    }
}

function eliminarSeleccion(datosSelect)
{
    for (var i = 0; i < datosSelect.getSelectionModel().getCount(); i++)
    {
        datosSelect.getStore().remove(datosSelect.getSelectionModel().getSelection()[i]);
    }
}


function presentarRegiones(valor_padre, combo_destino, valor_campo)
{
    var conn = new Ext.data.Connection();

    conn.request
        (
            {
                url: url_buscarRegiones,
                method: 'post',
                params: {},
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);
                    llenarCombo(combo_destino,json.encontrados,'nombre_region','id_region',valor_campo);
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            }
        );
}



function presentarProvincias(valor_padre, combo_destino, valor_campo )
{
    var conn = new Ext.data.Connection();
            
    conn.request
        (
            {
                url: url_buscarProvincia,
                method: 'post',
                params: {idRegion: valor_padre},
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);
                    llenarCombo(combo_destino,json.encontrados,'nombre_provincia','id_provincia',valor_campo);
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            }
        );
}


function presentarCantones(valor_padre, combo_destino, valor_campo)
{
    var conn = new Ext.data.Connection();

    conn.request
        (
            {
                url: url_buscarCanton,
                method: 'post',
                params: {idProvincia: valor_padre},
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);
                    llenarCombo(combo_destino,json.encontrados,'nombre_canton','id_canton',valor_campo);
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            }
        );
}

function presentarParroquias(valor_padre, combo_destino, valor_campo)
{
    var conn = new Ext.data.Connection();
    conn.request
        (
            {
                url: url_buscarParroquia,
                method: 'post',
                params: {idCanton: valor_padre},                
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);
                    llenarCombo(combo_destino,json.encontrados,'nombre_parroquia','id_parroquia',valor_campo);
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            }
        );
}

function llenarCombo(name_id_combo, objetos, valor, id, valor_campo)
{
    var combo = Ext.getDom(name_id_combo); 
    
    while (combo.length > 0)
    {
        combo.removeChild(combo.firstChild);
    }

    try
    {
        combo.add(new Option('-- Seleccione --', '0'), null);
    }
    catch (e)
    { //in IE
        combo.add(new Option('-- Seleccione --', '0'));
    }

    for (var i = 0; i < objetos.length; ++i)
    {
        try
        {
            combo.add(new Option(objetos[i][valor], objetos[i][id]), null);
        }
        catch (e)
        { //in IE
            combo.add(new Option(objetos[i][valor], objetos[i][id]));
        }
    }
    agregarValue(name_id_combo, valor_campo);
}

function agregarValue(campo, valor){
    document.getElementById(campo).value = valor;
}


function existeRecordRelacion(myRecord, grid)
{    
    var existe = false;        
    
    var num = grid.getStore().getCount();        

    for (var i = 0; i < num; i++)
    {
        var canton = grid.getStore().getAt(i).data.tipoEspacioId;                   

        if (canton === myRecord.get('tipoEspacioId'))
        {
            existe = true;
            break;
        }
    }
    return existe;
}

function validarSoloNumeros(me,e)
{
    var charCode = e.getKey();

    if (charCode >= 48 && charCode <= 57)
    {
        me.isValid();
    }
    else if (charCode === 8 || charCode === 46)
    {
        me.isValid();
    }
    else
    {
        e.stopEvent();
    }
}

