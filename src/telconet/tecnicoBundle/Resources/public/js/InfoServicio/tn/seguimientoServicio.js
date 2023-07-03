var entidadSolicitudSeguimiento = new Seguimiento();
var winAsignacionIndividual;
var winAsignacion;

function seguimientoServicio(data, grid)
{
    winAsignacionIndividual = "";
    formPanelAsignacionIndividual = "";
    if (!winAsignacionIndividual)
    {
        var id_servicio = data.idServicio;
        if(Ext.isEmpty(id_servicio))
        {
            Ext.MessageBox.show({
            title: 'Error',
            msg: 'No se encuentra Servicio, Imposible verificar Seguimiento',
            buttons: Ext.MessageBox.OK,
            icon: Ext.MessageBox.ERROR
            }); 
            return;
        }
       
        formPanelAsignacionIndividual = Ext.create('Ext.form.Panel', {
            buttonAlign: 'center',
            bodyStyle: "none",
            frame: true,
            heigth:400,
            items:
                [{
                        xtype: 'panel',
                        border: false,
                        title: 'seguimiento',
                        id:'panel2',
                        html:"<div  class=seguimiento_content id=seguimiento_content_"+id_servicio+">\n\
                                </div><table width=100% cellpadding=1 cellspacing=0  border=0><tr><td><div overflow=scroll, \n\
                                    id=getPanelSeguimiento"+id_servicio+"></div></td></tr></table>",
                        width:1200,
                        heigth:400,
                       listeners:
                                {
                                    afterrender: function(cmp)
                                    {
                                        var idServicio = data.idServicio;
                                        grafica(idServicio);
                                        cmp.doLayout();
                                    }
                                }
                    }
                ],
            buttons: [
                {
                    text: 'Cerrar',
                    handler: function() {
                        cierraVentanaAsignacionIndividual();
                    }
                }
            ]       
        });
        
        winAsignacionIndividual = Ext.widget('window', {
                            title: 'Seguimiento de Servicios',
                            resizable: true,
                            height: 425,
                            modal: true,
                            closable: false,
                            readOnly: true,
                            autoShow: true,
                            items: (formPanelAsignacionIndividual),
                            
                                    });
         winAsignacionIndividual.show();
    }

}
function grafica (objServicio)
{
    entidadSolicitudSeguimiento.initSeguimiento(objServicio, 'seguimiento_content'+"_"+objServicio,'getPanelSeguimiento'+objServicio);
}


function cierraVentanaAsignacionIndividual() {
    winAsignacionIndividual.close();
    winAsignacionIndividual.destroy();
}