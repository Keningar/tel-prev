/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var boolIsTarjeta = false;
Ext.onReady(function() {
    var arrayTabs;

    $('#formaContrato').change(function() {

        $('#tipos-fields-list').empty();
        $('#imagenes-fields-list').empty();

        var formaContrato = $(this).val();
        let list = document.getElementById('imagenes-fields-list').getElementsByTagName('li');
        imagenesCount = list.length;
        tiposCount = list.length;
        var imagenesList = jQuery('#imagenes-fields-list');
        var tiposList = jQuery('#tipos-fields-list');
        var i = 0;
        var name = '__name__';

        // graba la plantilla prototipo
        var newWidget = imagenesList.attr('data-prototype');

        if (formaContrato == 'Contrato Digital') {
            arrayDocumentosSubir = $("#urlDocumento").data("arraydocumento");
            // graba la plantilla prototipo
            for (var property in arrayDocumentosSubir) {
                var object = arrayDocumentosSubir[property];
                var contador = imagenesCount + i;
                var newWidgetTipo =
                    ' <select required="required" id="infodocumentotype_tipos_' + contador + '"' +
                    ' class="campo-obligatorio-select" name="infodocumentotype[tipos][' + contador + ']"> ' +
                    '   <option value="' + object.idTipoDocumento + '" > ' + object.descripcionTipoDocumento + '</option> ' +
                    ' </select>';

                var newWidgetImagen =
                    '<input type="file" id="infodocumentotype_imagenes_' + contador + '"' +
                    'name="infodocumentotype[imagenes][' + contador + ']"' + " data-documento='" + JSON.stringify(object) + "'" + 'class="campo-obligatorio"' +
                    'onchange="readURL(this)"></input>';

                // crea un nuevo elemento lista y lo añade a la lista
                var newLiImagen = jQuery('<li style="height:40px;width: 300px;"></li>').html(newWidgetImagen);
                newLiImagen.appendTo(jQuery('#imagenes-fields-list'));

                var newLiTipo = jQuery('<li style="height:40px"></li>').html(newWidgetTipo);
                newLiTipo.appendTo(jQuery('#tipos-fields-list'));

                i++;
            }
        } else {
            var newWidgetTipoFisico = tiposList.attr('data-prototype');

            newWidget = newWidget.replace(name, imagenesCount);
            newWidget = newWidget.replace(name, imagenesCount);
            newWidgetTipoFisico = newWidgetTipoFisico.replace(name, tiposCount);
            newWidgetTipoFisico = newWidgetTipoFisico.replace(name, tiposCount);
            newWidget = newWidget.replace('"campo-obligatorio"', '"campo-obligatorio" onchange="readURL(this)"');

            // crea un nuevo elemento lista y lo añade a la lista
            var newLiImagenFisico = jQuery('<li style="height:40px;width: 300px;"></li>').html(newWidget);
            newLiImagenFisico.appendTo(jQuery('#imagenes-fields-list'));

            var newLiTipoFisico = jQuery('<li style="height:40px"></li>').html(newWidgetTipoFisico);
            newLiTipoFisico.appendTo(jQuery('#tipos-fields-list'));
        }

    });

    $('#formaAdemdun').change(function() {
    
        $('#tipos-fields-list').empty();
        $('#imagenes-fields-list').empty();
    
        var formaAdemdun = $(this).val();
        let list = document.getElementById('imagenes-fields-list').getElementsByTagName('li'); 
        imagenesCount = list.length;  
        tiposCount    = list.length;        
        var imagenesList = jQuery('#imagenes-fields-list');
        var tiposList    = jQuery('#tipos-fields-list');
        var i = 0;
        var name='__name__';
    
        // graba la plantilla prototipo
        var newWidget = imagenesList.attr('data-prototype');
        
        if(formaAdemdun == 'Contrato Digital')
        { 
           arrayDocumentosSubir = $("#urlDocumento").data( "arraydocumento" );
           
            // graba la plantilla prototipo
            for (var property in arrayDocumentosSubir) 
            {
                var object    = arrayDocumentosSubir[property];
                var contador  = imagenesCount+i;
                var newWidgetTipo =            
                ' <select required="required" id="infodocumentotype_tipos_' +contador+ '"'+
                ' class="campo-obligatorio-select" name="infodocumentotype[tipos][' +contador+ ']"> '+
                '   <option value="'+object.idTipoDocumento+'" > '+object.descripcionTipoDocumento+'</option> '+
                ' </select>';
                var newWidgetImagen =
                '<input type="file" id="infodocumentotype_imagenes_' +contador+ '"'+
                'name="infodocumentotype[imagenes][' +contador+ ']"'+" data-documento='"+JSON.stringify(object)+"'"+'class="campo-obligatorio"'+
                'onchange="readURL(this)"></input>';
    
                // crea un nuevo elemento lista y lo añade a la lista
                var newLiImagen = jQuery('<li style="height:40px;width: 300px;"></li>').html(newWidgetImagen);
                newLiImagen.appendTo(jQuery('#imagenes-fields-list'));
    
                var newLiTipo = jQuery('<li style="height:40px"></li>').html(newWidgetTipo);             
                newLiTipo.appendTo(jQuery('#tipos-fields-list')); 
    
                i++;          
            }
        }
        else
        {
            var newWidgetTipoFisico = tiposList.attr('data-prototype');            
            console.log(formaAdemdun);
            newWidget = newWidget.replace(name, imagenesCount); 
            newWidget = newWidget.replace(name, imagenesCount);           
            newWidgetTipoFisico = newWidgetTipoFisico.replace(name, tiposCount);           
            newWidgetTipoFisico = newWidgetTipoFisico.replace(name, tiposCount);
            newWidget = newWidget.replace('"campo-obligatorio"', '"campo-obligatorio" onchange="readURL(this)"');
            
            // crea un nuevo elemento lista y lo añade a la lista
            var newLiImagenFisico = jQuery('<li style="height:40px;width: 300px;"></li>').html(newWidget);
            newLiImagenFisico.appendTo(jQuery('#imagenes-fields-list'));
            console.log(newLiImagenFisico);
            var newLiTipoFisico = jQuery('<li style="height:40px"></li>').html(newWidgetTipoFisico);             
            newLiTipoFisico.appendTo(jQuery('#tipos-fields-list'));
        }
    
    }); 

    if (($('#nombrePantalla').val() == 'Adendum' && $('#entityAdendum').val() != '') ||
        ($('#nombrePantalla').val() == 'Contrato' && $('#entityContrato').val() != '')) {
        $('#botonPin').show();
        $('#botonAutorizar').show();
        $('#botonGuardar').hide();
    } else {
        $('#botonPin').hide();
        $('#botonAutorizar').hide();
    }

    if ($('#nombrePantalla').val() == "Adendum") {
        arrayTabs = [
            { contentEl: 'tab1', title: 'Datos Principales' },
            { contentEl: 'tab2', title: 'Subir Archivos' }
        ];

        if (strIsGrantedICodPromo == 'S' && $('#entityAdendum').val() == '') {
            arrayTabs.push({ contentEl: 'tab3', title: 'Promociones por Código', itemId: 'tab3' });
        }
    } else {
        arrayTabs = [
            { contentEl: 'tab1', title: 'Datos Principales' },
            { contentEl: 'tab2', title: 'Clausulas' },
            { contentEl: 'tab3', title: 'Datos Adicionales' },
            { contentEl: 'tab4', title: 'Subir Archivos' }
        ];

        if (strIsGrantedICodPromo == 'S' && $('#entityContrato').val() == '') {
            arrayTabs.push({ contentEl: 'tab5', title: 'Promociones por Código', itemId: 'tab5' });
        }

    }

    var tabs = new Ext.TabPanel({
        height: 480,
        renderTo: 'my-tabs',
        activeTab: 0,
        items: arrayTabs,
        defaults: { autoScroll: true },
    });

    var fecha = new Date();
    fecha.setMonth(fecha.getMonth() + 12);

    //wsanchez issue #52
    var fechaHoy = new Date();

    var maintenance_date = new Ext.form.DateField({
        name: 'feFinContratoPost',
        format: 'Y-m-d',
        renderTo: 'feFinContrato',
        value: fecha,
        minValue: fechaHoy,
        editable: false
    });
    var valTipoContratoClausula = $('#tipoContratoClausula').val();
    var tipoContratoClausula    = (typeof valTipoContratoClausula !== 'undefined' && valTipoContratoClausula !== null) ? valTipoContratoClausula : null;

    if ($('#nombrePantalla').val() == 'Adendum')
     {
        $('#CambioPago').hide();
        $('#CambioPagoLabel').hide();
        $('#fechaCuadro').hide();
        var personaEmpresaRolId = $('#infocontratoextratype_personaEmpresaRolId').val();
        $.ajax({
            type: "POST",
            data: "identificacion=" + identificacion + "&personaEmpresaRolId=" + personaEmpresaRolId,
            url: url_valida_contrato_activo,
            success: function(msg1) {
                if (msg1 != 'no') {
                    var objContrato = JSON.parse(msg1);
                    var mensajes = '';
                    //Cargo informacion del Contrato    
                    tarjetaCompleta = objContrato[0].numeroCtaTarjeta;
                    var tarjetaOculta = "xxxxxxxxxx" + tarjetaCompleta.slice(-3);
                    boolIsTarjeta = objContrato[0].boolIsTarjeta;
                    if (boolIsTarjeta) {
                        var anioTarjeta = objContrato[0].anioVencimiento;
                        var mesTarjeta = objContrato[0].mesVencimiento;
                        var today = new Date();
                        var mm = today.getMonth() + 1;
                        var yyyy = today.getFullYear();
                        var boolAnio = Number(anioTarjeta) <= yyyy;
                        var boolMonth = Number(mesTarjeta) < mm;
                        var isExpired = false;
                        if (boolAnio) {
                            isExpired = true;
                            if ((Number(anioTarjeta) == yyyy) && !boolMonth) {
                                isExpired = false;
                            }
                        }

                        if (anioTarjeta == '' && anioTarjeta == null) {
                            mensajes += 'Año vacio, ingrese un año valido.  <br /> ';
                            $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + "");
                            verificacion = false;
                        } else if (mesTarjeta == '' && mesTarjeta == null) {
                            mensajes += 'Mes vacio, ingrese un mes valido.  <br /> ';
                            $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + "");
                            verificacion = false;
                        } else if (isExpired) {
                            $('#infocontratoformapagotype_titularCuenta').attr("disabled", "disabled");
                            $('#infocontratoformapagotype_numeroCtaTarjeta').attr("disabled", "disabled");
                            $('#infocontratoformapagotype_mesVencimiento').attr("disabled", "disabled");
                            $('#infocontratoformapagotype_anioVencimiento').attr("disabled", "disabled");
                            $('#infocontratoformapagotype_codigoVerificacion').attr("disabled", "disabled");
                            $('#botonGuardar').hide();
                            var mesValidaTarjeta = mesTarjeta;
                            mensajes += 'Por favor regularizar.</b>' + 'Tarjeta Caducada: <b>' + anioTarjeta + '/' + mesValidaTarjeta;
                            $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + "");
                            verificacion = false;
                        }
                    }

                    var codigoVerificacion = objContrato[0].codigoVerificacion;
                    if (codigoVerificacion === null) {
                        codigoVerificacion = '000';
                    }


                    $('#infocontratoextratype_tipoContratoId').val(objContrato[0].tipoContratoId);
                    $('#infocontratotype_formaPagoId').val(objContrato[0].formaPagoId);
                    $('#infocontratotype_numeroContratoEmpPub').val(objContrato[0].numeroContratoEmpPub);
                    $('#infocontratoformapagotype_tipoCuentaId').val(objContrato[0].tipoCuentaId);
                    $('#infocontratoformapagotype_bancoTipoCuentaId').val(objContrato[0].bancoTipoCuentaId);
                    $('#infocontratoformapagotype_numeroCtaTarjeta').val(tarjetaOculta);
                    $('#infocontratoformapagotype_titularCuenta').val(objContrato[0].titularCuenta);
                    $('#infocontratoformapagotype_mesVencimiento').val(Number(objContrato[0].mesVencimiento));
                    $('#infocontratoformapagotype_anioVencimiento').val(objContrato[0].anioVencimiento);
                    $('#infocontratoformapagotype_codigoVerificacion').val(codigoVerificacion);
                }
            }
        });  
        if(tipoContratoClausula === 'CONTRATO FÍSICO') {
            $('#formaAdemdun').val('Contrato Fisico').trigger('change');
            $('#formaAdemdun').attr("style", "pointer-events: none;");
        } else if(tipoContratoClausula === 'CONTRATO DIGITAL') {
           $('#formaAdemdun').val('Contrato Digital').trigger('change');
           $('#formaAdemdun').attr("style", "pointer-events: none;");
       }
    }
    else
    {

        $('.divAdendum').hide();
    }

    if (($('#preclientetype_prefijoEmpresa').val() == "MD" ||
    $('#preclientetype_prefijoEmpresa').val() == "EN" )) {
        //No permito editar la FORMA DE PAGO
        var forma_pago_id = $('#infopersonaempformapagotype_formaPagoId').val();
        var tipo_cuenta_id = $('#infopersonaempformapagotype_tipoCuentaId').val();
        var banco_tipo_cuentaid = $('#infopersonaempformapagotype_bancoTipoCuentaId').val();
        $('#infocontratotype_formaPagoId').val(forma_pago_id);
        $('#infocontratoformapagotype_tipoCuentaId').val(tipo_cuenta_id);
        $('#infocontratoformapagotype_bancoTipoCuentaId').val(banco_tipo_cuentaid);

        if ($('#cambioRazonSocial').val() == "N") {
            $("#infocontratotype_formaPagoId").attr("disabled", "disabled");
        }

        //No permito editar TIPO DE CUENTA : Tarjetas o cuenta Ahorro / Corriente
        $("#infocontratoformapagotype_tipoCuentaId").attr("disabled", "disabled");
        //No permito editar BANCO TIPO DE CUENTA
        $('#infocontratoformapagotype_bancoTipoCuentaId').attr("disabled", "disabled");
        //forma de pago
        var seleccion = $('#infocontratotype_formaPagoId').val();
        var proceso = $('#nombrePantalla').val();
        if (seleccion == 3) {
            $('#forma_pago').removeClass("campo-oculto");
            $('#infocontratoformapagotype_tipoCuentaId').attr("required", "required");
            $('#infocontratoformapagotype_bancoTipoCuentaId').attr("required", "required");
            $('#infocontratoformapagotype_numeroCtaTarjeta').attr("required", "required");
            $('#infocontratoformapagotype_titularCuenta').attr("required", "required");
            if (proceso == 'Adendum') {
                $('#infocontratoformapagotype_numeroCtaTarjeta').val("XXXXXXXXX");
                $('#infocontratoformapagotype_titularCuenta').val("XXXXXXXXX");	
            }      
        }    
        else
        {
             $('#forma_pago').addClass("campo-oculto");
             $('#infocontratoformapagotype_tipoCuentaId').removeAttr("required");
             $('#infocontratoformapagotype_bancoTipoCuentaId').removeAttr("required");
             $('#infocontratoformapagotype_numeroCtaTarjeta').removeAttr("required");
             $('#infocontratoformapagotype_titularCuenta').removeAttr("required");      
             $("#infocontratoformapagotype_mesVencimiento").removeAttr('required');
             $("#infocontratoformapagotype_anioVencimiento").removeAttr('required');
             $("#infocontratoformapagotype_codigoVerificacion").removeAttr('required');
                        
        }  

        if( seleccion == 3 )
        {
            $("label[for='infocontratoformapagotype_codigoVerificacion']").hide();
            $('#infocontratoformapagotype_codigoVerificacion').hide();
            //tipo cuenta
            var tipoCuenta = $('#infocontratoformapagotype_tipoCuentaId').val();
            var bcoTipoCtaId = $('#infocontratoformapagotype_bancoTipoCuentaId').val();
            $.ajax({
                type: "POST",
                data: "tipoCuenta=" + tipoCuenta + "&bcoTipoCtaId=" + bcoTipoCtaId,
                url: url_listarBancosAsoc,
                success: function(msg) {
                    if (msg.msg == 'ok') {
                        document.getElementById("infocontratoformapagotype_numeroCtaTarjeta").setAttribute("maxlength", msg.tam);

                        if (msg.tam == "16") {
                            $('#infocontratoformapagotype_mesVencimiento').attr("required", "required");
                            $('#infocontratoformapagotype_anioVencimiento').attr("required", "required");
                            $('#infocontratoformapagotype_codigoVerificacion').attr("required", "required");

                            $("label[for='infocontratoformapagotype_mesVencimiento']").html('* Mes Vencimiento:');
                            $("label[for='infocontratoformapagotype_anioVencimiento']").html('* A&ntilde;o Vencimiento:');
                            $("label[for='infocontratoformapagotype_codigoVerificacion']").html('* Cod. Verificacion:');
                        } else {
                            $("label[for='infocontratoformapagotype_mesVencimiento']").removeClass('campo-obligatorio');
                            $("label[for='infocontratoformapagotype_mesVencimiento']").html('Mes Vencimiento:');
                            $("label[for='infocontratoformapagotype_anioVencimiento']").removeClass('campo-obligatorio');
                            $("label[for='infocontratoformapagotype_anioVencimiento']").html('A&ntilde;o Vencimiento:');
                            $("label[for='infocontratoformapagotype_codigoVerificacion']").removeClass('campo-obligatorio');
                            $("label[for='infocontratoformapagotype_codigoVerificacion']").html('Cod. Verificacion:');
                        }
                    } else {
                        $("label[for='infocontratoformapagotype_mesVencimiento']").removeClass('campo-obligatorio');
                        $("label[for='infocontratoformapagotype_mesVencimiento']").html('Mes Vencimiento:');
                        $("label[for='infocontratoformapagotype_anioVencimiento']").removeClass('campo-obligatorio');
                        $("label[for='infocontratoformapagotype_anioVencimiento']").html('A&ntilde;o Vencimiento:');
                        $("label[for='infocontratoformapagotype_codigoVerificacion']").removeClass('campo-obligatorio');
                        $("label[for='infocontratoformapagotype_codigoVerificacion']").html('Cod. Verificacion:');
                    }
                }
            });
            $("label[for='infocontratoformapagotype_codigoVerificacion']").removeClass('campo-obligatorio');
            $('#infocontratoformapagotype_codigoVerificacion').removeAttr("required");
        }

    }

    $('#infocontratoextratype_tipoContratoId').change(function() {
        var tipoContratoId = $('#infocontratoextratype_tipoContratoId').val();
        $.ajax({
            type: "POST",
            data: "tipoContratoId=" + tipoContratoId,
            url: url_listarClausulas,
            success: function(msg) {
                if (msg.msg == 'ok') {
                    //Llenar el div que presentara las clausulas
                    //Validar que de respuesta sinop solicitarla
                    document.getElementById("tab2").innerHTML = msg.div;
                    //console.log(msg.id);
                } else
                    document.getElementById("tab2").innerHTML = msg.msg;
            }
        });
    });

    valTipoContratoClausula = $('#tipoContratoClausula').val();
    tipoContratoClausula    = (typeof valTipoContratoClausula !== 'undefined' && valTipoContratoClausula !== null) ? valTipoContratoClausula : null;
    if(tipoContratoClausula === 'CONTRATO FÍSICO') {
        $('#formaContrato').val('Contrato Fisico').trigger('change');
        $('#formaContrato').attr("style", "pointer-events: none;")
    } else if(tipoContratoClausula === 'CONTRATO DIGITAL' || $('#cambioRazonSocial').val() == 'S' ) {
        $('#formaContrato').val('Contrato Digital').trigger('change');
        $('#formaContrato').attr("style", "pointer-events: none;")
    }
    var valFormaPagoId = $('#formaPagoId').val();
    var formaPagoId    = (typeof valFormaPagoId !== 'undefined' && valFormaPagoId !== null) ? valFormaPagoId : null;
    if(formaPagoId == 3 && $('#nombrePantalla').val() == 'Contrato') {
        $('#infocontratoformapagotype_numeroCtaTarjeta').val($('#mostrarCuenta').val());
        $('#infocontratoformapagotype_titularCuenta').val($('#titular').val());
        $('#infocontratoformapagotype_anioVencimiento').val($('#anio').val());
        var mesVencimiento = parseInt($('#mes').val());
        $('#infocontratoformapagotype_mesVencimiento').val(mesVencimiento);

        $("label[for='infocontratoformapagotype_numeroCtaTarjeta']").removeClass('campo-obligatorio');
        $('#infocontratoformapagotype_numeroCtaTarjeta').removeAttr("required");
        $('#infocontratoformapagotype_numeroCtaTarjeta').attr("disabled","disabled");

        $("label[for='infocontratoformapagotype_titularCuenta']").removeClass('campo-obligatorio');
        $('#infocontratoformapagotype_titularCuenta').removeAttr("required");
        $('#infocontratoformapagotype_titularCuenta').attr("disabled","disabled");

        $("label[for='infocontratoformapagotype_anioVencimiento']").removeClass('campo-obligatorio');
        $('#infocontratoformapagotype_anioVencimiento').removeAttr("required");
        $('#infocontratoformapagotype_anioVencimiento').attr("disabled","disabled");

        $("label[for='infocontratoformapagotype_mesVencimiento']").removeClass('campo-obligatorio');
        $('#infocontratoformapagotype_mesVencimiento').removeAttr("required");
        $('#infocontratoformapagotype_mesVencimiento').attr("disabled","disabled");

    }
});

