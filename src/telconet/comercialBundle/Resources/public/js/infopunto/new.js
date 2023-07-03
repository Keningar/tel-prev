Ext.require([
    '*'
]);

var esRequerido = (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN' );
var strOpcionIncial='Escoja una opcion';

var esObligatorio = true; 
//control de ocultamiento de puntos de atencion
function ocultarPuntoAtencion()
{
    var cmbOrigen = document.getElementById("strNombreOrigen");
    var strNombreOrigen = cmbOrigen.options[cmbOrigen.selectedIndex].text;
    
    if(strNombreOrigen==="ATC" && prefijoEmpresa === "MD")
    {
        $("#bloquePuntoAtencion").css({display:"table-row"});
    }
    else
    {
        $("#bloquePuntoAtencion").css({display:"none"});
    }

}

function validaLogin(){
    currentLogin=$('#infopuntodatoaldicionaltype_login').val();
    if (currentLogin!="")
    {  
        
        $.ajax({
                type: "POST",
                data: "login=" + currentLogin,
                url: url_valida_login,
                beforeSend: function(){
                    $('#img-valida-login').attr("src",url_img_loader);
                },
                success: function(msg){
                    if (msg != ''){
                        if(msg=="no"){
                            flagLoginCorrecto = 1;
                            $('#img-valida-login').attr("title","login correcto");
                            $('#img-valida-login').attr("src",url_img_check);
                        }
                        if(msg=="si"){
                            flagLoginCorrecto = 0;
                            $('#img-valida-login').attr("title","login incorrecto");
                            $('#img-valida-login').attr("src",url_img_delete);
                            $('#infopuntotype_login').focus();
                            alert("Login ya existente. Favor Corregir");
                        }

                   }
                   else
                   {
                       alert("Error: No se pudo validar el login ingresado.");
                   }
                }
        });
    }
    else
    {
        flagLoginCorrecto = 0;
        $('#img-valida-login').attr("title","login incorrecto");
        $('#img-valida-login').attr("src",url_img_delete);
        $('#infopuntotype_login').focus();
    }    
}


Ext.onReady(function()
{
    if(boolBanderaTipo)
    {
        //CARGA DE DATOS DEL NUEVO CAMPO DEL COMBO DE ORIGEN
    $.ajax({
        url: url_formas_contacto,
        type: 'POST',
        success: function (response)
        {
            var arrayFormasContacto= response.formasContacto;
        //    arrayFormasContacto.sort((x, y) => x.descripcion.localeCompare(y.descripcion));
            var formasContactoCombo = document.getElementById("strNombreOrigen");
            var objOption = document.createElement("option");
            objOption.text = strOpcionIncial;
            formasContactoCombo.add(objOption);
          
          
            $.each(arrayFormasContacto, function (key, value) 
            {
                var objOption2 = document.createElement("option");
                objOption2.text = value.descripcion;
                objOption2.value=value.descripcion;
                formasContactoCombo.add(objOption2);
    
            });
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
           Ext.Msg.alert("Error al cargar información de los contactos");
        }
     });

//CARGA DE DATOS DEL NUEVO CAMPO DEL COMBO PUNTO DE ATENCION
$.ajax({
    url: urlComboPuntosAtencion,
    type: 'POST',
    success: function (response)
    {
        var arrayPuntosAtencion= jQuery.parseJSON(response.jsonPuntosAtencion);
     //   arrayPuntosAtencion.sort((x, y) => x.nombrePuntoAtencion.localeCompare(y.nombrePuntoAtencion));
        var PuntosAtencionCombo = document.getElementById("strNombrePuntodeAtencion");
        var objOption3 = document.createElement("option");
        objOption3.text = strOpcionIncial;
        PuntosAtencionCombo.add(objOption3);
      
      
        $.each(arrayPuntosAtencion, function (key, value) 
        {
            var objOption4 = document.createElement("option");
            objOption4.text = value.nombrePuntoAtencion;
            objOption4.value=value.nombrePuntoAtencion;
            PuntosAtencionCombo.add(objOption4);

        });
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
       Ext.Msg.alert("Error al cargar información del combo de puntos de atención");
    }
 });
    }


    $("#infopuntotype_tipoNegocioId").change(function() {
        generaLogin();
    })        
    if(prefijoEmpresa=="TN")
    {
        $("#identificacionCltDistribuidor").change(function() {
            getValidaCltDistribuidor();
        });
    }
    /**
     * Documentación para la función 'getValidaCltDistribuidor'.
     *
     * Función encargada validar si la identificación ingresada pertenece a un cliente, 
     * pre cliente o cuenta en TelcoCRM
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 25-05-2021
     *
    */
    function getValidaCltDistribuidor()
    {
        var strIdentificacion        = "";
        var identificacionEsCorrecta = false;
        var strMensajeIdentificacion = "";
        if(document.getElementById("identificacionCltDistribuidor").value != undefined)
        {
            strIdentificacion     = document.getElementById("identificacionCltDistribuidor").value;
            strTipoIdentificacion = document.getElementById("tipoIdentificacionCltDistribuidor").value;
            if (/^\d+$/.test(strIdentificacion) && ($('#tipoIdentificacionCltDistribuidor').val() === 'RUC' || $('#tipoIdentificacionCltDistribuidor').val() === 'CED'))
            {
                identificacionEsCorrecta = true;
            }
            if (/^[\w]+$/.test(strIdentificacion) && ($('#tipoIdentificacionCltDistribuidor').val() === 'PAS')) 
            {
                identificacionEsCorrecta = true;
            }
            if (identificacionEsCorrecta === true) 
            {
                $.ajax({
                    type: "POST",
                    data: "strIdentificacion=" + strIdentificacion + "&strTipoIdentificacion=" + strTipoIdentificacion,
                    url: url_ValidaCltDistribuidor,
                    beforeSend: function(){
                        $('#img-valida-identificacion-dist').attr("src",url_img_loader);
                    },
                    success: function(strMensaje)
                    {
                        if (strMensaje == "")
                        {
                            $('#img-valida-identificacion-dist').attr("title","Identificacion disponible");
                            $('#img-valida-identificacion-dist').attr("src",url_img_check);
                        }
                        if (strMensaje !== "")
                        {
                            Ext.Msg.show({
                                title: 'Error',
                                msg: strMensaje,
                                width : 420,
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.ERROR
                            });
                            document.getElementById("identificacionCltDistribuidor").value = "";
                            $('#img-valida-identificacion-dist').attr("title","identificacion ya existe");
                            $('#img-valida-identificacion-dist').attr("src",url_img_delete);
                            return;
                        }
                    }
                });
            }
            else
            {
                document.getElementById("identificacionCltDistribuidor").value = "";
                strMensajeIdentificacion = "Identificación es incorrecta por favor vuelva a ingresarla, no se permite caracteres especiales";
                Ext.Msg.show({
                    title: 'Error',
                    msg: strMensajeIdentificacion,
                    width : 420,
                    buttons: Ext.Msg.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        }
    }
 
    Ext.define('ListModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'id', type:'int'},
            {name:'nombre', type:'string'}
        ]
    });

    if ('S' === strAplicaTipoOrigen && 'S' === strIsGrantedTipoOrigen)
    {
        /*-- MODELO PARA LA PETICIÓN DE TIPO DE ORIGEN --*/
        Ext.define('ListModelTipoOrigen', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'valor1', type: 'string'},
                {name: 'valor2', type: 'string'}
            ]
        });
        /*-- STORE DE TIPO DE ORIGEN --*/
        objStoreTipoOrigen = Ext.create('Ext.data.Store',
                {
                    model: 'ListModelTipoOrigen',
                    autoLoad: true,
                    proxy:
                            {
                                type: 'ajax',
                                url: strUrlTipoOrigenEmpresa,
                                reader:
                                        {
                                            type: 'json',
                                            root: 'registros'
                                        }
                            },
                    listeners: {
                        load: function()
                        {
                            var rec = {valor1: 'Nuevo', valor2: ''};
                            this.insert(0, rec);
                        }
                    }

                });
        /*-- COMBOLIST DE TIPO DE ORIGEN --*/
        var objTipoOrigenList = new Ext.form.ComboBox(
                {
                    xtype: 'combobox',
                    store: objStoreTipoOrigen,
                    labelAlign: 'left',
                    name: 'tipoOrigenCaracteristica',
                    id: 'tipoOrigenCaracteristica',
                    valueField: 'valor2',
                    displayField: 'valor1',
                    fieldLabel: '',
                    width: 300,
                    allowBlank: true,
                    emptyText: 'Seleccione',
                    disabled: false,
                    renderTo: 'tipoOrigenDiv',
                    listeners:
                            {
                                select:
                                        {
                                            fn: function(combo, value)
                                            {
                                                $('#strTipoOrigenSelected').val(combo.getValue());
                                            }
                                        }
                            }
                });
                
        objStoreTipoOrigen.on('load', function()
        {
            objTipoOrigenList.setValue(strTipoOrigenSelected);
            objTipoOrigenList.setRawValue(strTipoOrigenDescripcion);
            $('#strTipoOrigenSelected').val(strTipoOrigenSelected);
        });
    }

    Ext.define('ListModelVendedor', {
        extend: 'Ext.data.Model',
        fields: [
            {name:'login', type:'string'},
            {name:'nombre', type:'string'}
        ]
    });
    
    storeVendedores = Ext.create('Ext.data.Store',
        {
            model: 'ListModelVendedor',
            pageSize: 200,
            autoLoad: false,
            proxy:
                {
                    type: 'ajax',
                    url: url_vendedores,
                    reader:
                        {
                            type: 'json',
                            root: 'registros'
                        }
                }
        });
    
    var combo_vendedores = new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            store: storeVendedores,
            labelAlign: 'left',
            name: 'idvendedor',
            id: 'idvendedor',
            valueField: 'login',
            displayField: 'nombre',
            fieldLabel: '',
            width: 290,
            allowBlank: false,
            emptyText: 'Seleccione Vendedor',
            disabled:strEditarCampos,
            renderTo: 'combo_vendedor',
            listeners:
                {
                    select:
                        {
                            fn: function(combo, value)
                            {
                                $('#infopuntoextratype_loginVendedor').val(combo.getValue());
                                document.getElementById('loginVend').value = combo.getValue();
                                ocultarDiv('div_errorvendedor');
                            }
                        },
                    click:
                        {
                            element: 'el',
                            fn: function()
                            {
                                storeVendedores.load();
                            }
                        }
                }
        });
    
    if (esRequerido)
    {
        Ext.define('ListModelCanal',
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'canal', type: 'string'},
                    {name: 'descripcion', type: 'string'}
                ]
            });

        Ext.define('ListModelPuntoVenta',
            {
                extend: 'Ext.data.Model',
                fields: [
                    {name: 'punto_venta', type: 'string'},
                    {name: 'descripcion', type: 'string'}
                ]
            });

        storeCanales = Ext.create('Ext.data.Store',
            {
                model: 'ListModelCanal',
                autoLoad: true,
                proxy:
                    {
                        type: 'ajax',
                        url: url_canales,
                        reader:
                            {
                                type: 'json',
                                root: 'canales'
                            }
                    }
            });

        storePuntoVenta = Ext.create('Ext.data.Store',
            {
                model: 'ListModelPuntoVenta',
                autoLoad: true,
                proxy:
                    {
                        type: 'ajax',
                        url: url_punto_venta,
                        reader: {
                            type: 'json',
                            root: 'puntos_venta'
                        }
                    }
            });
  // create the Data Store
  var storeFormasContactoOrigen = Ext.create('Ext.data.Store', {
    // destroy the store if the grid is destroyed
    autoDestroy: true,
    model: 'FormasContactoModel',
    proxy: {
        type: 'ajax',
        url: url_formas_contacto,
        reader: {
            type: 'json',
            root: 'formasContacto'
        }
    }
});
          
        storeCanales.on('load', function()
        {
            if (typeof canal !== typeof undefined && canal != '')
            {
                combo_canal.select(canal, true);
                $('#infopuntoextratype_canal').val(canal);
                storePuntoVenta.getProxy().extraParams.canal = canal;
                storePuntoVenta.load();
                combo_canal.setDisabled(true);
            }
            
        });

        storePuntoVenta.on('load', function()
        {
            if (typeof puntoVenta !== typeof undefined && puntoVenta != '')
            {
                rec = storePuntoVenta.findRecord('punto_venta', puntoVenta);
                if (rec != null)
                {
                    combo_punto_venta.select(puntoVenta, true);
                    $('#infopuntoextratype_punto_venta').val(puntoVenta);
                }
            }
        });
        storeFormasContactoOrigen.load();
        var combo_canal = new Ext.form.ComboBox(
            {
                xtype: 'combobox',
                store: storeCanales,
                labelAlign: 'left',
                id: 'idCanal',
                name: 'idCanal',
                valueField: 'canal',
                displayField: 'descripcion',
                fieldLabel: '',
                width: 250,
                allowBlank: false,
                emptyText: 'Seleccione Canal',
                editable: false,
                disabled:strEditarCampos,
                renderTo: 'Canales',
                queryMode:'local',
                listeners:
                    {
                        select:
                            {
                                fn: function(combo)
                                {
                                    $('#infopuntoextratype_canal').val(combo.getValue());
                                    $('#infopuntoextratype_punto_venta').val(null);
                                    ocultarDiv('div_errorcanal');
                                    combo_punto_venta.setValue(null);
                                    combo_punto_venta.setRawValue(null);
                                    storePuntoVenta.getProxy().extraParams.canal = combo.getValue();
                                    storePuntoVenta.load();
                                    if (combo.getValue() == 'CANAL_INTERNO')
                                    {
                                        capa = document.getElementById('label_punto_venta');
                                        capa.style.display = 'none';

                                        capa = document.getElementById('PuntosVenta');
                                        esObligatorio = false;
                                        combo_punto_venta.getEl().hide();
                                    }
                                    else
                                    {
                                        capa = document.getElementById('label_punto_venta');
                                        capa.style.display = 'block';

                                        capa = document.getElementById('PuntosVenta');
                                        esObligatorio = true;
                                        combo_punto_venta.getEl().show();
                                    }
                                }
                            },
                        click:
                            {
                                element: 'el'
                            }
                    }
            });
        
    

        var combo_punto_venta = new Ext.form.ComboBox(
            {
                xtype: 'combobox',
                store: storePuntoVenta,
                labelAlign: 'left',
                name: 'idPuntoVenta',
                id: 'idPuntoVenta',
                valueField: 'punto_venta',
                displayField: 'descripcion',
                fieldLabel: '',
                disabled:strEditarCampos,
                width: 250,
                allowBlank: false,
                emptyText: 'Seleccione Punto de Venta',
                editable: false,
                renderTo: 'PuntosVenta',
                listeners:
                    {
                        select:
                            {
                                fn: function(combo)
                                {
                                    $('#infopuntoextratype_punto_venta').val(combo.getValue());
                                    ocultarDiv('div_errorpuntoventa');
                                }
                            },
                        click:
                            {
                                element: 'el'
                            }
                    }
            });
    }
    
    if (typeof loginEmpleado !== typeof undefined && loginEmpleado !== "")
    {
        combo_vendedores.setValue(loginEmpleado);
        $('#infopuntoextratype_loginVendedor').val(loginEmpleado);
    }
    
    if (typeof nombreEmpleado !== typeof undefined && nombreEmpleado !== "")
    {
        combo_vendedores.setRawValue(nombreEmpleado);
    }
    
    if ((typeof latitudFloat  !== typeof undefined) && 
        (typeof longitudFloat !== typeof undefined))
    {
        dd2dms(parseFloat(latitudFloat), parseFloat(longitudFloat));
    }
    
    if (typeof loginPunto !== typeof undefined)
    {
        if (loginPunto !== null || loginPunto !== '')
        {
            if(strTipo!="editar")
            {
                validaLogin();
            }
            else
            {
            flagLoginCorrecto = 1;
                $ ('#img-valida-login').attr ('title', 'login correcto');
                $ ('#img-valida-login').attr ('src', url_img_check);
            }
            
        }
    }

    if (typeof esError !== typeof undefined && esError == 'S')
    {
        Ext.Msg.show(
            {
                title: 'Advertencia',
                msg: "Debe volver a cargar los archivos de ser necesario",
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.WARNING
            });
    }

      if (typeof strNombreOrigen !== typeof undefined &&strNombreOrigen!="" &&typeof strNombreOrigen === 'string' )
    {
        var formasContactoCombo = document.getElementById("strNombreOrigen");
        var objOption5 = document.createElement("option");
        objOption5.text = strNombreOrigen;
        formasContactoCombo.add(objOption5);
    }


    if (typeof strNombrePuntoAtencion !== typeof undefined && strNombreOrigen == 'ATC'&&strNombrePuntoAtencion!="")
    {
        var PuntosAtencionCombo = document.getElementById("strNombrePuntodeAtencion");
        var objOption6= document.createElement("option");
        objOption6.text = strNombrePuntoAtencion;
        PuntosAtencionCombo.add(objOption6);
        $("#bloquePuntoAtencion").css({display:"table-row"});
    }

    storePtosCobertura = Ext.create('Ext.data.Store',
        {
            id: 'storePtosCobertura',
            autoLoad: true,
            model: 'ListModel',
            proxy:
                {
                    type: 'ajax',
                    url: url_puntoscobertura,
                    reader:
                        {
                            type: 'json',
                            root: 'jurisdicciones'
                        }
                }
        });

    storeCantones = Ext.create('Ext.data.Store',
        {
            model: 'ListModel',
            autoLoad: false,
            proxy:
                {
                    type: 'ajax',
                    url: url_cantones,
                    reader:
                        {
                            type: 'json',
                            root: 'cantones'
                        }
                }
        });

    storeParroquias = Ext.create('Ext.data.Store',
        {
            model: 'ListModel',
            autoLoad: false,
            proxy:
                {
                    type: 'ajax',
                    url: url_lista_parroquias,
                    reader:
                        {
                            type: 'json',
                            root: 'parroquias'
                        }
                }
        });

    storeSectores = Ext.create('Ext.data.Store',
        {
            autoLoad: false,
            model: "ListModel",
            proxy:
                {
                    type: 'ajax',
                    url: url_lista_sectores,
                    reader:
                        {
                            type: 'json',
                            root: 'sectores'
                        }
                }
        });
        
    storePtosCobertura.on('load', function()
    {
        Ext.ComponentQuery.query('combobox[name=idcanton]')[0].reset();
        if (typeof ptoCoberturaId !== typeof undefined && ptoCoberturaId != '')
        {
            rec = storePtosCobertura.findRecord('id', ptoCoberturaId);
            if(rec != null)
            {
                combo_ptoscobertura.select(parseInt(ptoCoberturaId), true);
                $('#infopuntoextratype_ptoCoberturaId').val(ptoCoberturaId);
                storeCantones.proxy.extraParams = {idjurisdiccion: ptoCoberturaId};
                storeCantones.load();
                if (esRequerido) {
                    Ext.ComponentQuery.query('combobox[name=idptocobertura]')[0].setDisabled(true);               
                }
                
            }
        }
    });


    storeCantones.on('load', function()
    {
        if (typeof cantonId !== typeof undefined && cantonId != '')
        {
            rec = storeCantones.findRecord('id', cantonId);
            if(rec != null)
            {
             
                Ext.ComponentQuery.query('combobox[name=idcanton]')[0].setDisabled(false);
                combo_cantones.select(parseInt(cantonId), true);
                $('#infopuntoextratype_cantonId').val(cantonId);
                storeParroquias.proxy.extraParams = {idcanton: cantonId};
                storeParroquias.load();
                if (esRequerido) {
                    Ext.ComponentQuery.query('combobox[name=idcanton]')[0].setDisabled(true);               
                }
            }
        }
    });

    storeParroquias.on('load', function()
    {
        if (typeof parroquiaId !== typeof undefined && parroquiaId  != '')
        {
            rec = storeParroquias.findRecord('id', parroquiaId );
            if(rec != null)
            {
                Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].setDisabled(false);
                combo_parroquias.select(parseInt(parroquiaId ), true);
                $('#infopuntoextratype_parroquiaId').val(parroquiaId );
                storeSectores.proxy.extraParams = {idparroquia: parroquiaId };
                storeSectores.load();
                if (esRequerido) {
                    Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].setDisabled(true);               
                }
            }
        }
    });

    storeSectores.on('load', function()
    {
        if (typeof sectorId !== typeof undefined && sectorId != '')
        {
            rec = storeSectores.findRecord('id', sectorId);
            if(rec != null)
            {
                Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(false);
                combo_sector.select(parseInt(sectorId), true);
                $('#infopuntoextratype_sectorId').val(sectorId);
                if (esRequerido) {
                    Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(true);               
                }
            }
        }
    });

    combo_ptoscobertura = new Ext.form.ComboBox(
        {
            xtype: 'combobox',
            id: 'idptocobertura',
            name: 'idptocobertura',
            store: storePtosCobertura,
            labelAlign: 'left',
            emptyText: 'Escriba y Seleccione Pto Cobertura',
            valueField: 'id',
            displayField: 'nombre',
            fieldLabel: '',
            width: 300,
            allowBlank: false,
            renderTo: 'combo_ptoscobertura',
            queryMode: 'local',
            listeners: {
                select: {fn: function(combo, value) {
                        Ext.ComponentQuery.query('combobox[name=idcanton]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idcanton]')[0].setDisabled(false);
                        Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].setDisabled(true);
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(true);
                        $('#infopuntoextratype_sectorId').val('');
                        $('#infopuntodatoadicionaltype_puntoedificio').val('');
                        $('#infopuntodatoadicionaltype_puntoedificioid').val('');
                        $('#infopuntoextratype_ptoCoberturaId').val(combo.getValue());
                        storeCantones.proxy.extraParams = {idjurisdiccion: combo.getValue()};
                        storeCantones.load();

                    }},
                change: {fn: function(combo, newValue, oldValue) {
                        Ext.ComponentQuery.query('combobox[name=idcanton]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
                        Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                    }}
            }

        });
    
    var strLblCanton    = 'Seleccione Cant\u00F3n';
    var strLblParroquia = 'Seleccione Parroquia';
    var strLblSector    = 'Seleccione Sector';
    if (strNombrePais === 'PANAMA')
    {
        strLblCanton    = 'Seleccione Distrito';
        strLblParroquia = 'Seleccione Corregimiento';
    }
    combo_cantones = new Ext.form.ComboBox({
            id: 'idcanton',
            name: 'idcanton',
            labelAlign : 'left',
            fieldLabel: '',
            anchor: '100%',
            disabled: true,
            width: 300,
            emptyText: strLblCanton,
            store: storeCantones,
            displayField: 'nombre',
            valueField: 'id',
            triggerAction: 'all',
            selectOnFocus:true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            renderTo: 'combo_cantones',
            listeners:{
                select:{fn:function(combo, value) {
                   Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].reset();
                    Ext.ComponentQuery.query('combobox[name=idparroquia]')[0].setDisabled(false);
                    Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(true);
                    $('#infopuntoextratype_cantonId').val(combo.getValue());
                    storeParroquias.proxy.extraParams = {idcanton: combo.getValue()};
                    storeParroquias.load();
                    generaLogin();
                    $('#canton').val(combo.getRawValue());
                    if($('#canton').val()=='GUAYAQUIL' || $('#canton').val()=='QUITO' ){
                                        $('#labelLatitud').addClass('campo-obligatorio');
                                                            $('#grados_la').attr('required','required');
                                                            $('#minutos_la').attr('required','required');
                                                            $('#segundos_la').attr('required','required');
                                                            $('#decimas_segundos_la').attr('required','required');
                                                            $('#latitud').attr('required','required');
                                                            $('#labelLongitud').addClass('campo-obligatorio');
                                                            $('#grados_lo').attr('required','required');
                                                            $('#minutos_lo').attr('required','required');
                                                            $('#segundos_lo').attr('required','required');
                                                            $('#decimas_segundos_lo').attr('required','required');
                                                            $('#longitud').attr('required','required');
                    }else
                    {
                                        $('#labelLatitud').removeClass('campo-obligatorio');
                                                            $('#labelLongitud').removeClass('campo-obligatorio');
                                                            $('#grados_la').removeAttr('required');
                                                            $('#minutos_la').removeAttr('required');
                                                            $('#segundos_la').removeAttr('required');
                                                            $('#decimas_segundos_la').removeAttr('required');
                                                            $('#latitud').removeAttr('required');
                                                            $('#grados_lo').removeAttr('required');
                                                            $('#minutos_lo').removeAttr('required');
                                                            $('#segundos_lo').removeAttr('required');
                                                            $('#decimas_segundos_lo').removeAttr('required');
                                                            $('#longitud').removeAttr('required');										
                    }	
					
					
                },
            beforeshow: function(picker)
                {
                    picker.minWidth = picker.up('combobox').getSize().width;
                }
            },
                change: {fn:function( combo, newValue, oldValue ){				
                }}
            }            
    });    
    
    combo_parroquias = new Ext.form.ComboBox(
        {
            name: 'idparroquia',
            id: 'idparroquia',
            labelAlign: 'left',
            fieldLabel: '',
            disabled: true,
            width: 300,
            emptyText: strLblParroquia,
            store: storeParroquias,
            displayField: 'nombre',
            valueField: 'id',
            triggerAction: 'all',
            selectOnFocus: true,
            lastQuery: '',
            mode: 'local',
            allowBlank: false,
            renderTo: 'combo_parroquias',
            matchFieldWidth: true,
            listeners:
                {
                    select:
                        {
                            fn: function(combo, value)
                            {
                                if(strNombrePais !== 'GUATEMALA')
                                {
                                    Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                                    Ext.ComponentQuery.query('combobox[name=idsector]')[0].setDisabled(false);                                    
                                    storeSectores.proxy.extraParams = {idparroquia: combo.getValue()};
                                    storeSectores.load();                                    
                                }
                                $('#infopuntoextratype_parroquiaId').val(combo.getValue());

                            }
                        },
                    change:
                        {
                            fn: function(combo, newValue, oldValue)
                            {
                                if(strNombrePais !== 'GUATEMALA' && !esRequerido)
                                {
                                    Ext.ComponentQuery.query('combobox[name=idsector]')[0].reset();
                                }
                            }
                        }
                }
        });    
		
    combo_sector = new Ext.form.ComboBox({
                xtype: 'combobox',
                store: storeSectores,
                labelAlign : 'left',
                name: 'idsector',
		valueField:'id',
                displayField:'nombre',
                fieldLabel: '',
		width: 300,
		triggerAction: 'all',
		selectOnFocus:true,
		lastQuery: '',
		mode: 'local',
		allowBlank: false,	
                emptyText: strLblSector,
                disabled: true,
            renderTo: 'combo_sector',                
		listeners: {
                select:{fn:function(combo, value) {
                    $('#infopuntoextratype_sectorId').val(combo.getValue());
                    ocultarDiv('div_errorsector');
                }},
                    click: {
                        element: 'el',
                        fn: function(){ 
                        }
                    }			
		}
            });


    Ext.define('PersonaFormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'idPersonaFormaContacto', type: 'int'},
            {name: 'formaContacto'},
            {name: 'valor', type: 'string'}
        ]
    });

    Ext.define('FormasContactoModel', {
        extend: 'Ext.data.Model',
        fields: [
            // the 'name' below matches the tag name to read, except 'availDate'
            // which is mapped to the tag 'availability'
            {name: 'id', type: 'int'},
            {name: 'descripcion', type: 'string'}
        ]
    });
    
    // create the Data Store
    storePersonaFormasContacto = null;
    if(boolBanderaTipo)
    {
        storePersonaFormasContacto = Ext.create('Ext.data.Store',
        {
            autoDestroy: true, 
            model: 'PersonaFormasContactoModel',
            //autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    url: url_formas_contacto_punto_origen,
                    reader:
                        {
                            type: 'json',
                            root: 'personaFormasContacto',
                            totalProperty: 'total'
                        },
                    extraParams:
                        {
                            personaid: intPuntoId
                        },
                    simpleSortMode: true
                },
            listeners:
                {
                    beforeload: function(store)
                    {
                        store.getProxy().extraParams.personaid = intPuntoId;
                    }
                }
        });
    }
    else
    {
        storePersonaFormasContacto = Ext.create('Ext.data.Store',
        {
            autoDestroy: true, 
            model: 'PersonaFormasContactoModel',
            //autoLoad: true,
            proxy:
                {
                    type: 'ajax',
                    url: url_formas_contacto_persona,
                    reader:
                        {
                            type: 'json',
                            root: 'personaFormasContacto',
                            totalProperty: 'total'
                        },
                    extraParams:
                        {
                            personaid: personaid
                        },
                    simpleSortMode: true
                },
            listeners:
                {
                    beforeload: function(store)
                    {
                        store.getProxy().extraParams.personaid = personaid;
                    }
                }
        });
    }

    if (typeof formasDeContacto !== typeof undefined && formasDeContacto != '')
    {
        arrayFormasContacto = formasDeContacto.split(',');
        for (i = 0; i < arrayFormasContacto.length; i += 3)
        {
            var registro =
                {
                    'idPersonaFormaContacto': arrayFormasContacto[i],
                    'formaContacto': arrayFormasContacto[i + 1],
                    'valor': arrayFormasContacto[i + 2]
                };
            var rec = new PersonaFormasContactoModel(registro);
            if (rec.formaContacto !== "")
            {
                storePersonaFormasContacto.add(rec);
            }
        }
    }

    // create the Data Store
    var storeFormasContacto = Ext.create('Ext.data.Store', {
        // destroy the store if the grid is destroyed
        autoDestroy: true,
        model: 'FormasContactoModel',
        proxy: {
            type: 'ajax',
            url: url_formas_contacto,
            reader: {
                type: 'json',
                root: 'formasContacto'
            }
        }
    });

     
    
    var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 2
    });

    // create the grid and specify what field you want
    // to use for the editor at each header.
    gridFormasContacto = Ext.create('Ext.grid.Panel',
        {
            store: storePersonaFormasContacto,
            columns:
                [
                    {
                        text: 'Forma Contacto',
                        header: 'Forma Contacto',
                        dataIndex: 'formaContacto',
                        width: 150,
                        editor: new Ext.form.field.ComboBox(
                            {
                                typeAhead: true,
                                triggerAction: 'all',
                                selectOnTab: true,
                                id: 'id',
                                name: 'formaContacto',
                                valueField: 'descripcion',
                                displayField: 'descripcion',
                                store: storeFormasContacto,
                                lazyRender: true,
                                listClass: 'x-combo-list-small'
                            })
                    },
                    {
                        text: 'Valor',
                        //header: 'Valor',
                        dataIndex: 'valor',
                        width: 400,
                        align: 'right',
                        editor:
                            {
                                width: '80%',
                                xtype: 'textfield',
                                allowBlank: false
                            }
                    },
                    {
                        xtype: 'actioncolumn',
                        width: 45,
                        sortable: false,
                        items:
                            [
                                {
                                    iconCls: "button-grid-delete",
                                    tooltip: 'Borrar Forma Contacto',
                                    handler: function(grid, rowIndex, colIndex)
                                    {
                                        storePersonaFormasContacto.removeAt(rowIndex);
                                    }
                                }
                            ]
                    }
                ],
            selModel:
                {
                    selType: 'cellmodel'
                },
            renderTo: Ext.get('lista_formas_contacto_grid'),
            width: 600,
            height: 300,
            title: '',
            tbar:
                [
                    {
                        text: 'Agregar',
                        handler: function()
                        {
                            var boolError = false;
                            var indice = 0;
                            for (var i = 0; i < storePersonaFormasContacto.getCount(); i++)
                            {
                                variable = storePersonaFormasContacto.getAt(i).data;
                                boolError = trimAll(variable['formaContacto']) == '';

                                if (boolError)
                                {
                                    break;
                                }
                                else
                                {
                                    boolError = trimAll(variable['valor']) == '';
                                    if (boolError)
                                    {
                                        indice = 1;
                                        break;
                                    }
                                }
                            }
                            if (!boolError)
                            {
                                var r = Ext.create('PersonaFormasContactoModel',
                                    {
                                        idPersonaFormaContacto: '',
                                        formaContacto: '',
                                        valor: ''
                                    });
                                storePersonaFormasContacto.insert(0, r);
                            }
                            cellEditing.startEditByPosition({row: 0, column: indice});
                        }
                    }],
            plugins: [cellEditing]
        });
        storePersonaFormasContacto.load();

    function trimAll(texto)
    {
        return texto.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g, '').replace(/\s+/g, ' ');
    }

    var tabs = new Ext.TabPanel({
        height: 650,
        renderTo: 'my-tabs',
        activeTab: 0,
        plain:true,
        autoRender:true,
        autoShow:true,
        items:[
             {contentEl:'tab1', title:'Datos Principales'},
             {contentEl:'tab2', title:'Formas de contacto',listeners:{
                  activate: function(tab){
                          gridFormasContacto.view.refresh();
                          if (storePersonaFormasContacto.getCount() <= 0) {  
                              
                              storePersonaFormasContacto.load();      
                          }
                  }
                                
              }}
        ]            
    }); 
    
    //Se valida por empresa, presenta mensaje, bloquea botón guardar por status que devuelve del microservicio
    // validacionesPuntoAdicional.
    if (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN' )
    {
        var strStatusBloqueo = document.getElementById('strStatusBloqueo').value.trim(); 
        var strMsjBloqueo    = document.getElementById('strMsjBloqueo').value.trim();

        if(strStatusBloqueo == "ERROR")
        { 
            document.getElementById('btn-guardar').disabled = true;
            document.getElementById('btn-guardar').setAttribute("style", "background-color: #B5B2B2; text-shadow: none");
            document.getElementById("btn-guardar").title = "Botón bloqueado";
            Ext.Msg.show({
                title: 'Advertencia',
                msg: strMsjBloqueo,
                width : 420,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
            return false;
        }  
    }

    $.ajax({
        url: url_solInfCliente,
        method: 'GET',
        success: function (data) {
            if(data.solicitarInfoCliente == 'S'){
                $("#solInformacionCliente").prop("checked", true);
                $("#solicitarInfoClient").val('S');
            } else {
                $("#solInformacionCliente").prop("checked", false);
                $("#solicitarInfoClient").val('N');
            }
        },
        error: function () {
            alert("Error: al obtener la solicitud de información del cliente");
        }
    });


});

/**
 * Permite validar las formas de contactos.
 *
 * @version 1.00
 * 
 * Se llama a validación de formas de contactos centralizada.
 *
 * @author Héctor Ortega <haortega@telconet.ec>
 * @version 1.01, 29/11/2016
 */
function validaFormasContacto(){
    return Utils.validaFormasContacto(gridFormasContacto);
}

function validacionesForm(){

            if(boolBanderaTipo &&prefijoEmpresa == "MD")
            {
               
                    if(document.getElementById("strNombreOrigen").value == strOpcionIncial)
                    {
                        Ext.Msg.show({
                            title: 'Error',
                            msg: 'El campo "Origen", es obligatorio',
                            width : 420,
                            buttons: Ext.Msg.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                        return false;
                    }
                    else
                    {
                        if(document.getElementById("strNombreOrigen").value == "ATC" &&document.getElementById("strNombrePuntodeAtencion").value == strOpcionIncial)
                        {
                                Ext.Msg.show({
                                    title: 'Error',
                                    msg: 'El campo "Punto de Atencion", es obligatorio cuando el origen es ATC',
                                    width : 420,
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                                return false;
                            
                        }
                        if(document.getElementById("strNombreOrigen").value != "ATC")
                        {
                            document.getElementById("strNombrePuntodeAtencion").value=null;
                        }
                      
                    }
                
            }
    if( $("#solInformacionCliente").prop('checked') ) {
        $("#solicitarInfoClient").val('S');
    } else {
        $("#solicitarInfoClient").val('N');
    }
    if(prefijoEmpresa == "TN" && strEsDistribuidor == "SI")
    {
        if(document.getElementById("identificacionCltDistribuidor").value == undefined)
        {
            Ext.Msg.show({
                title: 'Error',
                msg: 'El campo "Identificación clt", es obligatorio, en caso de tener un cliente tipo Distribuidor en sesión',
                width : 420,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
            return false;
        }
        if(document.getElementById("razonSocialCltDistribuidor").value == undefined)
        {
            Ext.Msg.show({
                title: 'Error',
                msg: 'El campo "Razón social clt", es obligatorio, en caso de tener un cliente tipo Distribuidor en sesión',
                width : 420,
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
            return false;
        }
    }

    if ($('#infopuntodatoadicionaltype_dependedeedificio').val() == 'S')
    {
        if ($('#infopuntodatoadicionaltype_puntoedificio').val() == '')
        {
            alert('Debe ingresar el nombre del edificio del cual depende.');
            return false;
        }
    }    

    if(!validaFormasContacto())
    {
        return false;
    }
    
    if (esRequerido)
    {
        if ($('#infopuntoextratype_canal').val() == '')
        {
            mostrarDiv('div_errorcanal');
            return false;
        }
        if (esObligatorio)
        {
            if ($('#infopuntoextratype_punto_venta').val() == '')
            {
                mostrarDiv('div_errorpuntoventa');
                return false;
            }
        }
    }
    if(strNombrePais !== 'GUATEMALA' && $('#infopuntoextratype_sectorId').val()==='')
    {
        mostrarDiv('div_errorsector');
        return false;
    }
    
    if($('#infopuntoextratype_loginVendedor').val()=='')
    {
        mostrarDiv('div_errorvendedor');
        return false;
    }
    
    if(validaLoginCorrecto())
    {
            //Solo valida las COORDENADAS si es que es GYE o UIO
            //if($('#canton').val()=='GUAYAQUIL' || $('#canton').val()=='QUITO' ){
        if ((document.forms[0].grados_la.value && document.forms[0].minutos_la.value && document.forms[0].segundos_la.value && document.forms[0].decimas_segundos_la.value && (document.forms[0].latitud.value!='T'))&&
        (document.forms[0].grados_lo.value && document.forms[0].minutos_lo.value && document.forms[0].segundos_lo.value && document.forms[0].decimas_segundos_lo.value) && (document.forms[0].longitud.value!='T'))
        {
                //funciiones para validar las coordenadas
            if(!validarGradosNuevo(document.forms[0].grados_la.value,1))
                return false;
            if(!validarMinutosNuevo(document.forms[0].minutos_la.value,1))
                return false;
            if(!validarSegundosNuevo(document.forms[0].segundos_la.value,1))
                return false;
            if(!validarDecimasSegundosNuevo(document.forms[0].decimas_segundos_la.value,1))
                return false;
            if(!validarGradosNuevo(document.forms[0].grados_lo.value,2))
                return false;
            if(!validarMinutosNuevo(document.forms[0].minutos_lo.value,2))
                return false;
            if(!validarSegundosNuevo(document.forms[0].segundos_lo.value,2))
                return false;
            if(!validarDecimasSegundosNuevo(document.forms[0].decimas_segundos_lo.value,2))
                return false;
            if(document.forms[0].latitud[document.forms[0].latitud.selectedIndex].value=='T')
            {
                alert('Ingrese la latitud (Norte/Sur)');
                return false;
            }

             if(document.forms[0].longitud[document.forms[0].longitud.selectedIndex].value=='T')
            {
                alert('Ingrese la longitud (Este/Oeste)');
                return false;
            }

            if(!validarCoordenadasEcuador(document.forms[0]))
                return false;
            }else
            {
                if ((document.forms[0].grados_la.value || document.forms[0].minutos_la.value || document.forms[0].segundos_la.value || document.forms[0].decimas_segundos_la.value || (document.forms[0].latitud.value!='T'))||
                (document.forms[0].grados_lo.value || document.forms[0].minutos_lo.value || document.forms[0].segundos_lo.value || document.forms[0].decimas_segundos_lo.value) || (document.forms[0].longitud.value!='T'))
                {	
                        alert('Si no va a ingresar coordenadas debe dejar todos los campos de las coordenadas vacios.');
                        return false;
                }else
                {
                        return true;
                }
            }
    }else{
            return false;
    }
    
    if (prefijoEmpresa == 'TN'){
        var strCorreosDatoEnvio     = '';
        var strTelefonosDatoEnvio   = '';
        var intIdSector             = '';
        var strEsPadreDeFacturacion = '';
        
        strEsPadreDeFacturacion     = $( "#infopuntodatoadicionaltype_esPadreFacturacion" ).val();
        
        if(strEsPadreDeFacturacion == 'S')
        {
        
            if( Ext.isEmpty( $('#nombreDatoEnvio').val() )             || 
                Ext.isEmpty( $('#ciudadDatoEnvio').val() )             ||
                Ext.isEmpty( $('#parroquiaDatoEnvio').val() )          ||
                Ext.isEmpty( $('#sectorDatoEnvio').val() )             ||
                Ext.isEmpty( $('#direccionDatoEnvio').val() )          ||
                Ext.isEmpty( $('#correoElectronicoDatoEnvio').val() )  ||
                Ext.isEmpty( $('#telefonoDatoEnvio').val() )           
              )
            {
               alert('Error al guardar el punto, falta información de Datos de Envío.');
               return false;
            }
            else
            {
                 intIdSector            = parsearStringPadreHijoPorDelimitador( $('#sectorDatoEnvio').val() , '|', '', '');
                 document.getElementById("sectorDatoEnvio").value = intIdSector ;
            }
        }
        
        if(strEsPadreDeFacturacion == 'N')
        {
            if(pf_obligatorio === 'S')
            {
               alert('Por ser el primer punto del Cliente debe escogerlo como padre de Facturación e ingresar los datos de envío.');
               return false;
                    
            }
        }
        
        
    }

    return true;
}

function grabarFormasContacto(campo)
{
    var array_data = new Array();
    var variable='';
    var valoresVacios=false;
    for(var i=0; i < gridFormasContacto.getStore().getCount(); i++){ 
        variable=gridFormasContacto.getStore().getAt(i).data;
        for(var key in variable) {
            var valor = variable[key];
            if (key=='valor' && valor==''){
                    valoresVacios=true;
            }else{
                    array_data.push(valor);
            }
        } 
    }
    $(campo).val(array_data); 
    if (($(campo).val()=='0,,') || ($(campo).val()=='')) {
        alert('No hay formas de contacto aun ingresadas.');
        $(campo).val('');
    }else{
        if(valoresVacios==true){
                alert('Hay formas de contacto que tienen valor vacio, por favor corregir.');
                $(campo).val('');
        }
    }
}

function parsearStringPadreHijoPorDelimitador(strPadre , strDelimitadorPadre, strDelimitadorHijo, strSeparador){
    
    var strParseado     = '';
   
    if(!Ext.isEmpty( strDelimitadorPadre )){
        var arrayGrupos     = strPadre.split( strDelimitadorPadre );
        var arrayHijo       = new Array();
        var strValor        = '';
        
        if(!Ext.isEmpty( strDelimitadorHijo )){
            for (intIndex in arrayGrupos) {
                arrayHijo   = arrayGrupos[intIndex].split(strDelimitadorHijo);
                strValor    = arrayHijo[1];

                if( !Ext.isEmpty( strValor )){
                    strParseado = strParseado  + strSeparador + strValor ;
                }
            }
            
            strParseado = strParseado.substring(1, strParseado.length);
        }
        else
        {
            strParseado = arrayGrupos[0];
        }
    }
    
    return strParseado;
}
    /**
     * Documentación para la función 'validarIdentificacionTipo'.
     *
     * Función encargada validar la cantidad maxima de la identificación según el tipo.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 25-05-2021
     *
    */
    function validarIdentificacionTipo()
    {
        document.getElementById("identificacionCltDistribuidor").value = "";
        strTipoIdentificacion = document.getElementById("tipoIdentificacionCltDistribuidor").value;
        var intMaxLongitudIdentificacion = 0;
        if(strTipoIdentificacion == "SELECCIONE")
        {
            $('#img-valida-identificacion-dist').attr("src",url_img_delete);
            document.getElementById("identificacionCltDistribuidor").value = "";
            $('#identificacionCltDistribuidor').attr('readonly',true);
        }
        else
        {
            Ext.Ajax.request(
                {
                    url: url_getMaxLongitudIdentificacionAjax,
                    method: 'POST',
                    timeout: 99999,
                    async: false,
                    params: { strTipoIdentificacion : strTipoIdentificacion },
                    success: function(response){
            
                    var objRespuesta = Ext.JSON.decode(response.responseText);
            
                    if(objRespuesta.intMaxLongitudIdentificacion > 0)
                    {
                        intMaxLongitudIdentificacion = objRespuesta.intMaxLongitudIdentificacion;
                    }
                        $('#identificacionCltDistribuidor').removeAttr('readonly');
                        $('#identificacionCltDistribuidor').removeAttr('maxlength');
                        $('#identificacionCltDistribuidor').attr('maxlength',intMaxLongitudIdentificacion);
                    },
                    failure: function(response)
                    { 
                        Ext.Msg.alert('Error ','Error: ' + response.statusText);
                    }
                });
        }
    }


/*
 * @author Jefferson Alexy Carrillo Anchundia <jacarrillo@telconet.ec>       
 * @version 1.0  07-07-2021
 * #GEO Se dejo el metodo generarLogin de manera global
 */
function generaLogin () {
    var cantonId = combo_cantones.getValue ();
    var tiponegocioid = '';
    if (!isNaN (document.getElementById ('infopuntotype_tipoNegocioId').value))
      tiponegocioid = document.getElementById ('infopuntotype_tipoNegocioId')
        .value;
  
    $.ajax ({
      type: 'POST',
      data: 'idCanton=' +
        cantonId +
        '&idCliente=' +
        clienteId +
        '&tipoNegocio=' +
        tiponegocioid,
      url: url_genera_login,
      beforeSend: function () {
        $ ('#img-valida-login').attr ('src', url_img_loader);
      },
      success: function (msg) {
        if (msg != '') {
          $ ('#infopuntodatoaldicionaltype_login').removeAttr ('readonly');
          $ ('#infopuntodatoaldicionaltype_login').val (msg);
          if (prefijoEmpresa == 'MD' || prefijoEmpresa == 'EN' ) {
            $ ('#infopuntodatoaldicionaltype_login').attr (
              'readonly',
              'readonly'
            );
          }
          $ ('#img-valida-login').attr ('title', 'login correcto');
          $ ('#img-valida-login').attr ('src', url_img_check);
          validaLogin ();
        } else {
          alert ('Error: No se pudo generar el login ingresado.');
        }
      },
    });
  }
  
   /*
   * @author Jefferson Alexy Carrillo Anchundia <jacarrillo@telconet.ec>       
   * @version 1.0  07-07-2021
   * #GEO Se inicializar cambios en interfaz grafica
   */
   function bloquearInputGeolocalizacionMD () {
    if (esRequerido) { 
    Ext.ComponentQuery
      .query ('combobox[name=idptocobertura]')[0]
      .setDisabled (true);
    }  
  }   
  /*
   * @author Jefferson Alexy Carrillo Anchundia <jacarrillo@telconet.ec>       
   * @version 1.0  07-07-2021
   * #GEO Se inicializar cambios en interfaz grafica
   */
  $ (document).ready (function () {  
    cargaBloqueoCampos();
      setTimeout (() => {
         bloquearInputGeolocalizacionMD(); 
      }, 200);  
  });

/*
 * @author Andre Lazo <alazo@telconet.ec>       
 * @version 1.0  13-02-2023
 * este metodo bloquea y carga los campos de canal, tipo de negocio, 
 * punto de venta y vendedor para el nuevo flujo
 */
  function cargaBloqueoCampos()
  { 
    //CARGA DE DATOS DEL NUEVO CAMPO DEL COMBO DE ORIGEN
    if(strTipo=='editar'|| strTipo=='continuo')
    {
        $.ajax({
            url: url_carga_datos_origen,
            type: 'post',
            data: 
            { 
                intPuntoId: intPuntoId,
                strTipo:strTipo
            },
            success: function (response)
            {
                 var strCanal=response.strCanal;
                 var strPuntoVenta=response.strPuntoVenta;
                 var intTipoNegocioId=response.intTipoNegocio;
                 var strLoginVendedor=response.objVendedor;
                 if(
                    strCanal!=""&&
                    strPuntoVenta!=""&&
                    intTipoNegocioId!=null&&
                    strLoginVendedor!=""&&
                    strCanal!=null&&
                    strPuntoVenta!=null&&
                    strLoginVendedor!=null
                     )
                 {
                    Ext.getCmp('idCanal').value=strCanal;
                    storePuntoVenta.getProxy().extraParams.canal =  Ext.getCmp('idCanal').getValue();
                    $('#infopuntoextratype_canal').val(strCanal);
                    $('#infopuntoextratype_punto_venta').val(strPuntoVenta);
                    $('#infopuntoextratype_loginVendedor').val(strLoginVendedor);
                    storePuntoVenta.load();
                    storeVendedores.load();
                    Ext.getCmp('idPuntoVenta').value=strPuntoVenta;
                    Ext.getCmp('idvendedor').value=strLoginVendedor;
                  
                    document.getElementById ('infopuntotype_tipoNegocioId').value=intTipoNegocioId;
                    document.getElementById ('tipoNegocioId').value=intTipoNegocioId;
                    $('#infopuntotype_tipoNegocioId').prop('disabled', strEditarCampos);
                  
                    if(strTipo=="editar")
                    {
                        $('#infopuntotype_tipoUbicacionId').val(response.tipoUbicacionId);
                        if(response.strOrigen!="")
                        {
                            var cmbOrigen= document.getElementById("strNombreOrigen");
                            var objOption7 = document.createElement("option");
                            objOption7.text = response.strOrigen;
                            cmbOrigen.add(objOption7);
                            var strNombreOrigen = cmbOrigen.options[cmbOrigen.selectedIndex].text;
                            if(strNombreOrigen==="ATC" && prefijoEmpresa === "MD"&&response.strPuntoAtencion!="")
                            {
                                $("#bloquePuntoAtencion").css({display:"table-row"});
                                var PuntosAtencionCombo = document.getElementById("strNombrePuntodeAtencion");
                                var objOption8= document.createElement("option");
                                objOption8.text = response.strPuntoAtencion;
                                PuntosAtencionCombo.add(objOption8);
                               
                            }
                        }
                     
                    }
                 }
                 else
                 {
                    alert("El punto de origen no cumple con los datos necesarios para ser trasladado")
                 }
              
               
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
               alert("Este punto de origen no cumple con los datos necesarios para ser trasladado");
            }
         });
    }
   
  }

/**
 * Documentación para la funcion 'validarCaracterEspecial'
 * 
 * Función que permite validar los caracteres especiales de un campo.
 * 
 * @author  Kenth Encalada <kencalada@telconet.ec>
 * @version 1.0 23-06-2023
 * 
 */
  function validarCaracterEspecial(strCampoAValidar, strNombreCampo) {
    if (prefijoEmpresa == 'TN' && strCampoAValidar != '')
    {
        const patronCaracteresEspeciales = /[\'^£$%&*()}{@#~?><>,|=+¬\/"]/gi
        const strCampo = document.getElementById(strCampoAValidar)?.value ?? '';
        if (patronCaracteresEspeciales.test(strCampo)) 
        {
            Ext.Msg.alert('Alerta', `El campo: '${strNombreCampo}' contenía caracteres inválidos, por lo que se procederá con el ajuste del campo de texto.`);
            document.getElementById(strCampoAValidar).value = strCampo.replace(patronCaracteresEspeciales, '').trim();
        }
    } 
 }