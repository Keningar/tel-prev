/**
 * @class Ext.chooser.InfoPanel
 * @extends Ext.panel.Panel
 * @author Ed Spencer
 * 
 * This panel subclass just displays information about an image. We have a simple template set via the tpl property,
 * and a single function (loadRecord) which updates the contents with information about another image.
 */

Ext.define('Ext.chooser.InfoPanel',
{
    extend: 'Ext.panel.Panel',
    alias : 'widget.infopanel',   
    id:'viewPanel',
    width: 350,
    minWidth: 400,
    tpl: [
        '<div class="details">',
            '<tpl for=".">',
                '<br/>',
                '<tpl if="strUrlImagenAntes">',
                    '<div align="center" style="display:inline-block; margin-right:25px;"> <div> <p style="text-align: center; font-size: 15px;"> <b> Antes </b> </p> </div> <img src="{strUrlImagenAntes}" width="200" height="200" /></div>',
                    '<div align="center" style="display:inline-block; margin-left:25px;" > <div> <p style="text-align: center; font-size: 15px;"> <b> Despues </b> </p> </div> <img src="{strUrlImagen}" width="200" height="200" /></div>',
                    '<br/>',
                    '<br/>',
                '<tpl else>',
                    (!Ext.isIE6? 
                    '<div align="center" ><img src="{strUrlImagen}" width="260" height="200" /></div>' : 
                    '<div style="width:74px;height:74px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src="{strUrlImagen}")"></div>'),
                    '<div align="center" style="margin-top: 3px;">',                                               
                        '<a href="'+strUrlDescargarImagen+'?intIdImagen={intIdImagen}" target="_blank" class="button-grid-descargarImg">',
                        '</a>',
                    '</div>',
                '</tpl>',
                '<br/>',
                '<div align="center" class="details-info">',
                    '<div class="fila">',
                        '<div class="label">',
                            'Nombre Imagen:',
                        '</div>',
                        '<div class="descripcion" style="width: auto;">',
                            '{strNombreImagen}',
                        '</div>',
                    '</div>',
                    '<tpl if="strContenidoAdicImagen">',
                        '{strContenidoAdicImagen}',
                    '</tpl>',
                    '<div class="fila">',
                        '<div class="label">',
                            'Creado por:',
                        '</div>',
                        '<div class="descripcion">',
                            '{strPersonaNombre}',
                        '</div>',
                    '</div>',
                    '<div class="fila">',
                        '<div class="label">',
                            'Fecha Creaci&oacute;n:',
                        '</div>',
                        '<div class="descripcion">',
                            '{strFechaCreacion}',
                        '</div>',
                    '</div>',
                    '<div class="fila">',
                        '<div class="label">',
                            'Longitud:',
                        '</div>',
                        '<div class="descripcion">',
                            '{strLongitud}',
                        '</div>',
                    '</div>',
                    '<div class="fila">',
                            '<div class="label">',
                                'Latitud:',
                            '</div>',
                            '<div class="descripcion">',
                                '{strLatitud}',
                            '</div>',
                    '</div>',
                    '<tpl if="strLatitud">',
                        '<tpl if="strLongitud">',
                            '<div class="fila">',
                                '<a onclick="showVerMapa({strLatitud}, {strLongitud})" class="">',
                                '<img src="/public/images/images_crud/gmaps.png" title="Ubicación en el mapa" class="button-crud">',
                                '</a>',
                            '</div>',
                        '</tpl>',
                    '</tpl>',
                    '<tpl if="strInfoEvaluacionImg">',
                        '{strInfoEvaluacionImg}',
                    '</tpl>',
                    '</div>',
                    '<div id="tipoImagenEdit"></div><br/>',
                    '<input type="hidden" id="idImagen" value="{id}" />',                 
                '</div>',
            '</tpl>',
        '</div>'
    ],
     
    afterRender: function()
    {                               
        this.callParent();
        if (!Ext.isWebKit)
        {
            this.el.on('click', function()
            {}, this, {delegate: 'a'});
        }    
    },

    /**
     * Loads a given image record into the panel. Animates the newly-updated panel in from the left over 250ms.
     */
    loadRecord: function(image)
    {
        this.body.hide();
        this.tpl.overwrite(this.body, image.data);
        this.body.slideIn('l', 
        {
            duration: 250
        });
    },
    
    clear: function()
    {
        this.body.update('');
    }
});