//imagenesCount++;
//tiposCount++;
jQuery(document).ready(function() {
    jQuery('#agregar_imagen').click(function() {
        let list = document.getElementById('imagenes-fields-list').getElementsByTagName('li');
        imagenesCount = list.length;
        tiposCount = list.length;
        var imagenesList = jQuery('#imagenes-fields-list');
        var tiposList = jQuery('#tipos-fields-list');
        // graba la plantilla prototipo
        var newWidget = imagenesList.attr('data-prototype');
        var newWidgetTipo = tiposList.attr('data-prototype');
        var name = '__name__';
        newWidget = newWidget.replace(name, imagenesCount);
        newWidgetTipo = newWidgetTipo.replace(name, tiposCount);
        newWidget = newWidget.replace(name, imagenesCount);
        newWidgetTipo = newWidgetTipo.replace(name, tiposCount);
        imagenesCount++;
        tiposCount++;
        // crea un nuevo elemento lista y lo añade a la lista
        var newLi = jQuery('<li style="height:40px;width: 300px;"></li>').html(newWidget);
        newLi.appendTo(jQuery('#imagenes-fields-list'));

        var newLiType = jQuery('<li style="height:40px"></li>').html(newWidgetTipo);
        newLiType.appendTo(jQuery('#tipos-fields-list'));

        return false;
    });

    $('#CambioPago').change(function() {
        if (this.checked) {
            $("#infocontratotype_formaPagoId").removeAttr("disabled");
            $("#infocontratoformapagotype_tipoCuentaId").removeAttr("disabled");
            $('#infocontratoformapagotype_bancoTipoCuentaId').removeAttr("disabled");
            $('#infocontratoformapagotype_numeroCtaTarjeta').val("");
            $('#infocontratoformapagotype_titularCuenta').val("");
        } else {
            $("#infocontratotype_formaPagoId").attr("disabled", "disabled");
            $("#infocontratoformapagotype_tipoCuentaId").attr("disabled", "disabled");
            $('#infocontratoformapagotype_bancoTipoCuentaId').attr("disabled", "disabled");
            $('#infocontratoformapagotype_numeroCtaTarjeta').val("XXXXXXXXX");
            $('#infocontratoformapagotype_titularCuenta').val("XXXXXXXXX");
        }
    });

    $('#infocontratotype_formaPagoId').change(function() {
        var seleccion = $('#infocontratotype_formaPagoId').val();
        if (seleccion == 3) {
            $('#forma_pago').removeClass("campo-oculto");
            $('#infocontratoformapagotype_tipoCuentaId').attr("required", "required");
            $('#infocontratoformapagotype_bancoTipoCuentaId').attr("required", "required");
            $('#infocontratoformapagotype_numeroCtaTarjeta').attr("required", "required");
            $('#infocontratoformapagotype_titularCuenta').attr("required", "required");
        } else {
            $('#forma_pago').addClass("campo-oculto");
            $('#infocontratoformapagotype_tipoCuentaId').removeAttr("required");
            $('#infocontratoformapagotype_bancoTipoCuentaId').removeAttr("required");
            $('#infocontratoformapagotype_numeroCtaTarjeta').removeAttr("required");
            $('#infocontratoformapagotype_titularCuenta').removeAttr("required");
        }
    });

    $('#infocontratoformapagotype_tipoCuentaId').change(function() {
        var tipoCuenta = $('#infocontratoformapagotype_tipoCuentaId').val();
        $.ajax({
            type: "POST",
            data: "tipoCuenta=" + tipoCuenta,
            url: url_listarBancosAsoc,
            success: function(msg) {
                if (msg.msg == 'ok') {
                    //Llenar el div que presentara las clausulas
                    //Validar que de respuesta sinop solicitarla
                    //Debo poner el tamaño de la caja de texto N° tarjeta/cta
                    document.getElementById("infocontratoformapagotype_bancoTipoCuentaId").innerHTML = msg.div;
                    document.getElementById("infocontratoformapagotype_numeroCtaTarjeta").setAttribute("maxlength", msg.tam);
                    if (msg.tam == "16") {
                        $("label[for='infocontratoformapagotype_mesVencimiento']").addClass('campo-obligatorio');
                        $("label[for='infocontratoformapagotype_mesVencimiento']").html('* Mes Vencimiento:');
                        $("label[for='infocontratoformapagotype_anioVencimiento']").addClass('campo-obligatorio');
                        $("label[for='infocontratoformapagotype_anioVencimiento']").html('* A&ntilde;o Vencimiento:');
                        $("label[for='infocontratoformapagotype_codigoVerificacion']").addClass('campo-obligatorio');
                        $("label[for='infocontratoformapagotype_codigoVerificacion']").html('* Cod. Verificacion:');
                    } else {
                        $("label[for='infocontratoformapagotype_mesVencimiento']").removeClass('campo-obligatorio');
                        $("label[for='infocontratoformapagotype_mesVencimiento']").html('Mes Vencimiento:');
                        $("label[for='infocontratoformapagotype_anioVencimiento']").removeClass('campo-obligatorio');
                        $("label[for='infocontratoformapagotype_anioVencimiento']").html('A&ntilde;o Vencimiento:');
                        $("label[for='infocontratoformapagotype_codigoVerificacion']").removeClass('campo-obligatorio');
                        $("label[for='infocontratoformapagotype_codigoVerificacion']").html('Cod. Verificacion:');
                    }
                    //console.log(msg.id);
                } else {
                    document.getElementById("infocontratoformapagotype_bancoTipoCuentaId").innerHTML = msg.msg;
                    $("label[for='infocontratoformapagotype_mesVencimiento']").removeClass('campo-obligatorio');
                    $("label[for='infocontratoformapagotype_mesVencimiento']").html('Mes Vencimiento:');
                    $("label[for='infocontratoformapagotype_anioVencimiento']").removeClass('campo-obligatorio');
                    $("label[for='infocontratoformapagotype_anioVencimiento']").html('A&ntilde;o Vencimiento:');
                    $("label[for='infocontratoformapagotype_codigoVerificacion']").removeClass('campo-obligatorio');
                    $("label[for='infocontratoformapagotype_codigoVerificacion']").html('Cod. Verificacion:');
                }
            }
        });
    });

    $("#infocontratotype_valorContrato").blur(function() {
        if (validaValor()) {
            ocultarDiv('div_valor');
            return true;
        } else {
            mostrarDiv('div_valor');
            $('#div_valor').html('El valor debe ser en formato decimal (Formato:9999.99)');
            //Ext.Msg.alert('Alerta','El valor del pago que desea ingresar no esta en formato decimal');
            $("#infocontratotype_valorContrato").val('');
        }
    });

});

