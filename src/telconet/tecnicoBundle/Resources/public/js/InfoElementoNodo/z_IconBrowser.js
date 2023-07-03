/**
 * @class Ext.chooser.IconBrowser
 * @extends Ext.view.View
 * @author Ed Spencer
 * 
 * This is a really basic subclass of Ext.view.View. All we're really doing here is providing the template that dataview
 * should use (the tpl property below), and a Store to get the data from. In this case we're loading data from a JSON
 * file over AJAX.
 */
Ext.define('Ext.chooser.IconBrowser', {
    extend: 'Ext.view.View',
    alias: 'widget.iconbrowser',    
    uses: 'Ext.data.Store',						
	singleSelect: true,
    overItemCls: 'x-view-over',
    itemSelector: 'div.thumb-wrap',
    tpl: [                    
            '<tpl for=".">',                
                '<div style="float:left" class="thumb-wrap">',
                    '<div align="center" class="thumb">',
                    (!Ext.isIE6? '<img src="/{url}" width="175" height="160" class="imageNodo"/>' : 
                    '<div style="width:150px;height:q50px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src="/{url}")"></div>'),
                    '</br><span><b>Tipo Imagen : </b>{tag}</span>',
                    '</br><span><b>Modificado : </b>{fechaMod}</span>',
                    '</div>',                                        
                '</div>',
            '</tpl>'        
    ],
    
    initComponent: function() {                
        
        ImageModel = Ext.define('ImageModel', {
            extend: 'Ext.data.Model',
            fields: [
                {name: 'id'},
                {name: 'nombre'},
                {name: 'url'},
                {name: 'tag'},
                {name: 'idTag'},
                {name: 'fechaMod'}
            ]
        });                   
        
        storeImagenNodo = Ext.create('Ext.data.Store', {
            model: 'ImageModel',
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: url_imagenesNodo,
                reader: {
                    type: 'json',
                    root: 'encontrados'
                },
                extraParams: {
                    idNodo: idElemento
                }
            }
        });

        this.store = storeImagenNodo;
        this.callParent(arguments);
        this.store.sort();               
    }
});
