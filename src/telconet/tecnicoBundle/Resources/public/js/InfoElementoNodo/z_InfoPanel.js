/**
 * @class Ext.chooser.InfoPanel
 * @extends Ext.panel.Panel
 * @author Ed Spencer
 * 
 * This panel subclass just displays information about an image. We have a simple template set via the tpl property,
 * and a single function (loadRecord) which updates the contents with information about another image.
 */
Ext.define('Ext.chooser.InfoPanel', {
    extend: 'Ext.panel.Panel',
    alias : 'widget.infopanel',   
    id:'viewPanel',
    width: 350,
    minWidth: 400,
    tpl: [
        '<div class="details">',
            '<tpl for=".">',
				'<br/>',
				(!Ext.isIE6? 
				'<div align="center" ><img src="/{url}" width="260" height="200" /></div>' : 
				'<div style="width:74px;height:74px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src="/{url}")"></div>'),
				'<br/><br/>',
                '<div align="center" class="details-info">',

                    '<div id="tipoImagenEdit"></div><br/>',
                    '<input type="hidden" id="idTag" value="{idTag}" />',
                    '<input type="hidden" id="idTagNuevo" />',
                    '<input type="hidden" id="tag" value="{tag}" />',
                    '<input type="hidden" id="idImagen" value="{id}" />',

                    '<div align="center"><span class="height20px">',                                               
                        '<a class="button-crud" onclick="borrarImagen({id})">Eliminar Imagen</a>',
                    '</span></div>',
					                  
                '</div>',
            '</tpl>',
        '</div>'
    ],
     
    afterRender: function(){                               
        this.callParent();
        if (!Ext.isWebKit) {
            this.el.on('click', function(){
                //alert('The Sencha Touch examples are intended to work on WebKit browsers. They may not display correctly in other browsers.');
            }, this, {delegate: 'a'});
        }    
    },

    /**
     * Loads a given image record into the panel. Animates the newly-updated panel in from the left over 250ms.
     */
    loadRecord: function(image) {
        this.body.hide();
        this.tpl.overwrite(this.body, image.data);
        this.body.slideIn('l', {
            duration: 250
        });
    },
    
    clear: function(){
        this.body.update('');
    }
});