/**
 * Se llama al proceso de autorizar contrato
 *
 * @author Nestor Naula <nnaulal@telconet.ec>
 * @version 1.0 08/11/2020
 * 
 * @author Alex Gomez <algomez@telconet.ec>
 * @version 1.1 10/08/2022 Se agrega nuevo parametro para el paso de array de puntos por proceso de CRS
 * 
 */
function autorizarContrato() {
    var formPanelAutorizar = Ext.create('Ext.form.Panel', {
        bodyPadding: 10,
        waitMsgTarget: true,
        height: 110,
        width: 400,
        layout: 'fit',
        fieldDefaults: {
            labelAlign: 'center',
            msgTarget: 'side'
        },
        items: [{
            xtype: 'textfield',
            hideTrigger: true,
            id: 'pin',
            name: 'pin',
            fieldLabel: 'Ingresar Pin:',
            value: '',
            width: 350
        }, ]
    });
    var btncancelar = Ext.create('Ext.Button', {
        text: 'Cancelar',
        cls: 'x-btn-rigth',
        handler: function() {
            winAutorizarContrato.destroy();
        }
    });
    var btnaceptar = Ext.create('Ext.Button', {
        text: 'Autorizar',
        cls: 'x-btn-left',
        handler: function() {
            var valorPin = Ext.getCmp('pin').value;
            if (valorPin.trim() != '') {
                winAutorizarContrato.destroy();
                var personaEmpresaRolId = $('#infocontratoextratype_personaEmpresaRolId').val();
                var puntoCliente = $('#puntoCliente').val();
                var strProcesoContrato = $('#nombrePantalla').val();
                var strCambioRazonSocial = $('#cambioRazonSocial').val();
                var arrayPuntosCRS = $("#arrayPuntosCRS").val();


                Ext.MessageBox.wait("Autorizando Contrato...", 'Por favor espere');
                $.ajax({
                    url: url_autorizarContrato,
                    type: 'POST',
                    data: {
                        "puntoCliente": puntoCliente,
                        "personaEmpresaRolId": personaEmpresaRolId,
                        "pin": valorPin,
                        "tipo": strProcesoContrato,
                        "cambioRazonSocial": strCambioRazonSocial,
                        "arrayPuntosCRS": arrayPuntosCRS,

                    },
                    success: function(response) {
                        var json = Ext.JSON.decode(response);
                        Ext.MessageBox.hide();
                        if (json.strStatus == 0) {
                            var strUrl = json.strUrl;
                            Ext.Msg.alert("Mensaje", "Proceso realizado", function(btn) {
                                if (btn == 'ok') {
                                    window.location.href = strUrl;
                                }
                            });

                            if ($('#nombrePantalla').val() == 'Contrato') {
                                Ext.defer(function() {
                                    tabs.child('#tab5').tab.hide();
                                }, 1000);
                            } else if ($('#nombrePantalla').val() == 'Adendum') {
                                Ext.defer(function() {
                                    tabs.child('#tab3').tab.hide();
                                }, 1000);
                            }

                        } else {
                            Ext.Msg.alert('Mensaje ', json.strMensaje);
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Error ', errorThrown);
                    },
                    failure: function(response) {
                        Ext.MessageBox.hide();
                        Ext.Msg.alert('Error ', 'Se produjo un error al autorizar contrato');
                    }
                });
            } else {
                Ext.Msg.alert('Alerta ', 'Ingrese el PIN');
            }
        }
    });
    var winAutorizarContrato = Ext.create('Ext.window.Window', {
        title: 'Autorizar Contrato',
        modal: true,
        width: 410,
        height: 110,
        resizable: true,
        layout: 'fit',
        items: [formPanelAutorizar],
        buttonAlign: 'center',
        buttons: [btnaceptar, btncancelar]
    }).show();
}

/**
 * Se llama al proceso de reenvío de Pin
 *
 * @author Nestor Naula <nnaulal@telconet.ec>
 * @version 1.0 08/11/2020
 */
function reenviarPin() {
    var personaEmpresaRolId = $('#infocontratoextratype_personaEmpresaRolId').val();
    var puntoCliente = $('#puntoCliente').val();
    var telefonoCliente = $('#telefonos').val();
    Ext.MessageBox.wait("Reenviando Pin...", 'Por favor espere');
    $.ajax({
        url: url_reenvioPin,
        type: 'POST',
        data: {
            "puntoCliente": puntoCliente,
            "personaEmpresaRolId": personaEmpresaRolId,
            "telefonoCliente": telefonoCliente
        },
        success: function(response) {
            var json = Ext.JSON.decode(response);
            Ext.MessageBox.hide();
            Ext.Msg.alert('Mensaje ', json.strMensaje);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Mensaje ', errorThrown);
        },
        failure: function(response) {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error ', 'Se produjo un error al crear contrato');
        }
    });
}

/**
 * Se proceso data a enviar
 *
 * @author Nestor Naula <nnaulal@telconet.ec>
 * @version 1.0 08/11/2020
 */
function enviarInformacion() {
    mensajes = "";
    mensajes_bin = "";
    var verificacion = true;
    var ingresoFoto = 0;

    $('#mostrarResumen').val('');
    if ($('#infocontratotype_formaPagoId').val() == 3) {
        validarFormulario();
    } else {
        $("#infocontratotype_formaPagoId").attr("enabled", "enabled");
        $("#infocontratoformapagotype_tipoCuentaId").attr("enabled", "enabled");
        $('#infocontratoformapagotype_bancoTipoCuentaId').attr("enabled", "enabled");
        var tipoContratoId = $('#infocontratoextratype_tipoContratoId').val();
        var formaPagoId = $('#infocontratotype_formaPagoId').val();
        var formaContrato = $('#formaContrato').val();
        var proceso = $('#nombrePantalla').val();
        var formaAdemdun   = $('#formaAdemdun').val();

        if (proceso === 'Contrato' && ($('#preclientetype_prefijoEmpresa').val() == "MD" ||
                                      $('#preclientetype_prefijoEmpresa').val() == "EN" ) ) {
            /*Promociones*/
            var strMostrarResumen = 0;
            $('input[name^="codigoMens"]').each(function() {

                var promMens = $(this).val();
                var promMensId = $(this).attr('id');
                var arrayPos = promMensId.split('_');
                var strNombreCod = arrayPos[0];
                var strPosCod = arrayPos[1];
                var validado = $("#" + strNombreCod + "Val_" + strPosCod).val();
                var strMensajes = '';


                if ($("#" + strNombreCod + "_" + strPosCod).val() != '') {
                    strMostrarResumen = strMostrarResumen + 1;
                }

                if (validado == 'N' || validado == 'Mix') {
                    if (validado == 'Mix') {
                        strMensajes = 'Seleccionó Promoción Mix, debe ingresar el código Mix y validar.';
                    } else {
                        strMensajes = 'Debe validar los codigos, para poder guardar el contrato.';
                    }

                    $('#modalMensajes .modal-body').html('Alerta: ' + strMensajes);
                    $('#modalMensajes').modal({ show: true });

                    verificacion = false;
                }

                if (strMostrarResumen > 0) {
                    $('#mostrarResumen').val('S');
                } else {
                    $('#mostrarResumen').val('');
                }
            });

            var strMostrarResumen = 0;
            $('input[name^="codigoIns"]').each(function() {
                var promMensId = $(this).attr('id');
                var arrayPos = promMensId.split('_');
                var strNombreCod = arrayPos[0];
                var strPosCod = arrayPos[1];
                var validado = $("#" + strNombreCod + "Val_" + strPosCod).val();

                if ($("#" + strNombreCod + "_" + strPosCod).val() != '') {
                    strMostrarResumen = strMostrarResumen + 1;
                }

                if (validado == 'N') {


                    $('#modalMensajes .modal-body').html('Alerta: Debe validar los codigos, para poder guardar el contrato. ');
                    $('#modalMensajes').modal({ show: true });

                    verificacion = false;
                }


                if (strMostrarResumen > 0) {
                    $('#mostrarResumen').val('S');
                } else if (strMostrarResumen == 0 && $('#mostrarResumen').val() != 'S') {
                    $('#mostrarResumen').val('');
                }
            });

            var strMostrarResumen = 0;
            $('input[name^="codigoBw"]').each(function() {
                var promMensId = $(this).attr('id');
                var arrayPos = promMensId.split('_');
                var strNombreCod = arrayPos[0];
                var strPosCod = arrayPos[1];
                var validado = $("#" + strNombreCod + "Val_" + strPosCod).val();

                if ($("#" + strNombreCod + "_" + strPosCod).val() != '') {
                    strMostrarResumen = strMostrarResumen + 1;
                }

                if (validado == 'N') {

                    $('#modalMensajes .modal-body').html('Alerta: Debe validar los codigos, para poder guardar el contrato. ');
                    $('#modalMensajes').modal({ show: true });

                    verificacion = false;
                }

                if (strMostrarResumen > 0) {
                    $('#mostrarResumen').val('S');
                } else if (strMostrarResumen == 0 && $('#mostrarResumen').val() != 'S') {
                    $('#mostrarResumen').val('');
                }

            });
        }
        if ((formaAdemdun=="" || formaAdemdun == 'Selecione...') 
        && ($('#preclientetype_prefijoEmpresa').val()=="MD" || $('#preclientetype_prefijoEmpresa').val()=="EN") && proceso == 'Adendum')
        {
            mensajes+='Seleccione forma de Ademdun <br /> ';
            $('#mensaje_validaciones').removeClass('campo-oculto').html(""+mensajes+mensajes_bin+"");  
            verificacion=false;
        }
        if ((formaContrato == "" || formaContrato == 'Selecione...') &&
        ($('#preclientetype_prefijoEmpresa').val() == "MD" ||
        $('#preclientetype_prefijoEmpresa').val() == "EN" ) && proceso == 'Contrato') {
            mensajes += 'Seleccione forma de contrato <br /> ';
            $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
            verificacion = false;
        }
        var validacion = validDocument();
        if (validacion != "") {
            mensajes += validacion;
            $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
            verificacion = false;
        }
        if (tipoContratoId == "" || tipoContratoId <= 0) {
            mensajes += 'Seleccione un Tipo de Contrato <br /> ';
            $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
            verificacion = false;
        }
        if (formaPagoId == "Seleccione" || formaPagoId <= 0) {
            mensajes += 'Seleccione una Forma de Pago para el Contrato <br /> ';
            $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
            verificacion = false;
        }
        if (verificacion) {
            $('button[type="submit"]').removeAttr('disabled');
            $('#mensaje_validaciones').addClass('campo-oculto').html("");
            aprobarClick();
        }

    }
}

function validDocument() {
    var error = "";
    var errorOr = {};
    var grupos = [];
    var validacion = {};
    var descripcion = {};
    $("#imagenes-fields-list").find('[id*="infodocumentotype_imagenes_"]').each(function(index) {
        var documento = $(this).data("documento");
        if ($(this).data("documento")) {
            if (documento.requerido == 'AND_REQ') {
                if (!Boolean($(this).val())) {
                    error += 'imagenes ' + documento.descripcionTipoDocumento + ' es obligatoria <br />';
                }
            }
            if (documento.requerido == 'OR_REQ') {
                if (grupos.indexOf(documento.grupo) === -1) {
                    grupos.push(documento.grupo);
                    descripcion[documento.grupo] = documento.grupoDescripcion;
                    validacion[documento.grupo] = false;
                }
                console.log(Boolean($(this).val()));
                if (validacion[documento.grupo] != true && Boolean($(this).val())) {
                    validacion[documento.grupo] = true;
                }
                if (errorOr[documento.grupo] == null) {
                    errorOr[documento.grupo] = documento.descripcionTipoDocumento;
                } else {
                    errorOr[documento.grupo] = errorOr[documento.grupo] + ', ' + documento.descripcionTipoDocumento;
                }
            }
        }
    });
    if (errorOr != null) {
        grupos.forEach(grupo => {
            if (!validacion[grupo]) {
                error += 'Para el grupo (' + descripcion[grupo] + ') se debe seleccionar una de las siguientes imagenes: ' + errorOr[grupo] + ' <br />';
            }
        });
    }
    return error;
}

function readURL(input) {
    var reader = new FileReader();
    reader.readAsDataURL(input.files[0]);
    reader.onload = function(e) {
        input.setAttribute('value', e.target.result);
    }
}

/**
 * Se llama al proceso de crear Contrato
 *
 * @author Nestor Naula <nnaulal@telconet.ec>
 * @version 1.0 08/11/2020
 */
function aprobarClick() 
{  
    var url = "";
    var validado = $("#validado").val();
    var validadoProd = $("#validadoP").val();
    var mostrarResumen = $('#mostrarResumen').val();

    if (typeof tarjetaCompleta !== 'undefined' && tarjetaCompleta != "") 
    {
        $('#forma_pago').addClass("campo-oculto");
    } 
    var valor=$('#preclientetype_prefijoEmpresa').val() ;
    if(valor=== 'MD' || valor=== 'EN')
    {
        url = url_crearContratoMs;
    } else {
        url = url_crearContratoFisico;
    }


    if ((validado == 'S' || validadoProd == 'S') && mostrarResumen == 'S') {
        $('#modalMensajes2').modal('show');
        $("#btnAceptaCodigos").unbind('click');
        $("#btnAceptaCodigos").click(function() {

            $('#modalMensajes2').modal('toggle');
            guardarContrato(url);
        });
    } else {
        guardarContrato(url);
    }

}

/**
 * Realiza guardado de contrato
 *
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0 22/12/2020
 */
function guardarContrato(url)
{
     Ext.MessageBox.wait("Grabando Datos...", 'Por favor espere');
        var form = $('#formularioContrato');
        var formData = new FormData(form[0]);
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                var json = Ext.JSON.decode(response);
                Ext.MessageBox.hide();
                document.getElementById("urlRedireccionar").setAttribute("value", json.strUrl);
                if( typeof tarjetaCompleta !== 'undefined' && tarjetaCompleta != "")
                {
                    $('#forma_pago').removeClass("campo-oculto");
                    var tarjetaOculta   = "xxxxxxxxxx" + tarjetaCompleta.slice(-3);
                    $('#infocontratoformapagotype_numeroCtaTarjeta').val(tarjetaOculta);
                }
                if(json.strStatus == 0)
                {
                    if(($('#preclientetype_prefijoEmpresa').val() == "TN" ) || json.strContratoFisico == 1)
                    {
                        Ext.Msg.alert("Mensaje", json.strMensaje, function(btn){
                            if(btn=='ok')
                            {
                                window.location.href = json.strUrl;
                            }
                        });
                    }
                    else
                    {
                        Ext.Msg.alert("Mensaje", json.strMensaje, function(btn){
                            if(btn=='ok')
                            {
                                if(typeof json.strFlujoLinkBanca !== 'undefined' && json.strFlujoLinkBanca == 'S'){
                                    window.location.href = json.strUrl;
                                } else {
                                    $('#botonPin').show();
                                    $('#botonAutorizar').show();
                                }
                                $('#botonGuardar').hide();
                            
                                if ($('#nombrePantalla').val() == 'Contrato')
                                {
                                        $('#tab-1020').hide();
                                        $('#panel-1015').hide();
                                        $("#tab-1016-btnEl").click();
                       
                                }
                                else if( $('#nombrePantalla').val() == 'Adendum')
                                {
                                        $('#tab-1020').hide();
                                        $('#panel-1013').hide();
                                        $("#tab-1014-btnEl").click();
                                }
                            }
                        
                    });
                }
            } else {
                Ext.Msg.alert("Mensaje", json.strMensaje, function(btn) {
                    if (btn == 'ok') {
                        window.location.href = strUrl;
                    }
                });
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Mensaje ', errorThrown);
        },
        failure: function(response) {
            Ext.MessageBox.hide();
            Ext.Msg.alert('Error ', 'Se produjo un error al crear contrato');
        },
        cache: false,
        contentType: false,
        processData: false
    });
}

