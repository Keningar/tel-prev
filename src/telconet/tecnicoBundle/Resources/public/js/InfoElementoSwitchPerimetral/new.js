Ext.onReady(function() {

    var storeNodo = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_getEncontradosNodo,
            timeout: 40000,
            extraParams: {
                tipoElemento:   'NODO WIFI',
                estado:         'Activo'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'nombreElemento', mapping: 'nombreElemento'}
            ]
    });

    combo_nodos = new Ext.form.ComboBox({
        selectOnFocus: true,
        loadingText: 'Buscando ...',
        hideTrigger: false,
        xtype: 'combobox',
        name: 'combo_nodos',
        id: 'combo_nodos',
        typeAhead: true,
        triggerAction: 'all',
        displayField: 'nombreElemento',
        queryMode: "remote",
        valueField: 'idElemento',
        selectOnTab: true,
        store: storeNodo,
        width: 250,
        lazyRender: true,
        listClass: 'x-combo-list-small',
        forceSelection: true,
        emptyText: 'Escriba el nombre...',
        renderTo: 'combo_nodos',
        minChars: 3        
    });


});


function validaNaf()
{
    var objModeloElemento = document.getElementById("telconet_schemabundle_infoelementoswitchperimetraltype_modeloElementoId");
    var objSerie = document.getElementById("telconet_schemabundle_infoelementoswitchperimetraltype_serieFisica");
    var objMac = document.getElementById("mac");
    if (objSerie.value == "")
    {
        Ext.Msg.alert('Advertencia', "Ingrese la serie física");
        objModeloElemento.value = '';
        return false;
    }
    if (objModeloElemento.value == "") {
        Ext.Msg.alert('Advertencia', "Favor ingresar los campos obligatorios *");
        return false;
    }
    else
    {
        var strNombreModelo = objModeloElemento.options[objModeloElemento.selectedIndex].text;
    }

    Ext.Ajax.request({
        url: buscarCpeNaf,
        method: 'post',
        params: {
            serieCpe: objSerie.value,
            modeloElemento: strNombreModelo,
            estado: 'PI',
            bandera: 'ActivarServicio'
        },
        success: function(response) {
            var respuesta = response.responseText.split("|");
            var status = respuesta[0];
            var mensaje = respuesta[1].split(",");
            var macRadio = mensaje[1];

            if (status == "OK")
            {
                objMac.value = macRadio;
            }
            else
            {
                Ext.Msg.alert('Mensaje ', mensaje);
                objModeloElemento.value = '';
            }
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
    return true;
}

function limpiaModeloElemento()
{
    var objModeloElemento = document.getElementById("telconet_schemabundle_infoelementoswitchperimetraltype_modeloElementoId");
    objModeloElemento.value = '';
}


/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function validador(e, tipo) {

    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();

    if (tipo == 'numeros') {
        letras = "0123456789";
        especiales = [45, 46];
    } else if (tipo == 'letras') {
        letras = "abcdefghijklmnopqrstuvwxyz";
        especiales = [8, 36, 35, 45, 47, 40, 41, 46, 32, 37, 39];
    }
    else if (tipo == 'ip') {
        letras = "0123456789";
        especiales = [8, 46];
    }
    else {
        letras = "abcdefghijklmnopqrstuvwxyz0123456789";
        especiales = [8, 36, 35, 45, 47, 40, 41, 46];
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

function validacionesForm(){

    var idNodo = combo_nodos.value;

    if(document.getElementById("telconet_schemabundle_infoelementoswitchperimetraltype_nombreElemento").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementoswitchperimetraltype_descripcionElemento").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }

    if ( idNodo == "" || idNodo == null) {
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementoswitchperimetraltype_modeloElementoId").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }  
    
    if(document.getElementById("telconet_schemabundle_infoelementoswitchperimetraltype_serieFisica").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("telconet_schemabundle_infoelementoswitchperimetraltype_versionOs").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    } 
    
    var mac = document.getElementById("mac").value;

    if (mac == "" || mac == null) 
    {
        alert("Favor ingresar los campos obligatorios **");
        return false;
    }
    else
    {
        var bandera = 1;
        if (mac.length != 14){ bandera = 0;}
        if (mac.charAt(4) != ".")
        {bandera = 0;}
        if (mac.charAt(9) != ".")
        {bandera = 0;}
        if (bandera == 0)
        {
            alert("Favor ingrese la mac en formato correcto (aaaa.bbbb.cccc) * ");
            return false;   
        }
    }    
   
    return true;
}

