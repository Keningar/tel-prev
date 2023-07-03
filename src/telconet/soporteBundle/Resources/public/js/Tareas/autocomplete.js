/**
 * Archivo que permite construir un Drop Down con funcionalidad de autocomplete
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 18-05-2022
 * @since 1.0
 */

//funcion autocomplete
function autocompleteDropDown(id_ul,id_input) {
  var input, filter, ul, li, a, i;
  input = document.getElementById(id_input);
  filter = input.value.toUpperCase();
  ul = document.getElementById(id_ul);
  li = ul.getElementsByTagName("li");
  for (i = 0; i < li.length; i++) {
      a = li[i].getElementsByTagName("a")[0];
      if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
          li[i].style.display = "";
      } else {
          li[i].style.display = "none";

      }
  }
  document.getElementById(id_input).focus();
}

//función que permite mostrar el dropDown de un botton
function showDropDown(id_btn)
{
if(document.getElementById(id_btn).getAttribute("aria-expanded") !== 'true')
 {
  $("#"+id_btn).dropdown('toggle');
 }
}

//función que retorna la lista por defecto (sin valor o cargando)
function getDefaultLi(a)
{
  var action = (typeof a !== 'undefined')?a:'';
  var liHtml = "<li></li>";
  if(action == 'load')
  {
    liHtml = "<li style='text-align: center;'><span class='text-spinner'><i class='fa fa-spinner fa-spin'></i>Cargando...<span></li>";
  }                          
  return liHtml;
}

//función que retorna la lista del dropDown  
function buildListDropDown(input,cmb,arrDt,arrIds)
{
  var lisUl = getDefaultLi();
  if(arrDt.length > 0)
  {
    lisUl =  '';
  }
  var dataSetValue = '';
  for (i = 0; i < arrDt.length; i++) {
    dataSetValue = '{"input":"'+input+'","cmb":"'+cmb+'","nameValue":"'+arrDt[i]+'","idValue":"'+arrIds[i]+'"}';
    dataSetValue = dataSetValue.replace(/"/g, '\\"');
    lisUl +="<li><a onclick='setValueDropDown(\""+dataSetValue+"\")' class='li-drw-item'>"+arrDt[i]+"</a></li>";
  } 
  return lisUl;                         
}

//función que permite seleccionar un item del dropDown
function setValueDropDown(data)
{
  var dataDrw = JSON.parse(data);
  if(typeof dataDrw['input'] !== 'undefined' && typeof dataDrw['cmb'] !== 'undefined')
  {
    if($("#"+dataDrw['cmb']).hasClass( "changeInput") == true)
    {
      $('#'+dataDrw['cmb']).val(dataDrw['idValue']).trigger("change");
    }else{
      $('#'+dataDrw['cmb']).val(dataDrw['idValue']);
    }
    $('#'+dataDrw['input']).val(dataDrw['nameValue']);
    
  }
}

//función que permite setear el ancho del dropDown con el tamaño del imput
function setWidthDropDown(elmOrigen,elmUl)
{
  var box = document.querySelector('.'+elmOrigen);
  var width = box.offsetWidth;
  $('.'+elmUl).css('width',width);
}


//función que permite resetear dropDown
function resetDropDown(idUlDrw,idCombo,idInput,a,idInputVal)
{
  var action = (typeof a !== 'undefined')?a:'';
  idInputVal = (typeof idInputVal !== 'undefined')?idInputVal:'';
  if (action == 'load' && action !== '')
  {
    $('#'+idUlDrw).html(getDefaultLi(action));
  }
  else
  {
    $('#'+idUlDrw).html(getDefaultLi());
  }
  if(idInputVal !== '')
  {
    $('#'+idInputVal).val('');
  }
  $( "#"+idUlDrw).removeClass("loadList");
  $( "#"+idUlDrw).removeClass("loadListTotal");
  $('#'+idCombo).val('');
  document.getElementById(idInput).focus();

}