/**
 * Se valida que los datos obligatorios esten ingresados
 *
 * @author Nestor Naula <nnaulal@telconet.ec>
 * @version 1.0 08/11/2020
 *
 * @author Walther Joao Gaibor <wgaibor@telconet.ec>
 * @version 1.1 14/07/2021 - Validar anio y fecha de vencimiento de las tarjetas no sean nulas, ni esten vencidas
 */
function validarFormulario()
{        
    var tipoContratoId       = $('#infocontratoextratype_tipoContratoId').val();	
    var numeroCtaTarjeta     = $('#infocontratoformapagotype_numeroCtaTarjeta').val();	    
    var titularCuenta        = $('#infocontratoformapagotype_titularCuenta').val();	    
    var bancoTipoCuentaId    = $('#infocontratoformapagotype_bancoTipoCuentaId').val();                                
    var anioVencimiento      = $('#infocontratoformapagotype_anioVencimiento').val();  
    var mesVencimiento       = $('#infocontratoformapagotype_mesVencimiento').val(); 
    var codigoVerificacion   = $('#infocontratoformapagotype_codigoVerificacion').val();
    var totalCaracteres      = $('#totalCaracteres').val();
    var intIdCuenta          = $('#infocontratoformapagotype_tipoCuentaId').val();
    var clausulaGuardada     = $('#clausulaGuardada').val();
    var formaContrato        = $('#formaContrato').val(); 	
    var verificacion=true;
    var today           = new Date();
    var mm              = today.getMonth() + 1;
    var yyyy            = today.getFullYear();
    mensajes="";
    mensajes_bin="";
    var ingresoFoto          = 0;
    var proceso              = $('#nombrePantalla').val();
    var strMostrarResumen    = 0;
    /*Códigos Promociones*/
    $('input[name^="codigoMens"]').each(function() {
        var promMens = $(this).val();
        var promMensId = $(this).attr('id');
        var arrayPos = promMensId.split('_');
        var strNombreCod = arrayPos[0];
        var strPosCod = arrayPos[1];
        var validado = $("#" + strNombreCod + "Val_" + strPosCod).val();
        var strMensajes = '';

        if ($("#" + strNombreCod + "_" + strPosCod).val() != '') {
            strMostrarResumen = strMostrarResumen + 1;
        }

        if (validado == 'N' || validado == 'Mix') {
            if (validado == 'Mix') {
                strMensajes = 'Seleccionó Promoción Mix, debe ingresar el código Mix y validar.';
            } else {
                strMensajes = 'Debe validar los codigos, para poder guardar el contrato.';
            }

            $('#modalMensajes .modal-body').html('Existe un error: ' + strMensajes);
            $('#modalMensajes').modal({ show: true });

            verificacion = false;
        }
    });


    $('input[name^="codigoIns"]').each(function() {
        var promMensId = $(this).attr('id');
        var arrayPos = promMensId.split('_');
        var strNombreCod = arrayPos[0];
        var strPosCod = arrayPos[1];
        var validado = $("#" + strNombreCod + "Val_" + strPosCod).val();

        if ($("#" + strNombreCod + "_" + strPosCod).val() != '') {
            strMostrarResumen = strMostrarResumen + 1;
        }

        if (validado == 'N') {
            $('#modalMensajes .modal-body').html('Existe un error: Debe validar los codigos, para poder guardar el contrato. ');
            $('#modalMensajes').modal({ show: true });
            verificacion = false;
        }
    });


    $('input[name^="codigoBw"]').each(function() {
        var promMensId = $(this).attr('id');
        var arrayPos = promMensId.split('_');
        var strNombreCod = arrayPos[0];
        var strPosCod = arrayPos[1];
        var validado = $("#" + strNombreCod + "Val_" + strPosCod).val();

        if ($("#" + strNombreCod + "_" + strPosCod).val() != '') {
            strMostrarResumen = strMostrarResumen + 1;
        }

        if (validado == 'N') {

            $('#modalMensajes .modal-body').html('Existe un error: Debe validar los codigos, para poder guardar el contrato. ');
            $('#modalMensajes').modal({ show: true });

            verificacion = false;
        }
    });

    if (strMostrarResumen > 0) {
        $('#mostrarResumen').val('S');
    } else {
        $('#mostrarResumen').val('');
    }
    /**/
    /*let list = document.getElementById('imagenes-fields-list').getElementsByTagName('li'); 
    for (let i = 0; i <= list.length - 1; i++) {
        let id = 'infodocumentotype_imagenes_'+i;
        let archivobase64 = document.getElementById(id).getAttribute('value'); 
        if (Boolean(archivobase64)) 
        {
            ingresoFoto++;
        }                  
    }*/

    if ((formaContrato == "" || formaContrato == 'Selecione...') &&
    ($('#preclientetype_prefijoEmpresa').val() == "MD" ||
    $('#preclientetype_prefijoEmpresa').val() == "EN" ) && proceso == 'Contrato') {
        mensajes += 'Seleccione forma de contrato <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
        verificacion = false;
    }
    var validacion = validDocument();
    if (validacion != "") {
        mensajes += validacion;
        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
        verificacion = false;
    }

    if (tipoContratoId == "" || tipoContratoId <= 0) {
        mensajes += 'Seleccione un Tipo de Contrato <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
        verificacion = false;
    }

    if (numeroCtaTarjeta == "") {
        mensajes += 'Ingrese el Numero de Cuenta <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
        verificacion = false;
    }

    if (titularCuenta == "") {
        mensajes += 'Ingrese el Titular de Cuenta <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
        verificacion = false;
    }

    if( typeof clausulaGuardada !== 'undefined'  && 
        clausulaGuardada == 'N' &&
        intIdCuenta == 2 &&
        totalCaracteres > 0 && 
        $('#nombrePantalla').val() == 'Contrato' &&
        numeroCtaTarjeta.length != totalCaracteres)
    {
        mensajes+='Número de cuenta invalido. Digitos que debe ingresar: '+totalCaracteres+' <br /> ';
        $('#mensaje_validaciones').removeClass('campo-oculto').html(""+mensajes+mensajes_bin+"");
        verificacion=false;
    }
    
    if(boolIsTarjeta)
    {
        var boolAnio        = Number(anioVencimiento) <= yyyy;
        var boolMonth       = Number(mesVencimiento) < mm;
        var isExpired       = false;
        if(boolAnio)
        {
            isExpired   = true;
            if((Number(anioVencimiento) == yyyy) && !boolMonth){
                isExpired   = false;
            }
        }

        if (anioVencimiento == "" || anioVencimiento == null) {
            mensajes += 'Ingrese Anio de Vencimiento de la tarjeta <br /> ';
            $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
            verificacion = false;
        } else if (mesVencimiento == "" || mesVencimiento == null) {
            mensajes += 'Ingrese el Mes de vencimiento de la tarjeta <br /> ';
            $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
            verificacion = false;
        } else if (isExpired) {
            var mesValidaTarjeta = mesVencimiento;
            mensajes += 'Tarjeta Caducada: <b>' + anioVencimiento + '/' + mesValidaTarjeta + ',</b> Ingresar Nueva fecha ';
            $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + "");
            verificacion = false;
        }
    }

    if (verificacion) {
        //Funcion para obtener si la forma de pago es Tarjeta o Cuenta Bancaria 
        $.ajax({
            type: "POST",
            data: "bancoTipoCuentaId=" + bancoTipoCuentaId,
            url: url_validarFormaPago,
            success: function(msg) {
                if (msg.msg == 'TARJETA') {
                    $('label[for=infocontratoformapagotype_mesVencimiento]').html('* Mes Vencimiento:');
                    $('label[for=infocontratoformapagotype_mesVencimiento]').addClass('campo-obligatorio');
                    $("#infocontratoformapagotype_mesVencimiento").attr('required', 'required');
                    $('label[for=infocontratoformapagotype_anioVencimiento]').html('* A&ntilde;o Vencimiento:');
                    $('label[for=infocontratoformapagotype_anioVencimiento]').addClass('campo-obligatorio');
                    $("#infocontratoformapagotype_anioVencimiento").attr('required', 'required');
                    $('label[for=infocontratoformapagotype_codigoVerificacion]').html('* Codigo Verificaci&oacute;n:');
                    $('label[for=infocontratoformapagotype_codigoVerificacion]').addClass('campo-obligatorio');
                    $("#infocontratoformapagotype_codigoVerificacion").attr('required', 'required');
                    if ((anioVencimiento == "" || anioVencimiento == null) || (mesVencimiento == "" || mesVencimiento == null)) {
                        mensajes += 'Ingrese Anio y mes de Vencimiento de la tarjeta <br /> ';
                        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
                        verificacion = false;
                    }

                    if (codigoVerificacion == "" && codigoVerificacion == null) {
                        mensajes += 'Ingrese el codigo de verificacion de la tarjeta <br /> ';
                        $('#mensaje_validaciones').removeClass('campo-oculto').html("" + mensajes + mensajes_bin + "");
                        verificacion = false;
                    }

                    //Lamada a la validacion del bin
                    if (verificacion) {
                        var numeroCtaTarjeta = $('#infocontratoformapagotype_numeroCtaTarjeta').val();
                        if (numeroCtaTarjeta.includes("xxxxxxxxxx")) {
                            $('#infocontratoformapagotype_numeroCtaTarjeta').val(tarjetaCompleta);
                        }
                        var boolRequiereValidar = $('#boolRequiereValidar').val();
                        if(boolRequiereValidar == 'S')
                        {
                            aprobarClick();
                        }
                        else
                        {
                            validarNumeroTarjetaCuenta();
                        }
                   }
                } 
                else
                {
                    $("#infocontratoformapagotype_mesVencimiento").removeAttr('required');
                    $("#infocontratoformapagotype_anioVencimiento").removeAttr('required');
                    $("#infocontratoformapagotype_codigoVerificacion").removeAttr('required');
                    aprobarClick();
                }
            }
        });
    }
}

