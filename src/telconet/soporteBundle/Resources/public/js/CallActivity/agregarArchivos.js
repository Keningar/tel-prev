jQuery(document).ready(function() {                 
        jQuery('#agregar_archivo').click(function() { 
            var imagenesList = jQuery('#imagenes-fields-list');
            var newWidget = imagenesList.attr('data-prototype');
            var name='__name__';
            newWidget = newWidget.replace(name, filesCount);
            newWidget = newWidget.replace(name, filesCount);

            filesCount++;
            // crea un nuevo elemento lista y lo añade a la lista
            var newLi = jQuery('<li></li>').html(newWidget);
            newLi.appendTo(jQuery('#imagenes-fields-list'));
            
            return false;
        });
    });
