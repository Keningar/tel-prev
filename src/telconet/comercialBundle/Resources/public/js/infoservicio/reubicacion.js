Ext.require([
	'*',
	'Ext.tip.QuickTipManager',
		'Ext.window.MessageBox'
]);
var boolTrasladar = false;

Ext.onReady(function () {


    storeServiciosByLogin = new Ext.data.Store({
        total: 'total',
        pageSize: 10000,
        proxy:
            {
                type: 'ajax',
                url: url_servicios,
                reader:
                    {
                        type: 'json',
                        totalProperty: 'total',
                        root: 'listado'
                    },
                extraParams:
                    {
                        idPunto: '',
                        estado: ''
                    }
            },
        fields:
            [
                {name: 'idServicio', mapping: 'idServicio'},
                {name: 'servicio', mapping: 'servicio'},
                {name: 'estado', mapping: 'estado'},
                {name: 'strLoginAux', mapping: 'strLoginAux'},
                {name: 'strDescripcionFactura', mapping: 'strDescripcionFactura'}
            ]
    });
    
    storeServiciosByLogin.proxy.extraParams = 
                                            { idPunto : punto_id , 
                                              puntoId : punto_id,
                                              strEsReubicacion: 'SI',
                                              estado  : 'Todos' };
                                              storeServiciosByLogin.load({params: {}});

    listView = Ext.create('Ext.grid.Panel', {
        height: '200px',
        width: '922px',
        collapsible: false,
        title: 'Listado de servicvios',
        renderTo: Ext.get('servicios_traslado'),
        // paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: storeServiciosByLogin,
            displayInfo: true,
            displayMsg: 'Mostrando servicios {0} - {1} of {2}',
            emptyMsg: "No hay datos para mostrar"
        }),
        listeners:{
                            itemdblclick: function( view, record, item, index, eventobj, obj ){
                                var position = view.getPositionByEvent(eventobj),
                                data = record.data,
                                value = data[this.columns[position.column].dataIndex];
                                Ext.Msg.show({
                                    title:'Copiar texto?',
                                    msg: "Para poder copiar el contenido, seleccionar y presione Ctrl + C: <br> -&gt; <b>" + value + "</b>",
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.Msg.INFORMATION
                                });
                            }
                    },
        store: storeServiciosByLogin,
        multiSelect: false,
        viewConfig: {
            emptyText: 'No hay datos para mostrar'
        },
        columns: [new Ext.grid.RowNumberer(),
            {
                text: 'Servicio',
                width: '170px',
                dataIndex: 'servicio',
                align: 'center'
            },
            {
                text: 'Login Aux',
                width: '270px',
                dataIndex: 'strLoginAux',
                align: 'center'
            },
            {
                text: 'Descripcion Factura',
                width: '300px',
                dataIndex: 'strDescripcionFactura',
                align: 'center'
            },
            {
                text: 'Estado',
                dataIndex: 'estado',
                align: 'center',
                width: '170px'
            }]
    });

});
/**
 * Documentación para el método 'validarFormulario'.
 *
 * Valida si los servicios a trasaladar tienen un estado permitido
 * 
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.1 10-08-2016   Se agrega estado Rechazada por solicitud en ticket 37257
 * 
 * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
 * @version 1.2 09-09-2016   
 * Se agrega validación por registros seleccionados en el grid.
 * 
 * @since 1.0
 */
function validarFormulario()
{
    if (strEmpresaCod == "TN")
    {
        var esFacturableTn         = document.getElementById('esFacturableTn').value;
        var precioReubicacion      = document.getElementById('precioReubicacionTn').value;
        var descripcionReubicacion = document.getElementById('descripcionReubicacionTn').value;
        if (((esFacturableTn == "SI" && precioReubicacion == "")) || descripcionReubicacion == "")
        {
            alert("Imposible Reubicar Servicios del Login, información financiera incompleta "+
                  "(Precio de reubicación / Descripción de reubicación)");
            return false;
        }
        if (esFacturableTn == "SI" && (parseInt(precioReubicacion) <= 0 || parseInt(precioReubicacion) > 999))
        {
            alert("Imposible Reubicar Servicios del Login, el precio de la reubicación debe ser mayor a 0 y menor 999.");
            return false;
        }
    }
   
    if (confirm("Esta seguro(a) de generar la solicitud de reubicación de los servicios?")) 
    {
        Ext.MessageBox.wait("Generando solicitud", 'Mensaje');
        return true;
    } else 
    {
        return false;
    }
}

function isNumberKey(txt, evt) {

    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 46) {
        //Check if the text already contains the . character
        if (txt.value.indexOf('.') === -1) {
            return true;
        } else {
            return false;
        }
    } else {
        if (charCode > 31
             && (charCode < 48 || charCode > 57))
            return false;
    }
    return true;
}
