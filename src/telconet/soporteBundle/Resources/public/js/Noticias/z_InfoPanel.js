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
    /*id: 'img-detail-panel',*/

    width: 300,
    minWidth: 300,

    tpl: [
        '<div class="details">',
            '<tpl for=".">',
				'<br/>',
				(!Ext.isIE6? 
				'<div align="center" ><img src="{imagen_media}" width="200" height="200" /></div>' : 
				'<div style="width:74px;height:74px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'{imagen_media}\')"></div>'),
				'<br/><br/>',
                '<div class="details-info">',
                    '<b>Nombre Imagen: </b>',
                    '<span>{name}</span><br/><br/>',
					
                    '<b>Extension: </b>',
                    '<span>{extension}</span><br/><br/>',
					
                    '<b>Peso Original: </b>',
                    '<span>{peso}</span><br/><br/>',
					
                    '<b>URL Imagen: </b>',
                    '<span><a href="{url}" target="_blank">{url}</a></span>',
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