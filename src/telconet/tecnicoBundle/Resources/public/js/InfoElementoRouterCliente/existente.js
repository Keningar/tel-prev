Ext.onReady(function() {

    var storeNodo = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: url_getEncontradosNodo,
            timeout: 400000,
            extraParams: {
                nombreElemento: '',
                modeloElemento: '',
                marcaElemento:  '',
                canton:         '',
                jurisdiccion:   '',
                estado:         'Pendiente'
            },
            reader: {
                type: 'json',
                totalProperty: 'total',
                root: 'encontrados'
            },
            limitParam: undefined,
            startParam: undefined,
        },
        fields:
            [
                {name: 'idElemento', mapping: 'idElemento'},
                {name: 'idServicio', mapping: 'idServicio'},
                {name: 'nombreElemento', mapping: 'nombreElemento'}
            ]
    });

    
    var storeServiciosDatosByPunto = new Ext.data.Store({
    proxy: {
        type: 'ajax',
        url : url_get_servicios_datos,
        reader: {
            type: 'json',
            totalProperty: 'total',
            root: 'registros'
        }
    },
    fields:
            [
                {name:'idElemento'      , mapping: 'idElemento'  },
                {name:'descripcion'     , mapping: 'descripcion' }
            ]
});
            
    
    combo_nodos = new Ext.form.ComboBox({
        id: 'combo_nodos',
        name: 'combo_nodos',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Nodo',
        store: storeNodo,
        displayField: 'nombreElemento',
        valueField: 'idElemento',
        renderTo: 'combo_nodos',
        forceSelection: true,
        listeners:{
            select:{
                fn:function(combo, value) {
                    var objeto = combo.valueModels[0].raw;
                    document.getElementById("login").value = objeto.login;
                    document.getElementById("id_punto").value = objeto.idPunto;
                    document.getElementById("id_nodo").value = objeto.idElemento;
                    
                    if(objeto.idPunto)
                    {
                        storeServiciosDatosByPunto.proxy.extraParams = {idPunto: objeto.idPunto,
                                                                        estado: 'Activo' };
                        storeServiciosDatosByPunto.load({params: {}});
                    }
                    else
                    {
                        storeServiciosDatosByPunto.loadData([],false);
                        Ext.getCmp('combo_servicios').setRawValue('');
                        document.getElementById("id_elemento").value = '';
                        document.getElementById("id_punto").value = '';
                        document.getElementById("id_servicio").value = '';
                        alert('El nodo no está asociado a un cliente');                       
                        
                    }
                    
                }
            }
        }
    });
    
    
    combo_servicios = new Ext.form.ComboBox({
        id: 'combo_servicios',
        name: 'combo_servicios',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione ',
        store: storeServiciosDatosByPunto,
        displayField: 'descripcion',
        valueField: 'idElemento',
        renderTo: 'combo_servicios',
        forceSelection: true,
        listeners:{
            select:{
                fn:function(combo, value) {
                    var objeto = combo.valueModels[0].raw;
                    document.getElementById("id_elemento").value = combo.getValue();
                    document.getElementById("id_servicio").value = objeto.idServicio;
                }
            }
        }
    });


});


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

    if(document.getElementById("capacidad").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    
    if(document.getElementById("id_nodo").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
    if(document.getElementById("id_elemento").value==""){
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }

    if ( idNodo == "" || idNodo == null) {
        alert("Favor ingresar los campos obligatorios *");
        return false;
    }
    
   
    return true;
}

