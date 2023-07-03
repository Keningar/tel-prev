/**
 * @class Ext.chooser.IconBrowser
 * @extends Ext.view.View
 * @author Ed Spencer
 * 
 * This is a really basic subclass of Ext.view.View. All we're really doing here is providing the template that dataview
 * should use (the tpl property below), and a Store to get the data from. In this case we're loading data from a JSON
 * file over AJAX.
 */

Ext.define('Ext.chooser.IconBrowser',
{
    extend: 'Ext.view.View',
    alias: 'widget.iconbrowser',    
    uses: 'Ext.data.Store',						
    singleSelect: true,
    overItemCls: 'x-view-over',
    itemSelector: 'div.thumb-wrap',
    tpl:
    [                    
        '<tpl for=".">',                
            '<div style="float:left;" class="thumb-wrap">',
                '<div align="center" class="thumb">',
                (!Ext.isIE6? '<img src="{strUrlImagen}" width="125" height="100" class="imageNodo"/>' : 
                '<div style="width:120px;height:80px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src="{strUrlImagen}")"></div>'),
                '</br><span><b>Creado: </b>{strFechaCreacion}</span>',
                '<tpl if="strEstadoEvaluacion">',
                    '</br><span><b>Estado: </b>{strEstadoEvaluacion}</span>',
                '<tpl else>',
                    '</br><span>&nbsp;</span>',
                '</tpl>',
                '</div>',                                        
            '</div>',
        '</tpl>'        
    ],
    
    initComponent: function()
    {
        this.store = storeImagenes;
        this.callParent(arguments);
        this.store.sort();               
    }
});