$(function() {
    $("#infocontratoformapagotype_numeroCtaTarjeta").keydown(function(event) {
        if (!isNumeric(event)) return false;
    });
    $("#infocontratoformapagotype_mesVencimiento").keydown(function(event) {
        if (!isNumeric(event)) return false;
    });
    $("#infocontratoformapagotype_anioVencimiento").keydown(function(event) {
        if (!isNumeric(event)) return false;
    });
});

function isNumeric(event) {
    return (
        (event.keyCode > 7 && event.keyCode < 10) ||
        (event.keyCode > 47 && event.keyCode < 60) ||
        (event.keyCode > 95 && event.keyCode < 106) ||
        event.keyCode == 17 ||
        event.keyCode == 116
    )
}

function validaValor() {
    //patron= new RegExp("^[0-9]*\.?[0-9]{1,4}$","gi");
    return /^\d+(\.\d+)?$/.test($("#infocontratotype_valorContrato").val());
}

$("#infocontratotype_valorAnticipo").blur(function() {
    if (validaAnticipo() || $("#infocontratotype_valorAnticipo").val() == "") {
        ocultarDiv('div_valor');
        return true;
    } else {
        mostrarDiv('div_valor');
        $('#div_valor').html('El valor del anticipo debe ser en formato decimal (Formato:9999.99)');
        //Ext.Msg.alert('Alerta','El valor del pago que desea ingresar no esta en formato decimal');
        $("#infocontratotype_valorAnticipo").val('');
    }
});

function validaAnticipo() {
    //patron= new RegExp("^[0-9]*\.?[0-9]{1,4}$","gi");
    return /^\d+(\.\d+)?$/.test($("#infocontratotype_valorAnticipo").val());
}

$("#infocontratotype_valorGarantia").blur(function() {
    if (validaGarantia() || $("#infocontratotype_valorGarantia").val() == "") {
        ocultarDiv('div_valor');
        return true;
    } else {
        mostrarDiv('div_valor');
        $('#div_valor').html('El valor de la garantia debe ser en formato decimal (Formato:9999.99)');
        //Ext.Msg.alert('Alerta','El valor del pago que desea ingresar no esta en formato decimal');
        $("#infocontratotype_valorGarantia").val('');
    }
});

function validaGarantia() {
    //patron= new RegExp("^[0-9]*\.?[0-9]{1,4}$","gi");
    return /^\d+(\.\d+)?$/.test($("#infocontratotype_valorGarantia").val());
}

function mostrarDiv(div) {
    capa = document.getElementById(div);
    capa.style.display = 'block';
}

function ocultarDiv(div) {
    capa = document.getElementById(div);
    capa.style.display = 'none';
}