Ext.Date.monthNames = [
                        "Enero",
                        "Febrero",
                        "Marzo",
                        "Abril",
                        "Mayo",
                        "Junio",
                        "Julio",
                        "Agosto",
                        "Septiembre",
                        "Octubre",
                        "Noviembre",
                        "Diciembre"
                      ];
                   
Ext.Date.getShortMonthName = function(month)
{
    return Ext.Date.monthNames[month].substring(0,3);
};

Ext.Date.monthNumbers = {Ene:0,Feb:1,Mar:2,Abr:3,May:4,Jun:5,Jul:6,Ago:7,Sep:8,Oct:9,Nov:10,Dic:11};

Ext.Date.getMonthNumber=function(name)
{
    return Ext.Date.monthNumbers[name.substring(0,1).toUpperCase()+name.substring(1,3).toLowerCase()];
};

Ext.Date.dayNames = ["Domingo","Lunes","Martes","Mi&#233;rcoles","Jueves","Viernes","S&#225;bado"];

Ext.Date.getShortDayName = function(day)
{
    if(day==3)
    {
        return"MiÃ©";
    }
    
    if(day==6)
    {
        return"SÃ¡b";
    }
        
    return Ext.Date.dayNames[day].substring(0,3);
};

if(Ext.MessageBox)
{
    Ext.MessageBox.buttonText=
    {
        ok:"Aceptar",
        cancel:"Cancelar",
        yes:"S&#237;",
        no:"No"
    };
}