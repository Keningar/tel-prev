/**
 * Documentación para cargar y guardar datos de las subredes a asignar por PE
 * 
 * @author Jonathan Montece <jmontece@telconet.ec>
 * @version 1.0 11-08-2021
 * */
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    var storePe = new Ext.data.Store({
        total: 'total',
        autoLoad: true,
        proxy: {
            async: false,
            type: 'ajax',
            url: url_getEncontradosPe,
            timeout: 400000,
            extraParams: {
                strNombreElemento: 'pe',
                strEstadoElemento: 'Activo'
                
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
                {name: 'id_elemento', mapping: 'id_elemento'},
                {name: 'nombre_elemento', mapping: 'nombre_elemento'}
            ]
    });

    

   

  
    combo_nodos = new Ext.form.ComboBox({
        id: 'combo_pe',
        name: 'combo_pe',
        fieldLabel: false,
        anchor: '100%',
        queryMode: 'local',
        width: 250,
        emptyText: 'Seleccione Pe',
        store: storePe,
        displayField: 'nombre_elemento',
        valueField: 'id_elemento',
        renderTo: 'combo_pe',
        
        forceSelection: true
    });

    

});

/*Funcion para limpiar campos de formulario de ingreso de Subredes por Pe
*
*/
function limpiarForm() {
    document.getElementById("form_new_elemento_router").reset();
  }


/*Funcion para guardar la data del formulario de ingreso de Subredes por Pe
*
*/
function guardar()
{
   

    var ip_subred       = document.getElementById('subred').value;
    var mascara_subred  = document.getElementById('mascara').value;
    var id_pe           = Ext.getCmp('combo_pe').value;
    var tipo_red        = document.getElementById('tipo_red').value;
    var uso             = document.getElementById('uso').value;
    var boolContinuar  = true;

    const boton         = document.getElementById("enviar");
    
    boton.setAttribute('disabled', "true");
    var validarForm = validacionesForm();
   
    if(validarForm == true){
           // if(ip_subred != "")
            //{
                //if( verificarValoresRepetidos() )
                   // {
                       /* boolContinuar = false;

                        //Ext.MessageBox.hide();
                        Ext.Msg.alert("Atención", "La subred " + ip_subred +mascara_subred + " ya se encuentra registrada.");

                        return false;*/
                   // } 
           //}
            Ext.Msg.confirm('Alerta', 'Desea guardar los datos asignados?', function (btn) {
            if ('yes' == btn)
            {
                var conn = new Ext.data.Connection({
                    listeners: {
                        'beforerequest': {
                            fn: function(con, opt) {
                                Ext.get(document.body).mask('Guardando Datos...');
                            },
                            scope: this
                        },
                        'requestcomplete': {
                            fn: function(con, res, opt) {
                                Ext.get(document.body).unmask();
                            },
                            scope: this
                        },
                        'requestexception': {
                            fn: function(con, res, opt) {
                                Ext.get(document.body).unmask();
                            },
                            scope: this
                        }
                    }
                });
        
                conn.request({
                    method: 'POST',
                    params: {
                        ip_subred: ip_subred,
                        mascara_subred: mascara_subred,
                        id_pe: id_pe,
                        tipo_red: tipo_red,
                        uso: uso
                    },
                    url: urlGuardarPe,
                    success: function(response) {

                        var json = Ext.JSON.decode(response.responseText);

                        if (json.success === true)
                        {
                          
                            Ext.Msg.show({
                                title: 'Mensaje',
                                msg: 'La subred: <b>'+ ip_subred +mascara_subred+ '</b> se guardó correctamente',
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.INFO,
                                fn: function(btn, text) {
                                    if (btn === 'ok') {
                                        window.location="";
                                    }
                                }
                            });
                            
                           
                        }
                        else
                        {
                            Ext.Msg.alert('Error ', json.mensaje);
                            boton.disabled = false;
                        }
                    },
                    failure: function(response) {
                        Ext.Msg.alert('Alerta ', 'Error al guardar');
                        boton.disabled = false;
                    }
                });
                
            }
            else
            {
                boton.disabled = false;
            }
        });
    }
    else
    {
        boton.disabled = false;
    }
   

}

