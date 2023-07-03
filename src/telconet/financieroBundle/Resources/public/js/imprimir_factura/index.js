Ext.require([
    '*',
    'Ext.tip.QuickTipManager',
    'Ext.window.MessageBox'
]);

Ext.onReady(function () {

    $(".soloFacturas").hide();
    $(".rangos").hide();


    var tiposDoc = Ext.create('Ext.data.Store', {
        fields: ['opcion', 'valor'],
        data: [{
                "opcion": "PDF Facturas",
                "valor": "FACE"
            }, {
                "opcion": "PDF Notas de Credito",
                "valor": "NCE"
            },
            {
                "opcion": "Notas de Credito (Generar Numeración)",
                "valor": "NCN"
            }
        ]
    });
    var tipo_documento_cmb = new Ext.form.ComboBox({
        xtype: 'combobox',
        id: 'cmb_tipo',
        store: tiposDoc,
        queryMode: 'local',
        name: 'idtipodocumento',
        valueField: 'valor',
        displayField: 'opcion',
        width: 250,
        lastQuery: '',
        mode: 'local',
        renderTo: 'tipoDocumento',
        listeners: {
            select:
                function (e) {

                    if (e.value == 'FACE')
                    {
                        $(".soloFacturas").show();
                        Ext.getCmp('cmb_numeracion').value = "";
                        Ext.getCmp('cmb_numeracion').setRawValue("");

                        $(".rangos").show();

                        storeNumeracion.proxy.extraParams = {tipo: e.value};
                        storeNumeracion.load({params: {}});
                    }
                    else if (e.value == 'NCE')
                    {
                        $(".soloFacturas").hide();
                        Ext.getCmp('cmb_numeracion').value = "";
                        Ext.getCmp('cmb_numeracion').setRawValue("");

                        $(".rangos").show();

                        storeNumeracion.proxy.extraParams = {tipo: e.value};
                        storeNumeracion.load({params: {}});
                    }
                    else {

                        $(".rangos").hide();
                        Ext.getCmp('cmb_numeracion').value = "";
                        Ext.getCmp('cmb_numeracion').setRawValue("");

                        $(".soloFacturas").hide();
                    }

                    Ext.getCmp('cmb_numeracion').enable();
                    $("#tipoHD").val(e.value);

                }
        }
    });
    /*********************************************************************/

    Ext.define('storeNumeracionModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'idNumeracion', type: 'int'},
            {name: 'numeracion', type: 'string'}
        ]
    });

    var storeNumeracion = Ext.create('Ext.data.Store', {
        id: 'storeNumeracion',
        model: 'storeNumeracionModel',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: 'getNumeracionXTipoDocumento',
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados',
            },
            extraParams: {
                tipo: 'FACT',
            }
        }
    });

    combo_numeracion = new Ext.form.ComboBox({
        id: 'cmb_numeracion',
        name: 'cmb_numeracion',
        queryMode: 'remote',
        width: 250,
        store: storeNumeracion,
        displayField: 'numeracion',
        valueField: 'idNumeracion',
        layout: 'anchor',
        disabled: false,
        emptyValue: '000-000',
        renderTo: "numeracion",
        listeners: {
            select: function (e) {
                $("#numeracionHD").val(e.value);
            }
        }
    });

    Ext.getCmp('cmb_numeracion').disable();

    /******************************************************/

    DTFechaLimite = new Ext.form.DateField({
        id: 'fechaLimite',
        name: 'fechaLimite',
        labelAlign: 'left',
        allowBlank: false,
        xtype: 'datefield',
        format: 'Y-m-d',
        width: 250,
        editable: false,
        renderTo: 'fechaLimite',
        listeners: {
            render: function (datefield) {
                datefield.setValue(new Date());
            }
        }
    });

    /******************************************************/

});

function validarIsNumerico(evt) {
    evt = (evt) ? evt : window.event
    var charCode = (evt.which) ? evt.which : evt.keyCode
    console.log(charCode)
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false
    }
    return true
}

function validador(e, tipo) {

    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();

    console.log(key);

    if (tipo == 'numeros') {
        letras = "0123456789";
        especiales = [8, 37, 36];
    } else if (tipo == 'letras') {
        letras = "abcdefghijklmnopqrstuvwxyz";
        especiales = [8, 37, 32, 46, 36, 38, 40, 44];
    }
    else {
        letras = "abcdefghijklmnopqrstuvwxyz0123456789";
        especiales = [8, 37, 32, 46, 36, 38, 40, 44];
    }

    //46 => .    
    //32 => ' ' espacio
    //8 => backspace       
    //37 => direccional izq
    //39 => direccional der y '
    //44 => ,

    tecla_especial = false
    for (var i in especiales) {
        if (key == especiales[i]) {
            tecla_especial = true;
            break;
        }
    }


    if (letras.indexOf(tecla) == -1 && !tecla_especial)
        return false;
}


function validarFormulario() {

    if (Ext.getCmp("cmb_tipo").value == null)
    {
        Ext.Msg.alert("Alerta", "Debe escoger el tipo de documento a generar");
        return false;
    }
    else
    {

        if (Ext.getCmp("cmb_tipo").value == 'FACT')
        {

            if (Ext.getCmp("cmb_numeracion").value == '')
            {
                Ext.Msg.alert("Alerta", "Debe elegir una numeracion para generar las Facturas");
                return false;
            }
            if (document.getElementById("infodocumentofacturacabtype_descripcion").value == 'Escoja tipo de Descripcion' &&
                Ext.getCmp("cmb_tipo").value == 'FACT')
            {
                Ext.Msg.alert("Alerta", "Debe elegir la descripcion a mostrar en la factura");
                return false;
            }
            else
            {
                if (document.getElementById("infodocumentofacturacabtype_inicio").value == "" ||
                    document.getElementById("infodocumentofacturacabtype_fin").value == "")
                {
                    Ext.Msg.alert("Alerta", "Debe ingresar el Inicio o Fin de la numeracion de facturas a imprimir");
                    return false;

                }
                else
                    return true;

            }

        } else if (Ext.getCmp("cmb_tipo").value == 'NCR')
        {
            if (Ext.getCmp("cmb_numeracion").value == '')
            {
                Ext.Msg.alert("Alerta", "Debe elegir una numeracion para generar las Nota de Crédito");
                return false;
            }
            else
            {
                if (document.getElementById("infodocumentofacturacabtype_inicio").value == "" ||
                    document.getElementById("infodocumentofacturacabtype_fin").value == "")
                {
                    Ext.Msg.alert("Alerta", "Debe ingresar el Inicio o Fin de la numeracion de Notas de Credito a imprimir");
                    return false;

                }
                else
                    return true;

            }

        } else
        {

            return true;

        }

    }
}


