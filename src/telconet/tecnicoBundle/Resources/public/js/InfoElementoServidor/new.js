/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function presentarCantones(objeto_1, objeto_2, root, seleccion)
{
 var conn = new Ext.data.Connection();
 conn.request
      (
        {
          url: buscarCantones,
          method: 'post',
          params: {idJurisdiccion : objeto_1.value},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     if(root == 'encontrados')
                     {
                       llenarCombo(objeto_2, json.encontrados, 'encontrados', seleccion, 'nombre_canton', 'id_canton');
                     }
                   },
          failure: function(result)
                   {
                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                   }
        }
      );
}

function presentarParroquias(objeto_1, objeto_2, root, seleccion)
{
 var conn = new Ext.data.Connection();
 conn.request
      (
        {
          url: buscarParroquias,
          method: 'post',
          params: {idCanton : objeto_1.value},
          success: function(response)
                   {
                     var json = Ext.JSON.decode(response.responseText);
                     if(root == 'encontrados')
                     {
                       llenarCombo(objeto_2, json.encontrados, 'encontrados', seleccion, 'nombre_parroquia', 'id_parroquia');
                     }
                   },
          failure: function(result)
                   {
                     Ext.Msg.alert('Error ','Error: ' + result.statusText);
                   }
        }
      );
}

function llenarCombo(name_id_combo, objetos, tag, value_option_selected, valor, id)
{
    var combo_el = Ext.get(name_id_combo);
    var combo = Ext.getDom(name_id_combo); //combo_el.dom;
    var size_combo = combo.length;

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