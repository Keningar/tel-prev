
Ext.onReady(function() {
    //
    if(tieneCamara == 'SI')
    {
        Ext.get('vpn_form_formato_camara_lbl').dom.hidden = true;
        Ext.get('vpn_form_formato_camara').dom.hidden     = true;
        Ext.get('vpn_form_formato_camara').dom.disabled   = true;
        $("#vpn_form_nombre").parent().append("<label id='vpn_formato_label' style='width:60px' hidden></label>");
    }
});

function cambiarVpnCamara() {
    if(Ext.get('vpn_form_es_camara').dom.value == 1){
        Ext.get('vpn_formato_label').dom.innerHTML = Ext.get('vpn_form_formato_camara').dom.value+'_';
        Ext.get('vpn_form_formato_camara_lbl').dom.hidden = false;
        Ext.get('vpn_form_formato_camara').dom.hidden     = false;
        Ext.get('vpn_form_formato_camara').dom.disabled   = false;
        Ext.get('vpn_formato_label').dom.hidden           = false;
    }else{
        Ext.get('vpn_form_formato_camara_lbl').dom.hidden = true;
        Ext.get('vpn_form_formato_camara').dom.hidden     = true;
        Ext.get('vpn_form_formato_camara').dom.disabled   = true;
        Ext.get('vpn_formato_label').dom.hidden           = true;
    }
}

function cambiarVpnFormat() {
    if(Ext.get('vpn_form_es_camara').dom.value == 1){
        Ext.get('vpn_formato_label').dom.innerHTML = Ext.get('vpn_form_formato_camara').dom.value+'_';
    }
}

function validadorVlan(e) {
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = "0123456789";
    if(letras.indexOf(tecla) == -1)
        return false;
}
