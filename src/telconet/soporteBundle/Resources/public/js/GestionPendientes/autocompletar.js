
/**
 * Actualización: mejora en el match de la coincidencia al autocompletar
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.1 09-03-2022
 * 
 * Funcion que se usa para dar el efecto de autocompletar a un text input
 * @author Andrés Montero <amontero@telconet.ec>
 * @version 1.0 19-07-2018
 * @since 1.0
 */
function autocomplete(inp,inpId, arr, arrIds) {
    var currentFocus;
    //dibuja el listado
    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;
        var x = 0;
        var wordBold = ''; var wordReplace = '';
        closeAllLists();
        if (!val) { return false;}
        currentFocus = -1;
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        this.parentNode.appendChild(a);
        for (i = 0; i < arr.length; i++) {
          x = arr[i].toUpperCase().indexOf(val.toUpperCase());
          if (arr[i].substr(x, val.length).toUpperCase() == val.toUpperCase()) {
            wordBold = "<strong>" + arr[i].substr(x, val.length) + "</strong>";
            wordReplace = arr[i].substr(x, val.length);
            b = document.createElement("DIV");
            //b.innerHTML = "<strong>" + arr[i].substr(x, val.length) + "</strong>";
            //b.innerHTML += arr[i].substr(val.length);
            b.innerHTML = arr[i].replace(wordReplace, wordBold);
            b.innerHTML += "<input type='hidden' value='" + arr[i] + "'><input type='hidden' value='" + arrIds[i] + "'>";
                b.addEventListener("click", function(e) {

                inp.value = this.getElementsByTagName("input")[0].value;
                inpId.value = this.getElementsByTagName("input")[1].value;
                
                closeAllLists();
            });
            a.appendChild(b);
          }
        }
    });
    //acciona el autocompletar segun la tecla presionada
    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
          currentFocus++;
          addActive(x);
        } else if (e.keyCode == 38) {
          currentFocus--;
          addActive(x);
        } else if (e.keyCode == 13) {
          e.preventDefault();
          if (currentFocus > -1) {
            if (x) x[currentFocus].click();
          }
        }
    });
    
    /**
     * activa autocompletar
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     */
    function addActive(x) {
      if (!x) return false;
      removeActive(x);
      if (currentFocus >= x.length) currentFocus = 0;
      if (currentFocus < 0) currentFocus = (x.length - 1);
      x[currentFocus].classList.add("autocomplete-active");
    }
    /**
     * desactiva autocompletar
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     */
    function removeActive(x) {
      for (var i = 0; i < x.length; i++) {
        x[i].classList.remove("autocomplete-active");
      }
    }
    /**
     * cierra toda la lista
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-07-2018
     * @since 1.0
     */
    function closeAllLists(elmnt) {
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) 
        {
            if (elmnt != x[i] && elmnt != inp) 
            {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    //Configura el evento de cerrar toda la lista cuando haga click fuera del text input
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}

/**
 * Función que se usa para agregar carácter a la izquierda de un número
 * @author Andrés Montero <amontero@telconet.ec>
 * @param int numero   => número al que se desea agregar el carácter
 * @param int ancho    => el ancho en caracteres que se desea que tenga el número
 * @param int caracter => el carácter que se desea agregar al número 
 * @version 1.0 07-02-2019
 * @since 1.0
 */
function autoCompletarCaracterAlIzquierda( numero, ancho, caracter )
{
  ancho -= numero.toString().length;
  if ( ancho > 0 )
  {
    return new Array( ancho + (/\./.test( numero ) ? 2 : 1) ).join( caracter ) + numero;
  }
  return numero + ""; // siempre devuelve tipo cadena
}