function verificarValoresRepetidos()
{
    var intTotalOpciones = gridOpciones.getStore().getCount();
    var intContador      = 0;

    for(var i=0; i < intTotalOpciones ; i++)
    {
        var valorParametroOpcion1 = gridOpciones.getStore().getAt(i).get('valorParametro');
        
        for(var j=i+1; j < intTotalOpciones ; j++)
        {
            var valorParametroOpcion2 = gridOpciones.getStore().getAt(j).get('valorParametro');
            
            if(valorParametroOpcion1 == valorParametroOpcion2 )
            {
                intContador++;
            }
        }
    }
    
    if( intContador > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function validador(e, tipo) {

    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();

    console.log(key);
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

function validacionesForm() {
    //validar nombre caja
    if (document.getElementById("subred").value == "") 
    {
        alert("Falta llenar campo subred");
        return false;
    }
    const element=document.getElementById('subred');

    // Patron para validar la ip

    const patronIp=new RegExp(/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/gm);

    if (element.value.search(patronIp)!=0) {

        // Ip no es correcta
        alert("La ip no es correcta");
        return false;

    } 

    //validar ip
    if (document.getElementById("mascara").value == "") 
    {
        alert("Falta escoger mascara");
        return false;
    }
  

    //validar Pe
    var datosPe  = combo_nodos.getValue();
    if (datosPe === null ||  datosPe === '') 
    {
        alert("Falta escoger el nombre del Pe");
        return false;
    }
    
    //validar Tipo
    if (document.getElementById("tipo_red").value == "") 
    {
        alert("Falta escoger el tipo de red");
        return false;
    }
     //validar Uso
     if (document.getElementById("uso").value == "") 
    {
        alert("Falta escoger el uso");
        return false;
    }

   
    return true;
}

function validacion_ip() {
      /**

     * Función para validar una dirección ip

     * @param idElement

     */


        const element=document.getElementById('subred');

 

        // Patron para validar la ip

        const patronIp=new RegExp(/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/gm);

        if (element.value.search(patronIp)==0) {

            // Ip correcta

            element.style.color="#000";

        } else {

            // Ip incorrecta

            element.style.color="#f00";

        }
    }


function presentarCantones(objeto_1, objeto_2, accion, root, seleccion)
{
    var conn = new Ext.data.Connection();
    conn.request
        (
            {
                url: '../../../administracion/general/admi_canton/' + accion,
                method: 'post',
                params: {idJurisdiccion: objeto_1.value},
                success: function(response)
                {
                    var json = Ext.JSON.decode(response.responseText);
                    if (root == 'encontrados')
                    {
                        llenarCombo(objeto_2, json.encontrados, 'encontrados', seleccion, 'nombre_canton', 'id_canton');
                    }
                },
                failure: function(result)
                {
                    Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
                }
            }
        );
}

function llenarCombo(name_id_combo, objetos, tag, value_option_selected, valor, id)
{
    var combo_el = Ext.get(name_id_combo);
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
    if (value_option_selected)
    {
        var el_option = combo_el.query('option[value=' + value_option_selected + ']');

        el_option = Ext.get(el_option[0]);
        el_option.dom.selected = true;
    }
}


function getAprovisionamientoIp()
{
    var modeloElementoId = $("#telconet_schemabundle_infoelementoolttype_modeloElementoId").val();
    
    Ext.Ajax.request
    ({
        url: strUrlGetOpcionesAprovisionamientos,
        method: 'post',
        async: false,
        params: { modeloElementoId: modeloElementoId },
        success: function(response)
        {
            $("#selectAprovisionamiento").html(response.responseText);
            
            if( modeloElementoId == '' || modeloElementoId == null)
            {
                $("#aprovisionamiento" ).prop( "disabled", true );
            }
            else
            {
                $("#aprovisionamiento" ).prop( "disabled", false );
            }
        },
        failure: function(result)
        {
            Ext.Msg.alert('Error ', 'Error: ' + result.statusText);
        }
    });
}