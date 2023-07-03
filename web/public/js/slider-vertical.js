/*
 Slider vertical
 autor: coyr
 Sitio: www.xoyaz.com
 */
velocidad = 750;
tiempoEspera = 3000;
verificar = 1;
dif = 0;
timer = 0;
contador = 0;
posActividad = 0;
function moverSlider() {
    sliderAltura = $(".bloque-slider").height();
    moduloAltura = $(".modulo-slider").height() + parseFloat($(".modulo-slider").css("padding-top")) + parseFloat($(".modulo-slider").css("padding-bottom"));
    sliderTop = parseFloat($(".bloque-slider").css("top"));
    dif = sliderAltura + sliderTop;

    if (verificar == 1) {

        if (dif > moduloAltura * 4) {
           
                contador = 0.3;
                moduloAltura = 75 + contador;
          

            $(".bloque-slider").animate({top: "-=" + moduloAltura}, velocidad);
            timer = setTimeout('moverSlider()', tiempoEspera);
        }
        else {
            contador = 0;
            clearTimeout(timer);
            $(".bloque-slider").css({top: 0});
            timer = setTimeout('moverSlider()', tiempoEspera);
        }
    }
    else {
        timer = setTimeout('moverSlider()', 1000);
    }
}
function bajarSlider() {
    if (dif >= moduloAltura * 4) {
        
        $(".bloque-slider").animate({top: "-=" + moduloAltura}, velocidad);
    }
    else {
        contador = 0;
        $(".bloque-slider").css({top: 0});
        $(".bloque-slider").animate({top: "-=" + moduloAltura}, velocidad);
    }
}
function subirSlider() {
    if (sliderTop <= -moduloAltura) {
        
        $(".bloque-slider").animate({top: "+=" + moduloAltura}, velocidad);
    }
    else {
        contador = 0;
        $(".bloque-slider").css({top: -sliderAltura + moduloAltura});
        $(".bloque-slider").animate({top: "+=" + moduloAltura}, velocidad);
    }
}

function bajarActividad() {
    if(posActividad < 120) {
        posActividad = posActividad + 30;
        $("#contenedor-actividades").animate({top: "-=" + 30}, velocidad);
    }
}
function subirActividad() {
    if(posActividad > 0) {
        posActividad = posActividad - 30;
        $("#contenedor-actividades").animate({top: "+=" + 30}, velocidad);
    }
}

