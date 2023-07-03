/*
 * This example features a window with a DataView from which the user can select images to add to a <div> on the page.
 * To create the example we create simple subclasses of Window, DataView and Panel. When the user selects an image
 * we just add it to the page using the insertSelectedImage function below.
 * 
 * Our subclasses all sit under the Ext.chooser namespace so the first thing we do is tell Ext's class loader that it
 * can find those classes in this directory (InfoPanel.js, IconBrowser.js and Window.js). Then we just need to require
 * those files and pass in an onReady callback that will be called as soon as everything is loaded.
 */

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.chooser', strUrlChooser);
Ext.Loader.setPath('Ext.ux', strUrlUx);

Ext.require
([
    'Ext.button.Button',
    'Ext.data.proxy.Ajax',
    'Ext.chooser.z_InfoPanel',
    'Ext.chooser.z_IconBrowser',
    'Ext.chooser.z_Panel',
    'Ext.ux.DataView.Animated',
    'Ext.toolbar.Spacer'
]);

var winImagenes;

Ext.onReady(function() 
{
    /*
     * Here is where we create the window from which the user can select images to insert into the 'images' div.
     * This window is a simple subclass of Ext.window.Window, and you can see its source code in Window.js.
     * All we do here is attach a listener for when the 'selected' event is fired - when this happens it means
     * the user has double clicked an image in the window so we call our insertSelectedImage function to add it
     * to the DOM (see below).
     */
    winImagenes = Ext.create('Ext.chooser.z_Panel',
    {
        id: 'img-chooser-dlg',
        listeners:
        {
            selected: insertSelectedImage
        }
    });
    
    /*
     * This function is called whenever the user double-clicks an image inside the window. It creates
     * a new <img> tag inside the 'images' div and immediately hides it. We then call the show() function
     * with a duration of 500ms to fade the image in. At the end we call .frame() to give a visual cue
     * to the user that the image has been inserted
     */
    function insertSelectedImage(image)
    {
        //create the new image tag
        var image = Ext.fly('images').createChild
        ({
            tag: 'img',
            src: 'icons/' + image.get('thumb')
        });
        
        //hide it straight away then fade it in over 500ms, finally use the frame animation to give emphasis
        image.hide().show({duration: 500}).frame();
        
        //this will make the window animate back to the newly inserted image element
        winImagenes.animateTarget = image;
    }
});
