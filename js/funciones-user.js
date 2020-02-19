//validate the form

var processtoentry = false;

function validateForm() {
    var emailreg = /^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/;

//	$(".enter").click(function (){
    $(".error").remove();
    if ($("#_usuario").val() == "") {
        $("#_usuario").focus().after("<span class='error'>Ingrese usuario</span>");
        return false;
    } else if ($("#_password").val() == "" && $("#dispatch").val() == 'add') {
        $("#_password").focus().after("<span class='error'>Ingrese password</span>");
        return false;
    } else if ($("#_password").val() != "" && $("#_password").val() != $("#_passwordconf").val()) {
        //alert($("#_password").val());
        $("#_passwordconf").focus().after("<span class='error'>Los password deben ser iguales</span>");
        return false;
    } else if ($("#_rol").val() == "") {
        $("#_rol").focus().after("<span class='error'>Seleccione Rol</span>");
        return false;
        //}else if( $("#_email").val() == "" || !emailreg.test($("#_email").val()) ){
    } else if ($("#_email").val() == "") {
        $("#_email").focus().after("<span class='error'>Ingrese un email correcto</span>");
        return false;
    } else if ($("#_activo").val() == "") {
        $("#_activo").focus().after("<span class='error'>Seleccione Activo</span>");
        return false;
    } else if ($("#_nombre").val() == "") {
        $("#_nombre").focus().after("<span class='error'>Ingrese Nombre</span>");
        return false;
    }

    document.forms["requestform"].submit();
}//validate form

function validateFormPartner(){
    //alert("Se envia");
    document.forms["partnerform"].submit();
}

function validateFormItem(){
    document.forms["itemform"].submit();
}

function validateFormPurchase(){
    document.forms["requestform"].submit();

}

function validateFormConfirmDelivery(){

    if($('input[name="_art[]"]').length == 1){
        for (i = 0; i < $('input[name="_art[]"]').length; i++) {
            if((document.forms["requestform"].elements["_guia[]"].value != document.forms["requestform"].elements["_guiac[]"].value) &&
                $('#_dbvalidateguia').val() == 1){
                alert('La guia no coincide con la capturada! Articulo:' + document.forms["requestform"].elements["_art[]"].value);
                return false;
            }
        }
    }else{
        for (i = 0; i < $('input[name="_art[]"]').length; i++) {
            if((document.forms["requestform"].elements["_guia[]"][i].value != document.forms["requestform"].elements["_guiac[]"][i].value) &&
                $('#_dbvalidateguia').val() == 1){
                alert('La guia no coincide con la capturada! Articulo:' + document.forms["requestform"].elements["_art[]"][i].value);
                return false;
            }
        }
    }

    document.forms["requestform"].submit();
}

function validateFormReturn(){
    document.forms["requestform"].submit();
}

function validateFormDeliveryWithoutPArtials(){

    //code to validate inventorySalesForm

    if( $('#_status').val() == "1"){
        alert('Para guardar debe seleccionar estatus entregado!');
        return false;
    }

    var qtyinline = 0;
    var nlj = 0;
    var skutmp = '';

    for (j = 0; j < $('input[name="_idline[]"]').length; j++) {

        if($('input[name="_idline[]"]').length > 1) {
            //qtyinline = document.forms["requestform"].elements["_qty[]"][j].value; sin parciales
            qtyinline = document.forms["requestform"].elements["_qtyr[]"][j].value;
            nlj = document.forms["requestform"].elements["_idline[]"][j].value;
            skutmp = document.forms["requestform"].elements["_art[]"][j].value;
        }
        else {
            //qtyinline = document.forms["requestform"].elements["_qty[]"].value; sin parciales
            qtyinline = document.forms["requestform"].elements["_qtyr[]"].value;
            nlj = document.forms["requestform"].elements["_idline[]"].value;
            skutmp = document.forms["requestform"].elements["_art[]"].value;
        }

        if(qtyinline > 0){
            for(k=0; k< $('input[name="_linea[]"]').length; k++){
                if($('input[name="_linea[]"]').length > 1) {
                    if (nlj == document.forms["requestform"].elements["_linea[]"][k].value)
                        qtyinline--;
                }else {
                    if (nlj == document.forms["requestform"].elements["_linea[]"].value)
                        qtyinline--;
                }
            }
        }

        if(qtyinline > 0) {
            alert('Faltan series para linea: ' + nlj + ' SKU: '+skutmp);
            return;
        }
    }
    //end code to validate


    //return;

    //code to validate series into bd
    var _tmpser = "";
    var _iteminloop = "";
    var _codeinloop = "";
    var _iditemloop = "";
    var processdelivery= true;

    /*$('input[name="_seriea[]"]').each(function() {
        _tmpser = _tmpser + "'"+$(this).val()+"',";
    });
    */

    if($('input[name="_iditem[]"]').length > 1){
        for (i = 0; i < $('input[name="_iditem[]"]').length; i++) {
            _tmpser = "";
            _iteminloop = document.forms["requestform"].elements["_idline[]"].value;
            _codeinloop = document.forms["requestform"].elements["_art[]"].value;
            _iditemloop = document.forms["requestform"].elements["_iditem[]"].value;

            for (j = 0; j < $('input[name="_seriea[]"]').length; j++) {
                if (_iteminloop == document.forms["requestform"].elements["_linea[]"][j].value) {
                    _tmpser = _tmpser + "'" + document.forms["requestform"].elements["_seriea[]"][j].value + "',";
                }
            }
            _tmpser = _tmpser + "''";
            //alert(_iteminloop + '|'+ _codeinloop + '|' + _iditemloop+" ** SER ** "+_tmpser);

            /*call to check in dt*/
            $.ajax({
                url: '../purchase/util.php',
                type: "POST",
                dataType: "json",
                data: {"_checkser": _tmpser, "_doc": 'delivery', "_item": _iditemloop},
                success: function (data) {
                    if (data.serrepeat[0].serrep.trim().length > 0) {
                        alert('Series No Existen: ' + data.serrepeat[0].serrep.trim() + ' para Item:' + _codeinloop);
                        processdelivery = false;
                        document.getElementById('_seriesok').value = 0;
                        return;
                    }
                },
                error: function () {
                    console.log('Error al consultar');
                }
            });
            /*end call to check in dt*/

        }//items to check serie

    }else {
        //for (i = 0; i < $('input[name="_iditem[]"]').length; i++) {
        _tmpser = "";
        _iteminloop = document.forms["requestform"].elements["_idline[]"].value;
        _codeinloop = document.forms["requestform"].elements["_art[]"].value;
        _iditemloop = document.forms["requestform"].elements["_iditem[]"].value;

        if($('input[name="_seriea[]"]').length==1){
            _tmpser =  "'" + document.forms["requestform"].elements["_seriea[]"].value + "',";
        }else {
            for (j = 0; j < $('input[name="_seriea[]"]').length; j++) {
                if (_iteminloop == document.forms["requestform"].elements["_linea[]"][j].value) {
                    _tmpser = _tmpser + "'" + document.forms["requestform"].elements["_seriea[]"][j].value + "',";
                }
            }//for _seriea[]
        }//else if _seriea[]
        _tmpser = _tmpser + "''";

        /*call to check in dt*/
        $.ajax({
            url: '../purchase/util.php',
            type: "POST",
            dataType: "json",
            data: {"_checkser": _tmpser, "_doc": 'delivery', "_item": _iditemloop},
            success: function (data) {
                if (data.serrepeat[0].serrep.trim().length > 0) {
                    document.getElementById('_seriesok').value = 0;
                    alert('Series No Existen: ' + data.serrepeat[0].serrep.trim() + ' para Item:' + _codeinloop);
                    processdelivery = false;
                    document.getElementById('_seriesok').value = 0;
                    //setTimeout(function() { document.getElementById('_seriesok').value = 0 }, 1000);
                    document.getElementById('_seriesok').value = 0;
                    return;
                }
            },
            error: function () {
                console.log('Error al consultar');
            }
        });
        /*end call to check in dt*/

        //}//for items to check serie
    }
    //_tmpser = _tmpser + "''";

    if(processdelivery) {
        document.forms["requestform"].submit();
        //alert('lo va a procesar!');
    }else {
        alert('tiene errores');
        return false;
    }
    //document.forms["requestform"].submit();
}

function validateFormDelivery(){

    //code to validate inventorySalesForm

    if( $('#_status').val() == "1"){
        alert('Para guardar debe seleccionar estatus entregado!');
        return false;
    }

    var qtyinline = 0;
    var nlj = 0;
    var skutmp = '';

    for (j = 0; j < $('input[name="_idline[]"]').length; j++) {

        if($('input[name="_idline[]"]').length > 1) {
            //qtyinline = document.forms["requestform"].elements["_qty[]"][j].value; sin parciales
            qtyinline = document.forms["requestform"].elements["_qtyr[]"][j].value;
            nlj = document.forms["requestform"].elements["_idline[]"][j].value;
            skutmp = document.forms["requestform"].elements["_art[]"][j].value;
        }
        else {
            //qtyinline = document.forms["requestform"].elements["_qty[]"].value; sin parciales
            qtyinline = document.forms["requestform"].elements["_qtyr[]"].value;
            nlj = document.forms["requestform"].elements["_idline[]"].value;
            skutmp = document.forms["requestform"].elements["_art[]"].value;
        }

        if(qtyinline > 0){

            /*se agrega validacion para manejo con serie*/
            if($('#manser_'+nlj).val()==0){
                qtyinline = 0;
            }else{

                for(k=0; k< $('input[name="_linea[]"]').length; k++){
                    if($('input[name="_linea[]"]').length > 1) {
                        if (nlj == document.forms["requestform"].elements["_linea[]"][k].value)
                            qtyinline--;
                    }else {
                        if (nlj == document.forms["requestform"].elements["_linea[]"].value)
                            qtyinline--;
                    }
                }

                if(qtyinline > 0) {
                    alert('Faltan series para linea: ' + nlj + ' SKU: '+skutmp);
                    return;
                }

            }//else if manser = 1
        }//if qtyinline > 0

    }//for name=_idline
    //end code to validate

    console.log("paso validacion de series");
    //return;

    //code to validate series into bd
    var _tmpser = "";
    var _iteminloop = "";
    var _codeinloop = "";
    var _iditemloop = "";
    var processdelivery= true;



    if($('input[name="_iditem[]"]').length > 1){
        for (i = 0; i < $('input[name="_iditem[]"]').length; i++) {
            _tmpser = "";
            _iteminloop = document.forms["requestform"].elements["_idline[]"].value;
            _codeinloop = document.forms["requestform"].elements["_art[]"].value;
            _iditemloop = document.forms["requestform"].elements["_iditem[]"].value;



            for (j = 0; j < $('input[name="_seriea[]"]').length; j++) {
                if($('input[name="_linea[]"]').length>1) {
                    if (_iteminloop == document.forms["requestform"].elements["_linea[]"][j].value) {
                        _tmpser = _tmpser + "'" + document.forms["requestform"].elements["_seriea[]"][j].value + "',";
                    }
                }else{
                    if (_iteminloop == document.forms["requestform"].elements["_linea[]"].value) {
                        _tmpser = _tmpser + "'" + document.forms["requestform"].elements["_seriea[]"].value + "',";
                    }
                }
            }
            _tmpser = _tmpser + "''";
            //alert(_iteminloop + '|'+ _codeinloop + '|' + _iditemloop+" ** SER ** "+_tmpser);

            /*call to check in dt*/
            $.ajax({
                url: '../purchase/util.php',
                type: "POST",
                dataType: "json",
                data: {"_checkser": _tmpser, "_doc": 'delivery', "_item": _iditemloop},
                success: function (data) {
                    if (data.serrepeat[0].serrep.trim().length > 0) {
                        alert('Series No Existen: ' + data.serrepeat[0].serrep.trim() + ' para Item:' + _codeinloop);
                        processdelivery = false;
                        document.getElementById('_seriesok').value = 0;
                        return;
                    }
                },
                error: function () {
                    console.log('Error al consultar');
                }
            });
            /*end call to check in dt*/

        }//items to check serie

    }else {
        //for (i = 0; i < $('input[name="_iditem[]"]').length; i++) {
        _tmpser = "";
        _iteminloop = document.forms["requestform"].elements["_idline[]"].value;
        _codeinloop = document.forms["requestform"].elements["_art[]"].value;
        _iditemloop = document.forms["requestform"].elements["_iditem[]"].value;

        if($('input[name="_seriea[]"]').length==1){
            _tmpser =  "'" + document.forms["requestform"].elements["_seriea[]"].value + "',";
        }else {
            console.log("seriea.length:"+$('input[name="_seriea[]"]').length);
            if($('input[name="_seriea[]"]').length==1){
                if (_iteminloop == document.forms["requestform"].elements["_linea[]"].value) {
                    _tmpser = _tmpser + "'" + document.forms["requestform"].elements["_seriea[]"].value + "',";
                }
            }else {
                for (j = 0; j < $('input[name="_seriea[]"]').length; j++) {
                    if (_iteminloop == document.forms["requestform"].elements["_linea[]"][j].value) {
                        _tmpser = _tmpser + "'" + document.forms["requestform"].elements["_seriea[]"][j].value + "',";
                    }
                }//for _seriea[]
            }
        }//else if _seriea[]
        _tmpser = _tmpser + "''";
        //alert(_iteminloop + '|'+ _codeinloop + '|' + _iditemloop+" ** SER ** "+_tmpser);

        /*call to check in dt*/
        $.ajax({
            url: '../purchase/util.php',
            type: "POST",
            dataType: "json",
            data: {"_checkser": _tmpser, "_doc": 'delivery', "_item": _iditemloop},
            success: function (data) {
                if (data.serrepeat[0].serrep.trim().length > 0) {
                    document.getElementById('_seriesok').value = 0;
                    alert('Series No Existen: ' + data.serrepeat[0].serrep.trim() + ' para Item:' + _codeinloop);
                    processdelivery = false;
                    document.getElementById('_seriesok').value = 0;
                    //setTimeout(function() { document.getElementById('_seriesok').value = 0 }, 1000);
                    document.getElementById('_seriesok').value = 0;
                    return;
                }
            },
            error: function () {
                console.log('Error al consultar');
            }
        });
        /*end call to check in dt*/

        //}//for items to check serie
    }
    //_tmpser = _tmpser + "''";
    console.log("paso validacion de entregas y series");

    if(processdelivery) {
        document.forms["requestform"].submit();
        //alert('lo va a procesar!');
    }else {
        alert('tiene errores');
        return false;
    }
    //document.forms["requestform"].submit();
}

function processSuccessSerDelivery(data,response) {

    var _cont = 0;

    if(data.serrepeat[0].serrep.trim().length > 0){
        alert('Series Existentes: '+data.serrepeat[0].serrep.trim());
        processtoentry =  false;
        return;
    }else{
        console.log('NO hay series existentes');
        //alert('se envia form');
        processtoentry =  true;
        document.forms["requestform"].submit();
        return;
    }

} //processSuccessSerDelivery

function validateFormPurchaseEntry(){

    /*valida que las cantidades coincidan con las series ingresadas*/

    var qtyinline = 0;
    var qtyinlineopen = 0;
    var nlj = 0;
    var skuinline = "";

    for (j = 0; j < $('input[name="_idline[]"]').length; j++) {
        if($('input[name="_idline[]"]').length > 1) {
            qtyinline = document.forms["requestform"].elements["_qtyr[]"][j].value;
            qtyinlineopen = document.forms["requestform"].elements["_qtyo[]"][j].value;
            nlj = document.forms["requestform"].elements["_idline[]"][j].value;
            skuinline = document.forms["requestform"].elements["_art[]"][j].value;
        }
        else {
            qtyinline = document.forms["requestform"].elements["_qtyr[]"].value;
            qtyinlineopen = document.forms["requestform"].elements["_qtyo[]"].value;
            nlj = document.forms["requestform"].elements["_idline[]"].value;
            skuinline = document.forms["requestform"].elements["_art[]"].value;
        }

        console.log("nlj:"+nlj);
        /*se agrega validacion para manejo con serie*/
        if($('#manser_'+nlj).val()==0){
            qtyinline = 0;
        }else{
            for(k=0; k< $('input[name="_linea[]"]').length; k++){
                if($('input[name="_linea[]"]').length > 1){
                    if(nlj == document.forms["requestform"].elements["_linea[]"][k].value)
                        qtyinline --;
                }
                else{
                    if(nlj == document.forms["requestform"].elements["_linea[]"].value)
                        qtyinline --;
                }
            }
        }//else manser_==0

        console.log("cantidades:"+parseFloat(qtyinline) +"|"+ parseFloat(qtyinlineopen));
        if(parseFloat(qtyinline) > parseFloat(qtyinlineopen)){
            alert('Cantidad invalida para: '+skuinline+' ID linea: ' + nlj);
            return;
        }
        if(qtyinline > 0) {
            alert('Faltan series para SKU: '+skuinline+' ID linea: ' + nlj);
            return;
        }
    }

    /*fin valida que las cantidades coincidan con las series ingresadas*/

    /**/
    var i = 0;
    if($('input[name="_qtyo[]"]').length==1) {

        //if (document.forms["requestform"].elements["_aut[]"].checked) {
        if (isNaN(document.forms["requestform"].elements["_qtyr[]"].value)) {
            alert("Debe ingresar cantidades validas!");
            return false;
        } //validate numeric
        //}
    }else{
        for (i = 0; i < $('input[name="_qtyo[]"]').length; i++) {

            //alert(document.forms["requestform"].elements["_art[]"][i].value);
            if (isNaN(document.forms["requestform"].elements["_qtyr[]"][i].value)) {
                alert("Debe ingresar cantidades validas!");
                return false;
            } //validate numeric

            if (parseFloat(document.forms["requestform"].elements["_qtyr[]"][i].value) > parseFloat(document.forms["requestform"].elements["_qtyo[]"][i].value)) {
                alert('Articulo ' + document.forms["requestform"].elements["_art[]"][i].value + ' | ' + document.forms["requestform"].elements["_desc[]"][i].value + ' excede cantidad');
                return false
            }//valida qty pending

        }//for
    }
    /**/


    //code to validate series into bd
    var _tmpser = "";

    processtoentry = false;
    $('input[name="_seriea[]"]').each(function() {
        _tmpser = _tmpser + "'"+$(this).val()+"',";
    });

    _tmpser = _tmpser + "''";

    $.ajax({
        url: '../purchase/util.php',
        type: "POST",
        dataType: "json",
        data: {"_checkser":_tmpser, "_doc":'entry'},
        success: processSuccessSerEntry ,
        error: function(){console.log('Error al consultar');}
    });


    if(processtoentry){
        //alert('se envia form');
        //document.forms["requestform"].submit();
    }
}

function processSuccessSerEntry(data,response) {

    var _cont = 0;

    if(data.serrepeat[0].serrep.trim().length > 0){
        alert('Series Existentes: '+data.serrepeat[0].serrep.trim());
        processtoentry =  false;
        return;
    }else{
        console.log('NO hay series existentes');
        //alert('se envia form');
        processtoentry =  true;
        document.forms["requestform"].submit();
        return;
    }

} //processSuccessEntry

function validateFormSales(){
    document.forms["requestform"].submit();
}

function validateFormQuote(e){

    var i=0;
    var _success = true;

    e.preventDefault();
    $('#btn_add').attr('disabled', true);

    if($('#dispatch').val() == "add")
        document.forms["requestform"].submit();

    /*if($('input[name="_qtyc[]"]').length==1){


        if (document.forms["requestform"].elements["_aut[]"].checked) {

            if (isNaN(document.forms["requestform"].elements["_qtyc[]"].value)) {
                alert("Debe ingresar cantidades validas!");
                return false;
            } //validate numeric

            if (parseFloat(document.forms["requestform"].elements["_qtyc[]"].value) > parseFloat(document.forms["requestform"].elements["_stock[]"].value)) {
                alert('Articulo ' + document.forms["requestform"].elements["_art[]"].value + ' | ' + document.forms["requestform"].elements["_desc[]"].value + ' con Stock insuficiente');
                return false
            }//valida numeric

            if (parseFloat(document.forms["requestform"].elements["_qtyc[]"].value) > parseFloat(document.forms["requestform"].elements["_qtyo[]"].value)) {
                alert('Articulo ' + document.forms["requestform"].elements["_art[]"].value + ' | ' + document.forms["requestform"].elements["_desc[]"].value + ' excede cantidad');
                return false
            }//valida qty pending
        }

    }else {*/

    //for (i = 0; i < $('input[name="_idline[]"]').length; i++) {
    var i=0;
    var valinput = "";
    $('input[name="_idline[]"]').each(function() {
        valinput = '';
        //alert($(this).val());
        valinput = $(this).val();
        //alert($(this).val());
        //valinput = '_aut_'+valinput;
        //alert(valinput);
        if(document.getElementById("_aut_"+valinput) == null){
            console.log("null");
        }else {
            console.log("NOT null");
            //alert(document.forms["requestform"].elements["_qtyc[]"][i].value);
            //if (document.forms["requestform"].elements["_aut[]"][i].checked) {
            if (document.getElementById("_aut_" + valinput).checked) {

                if($('input[name="_qtyc[]"]').length==1){
                    //alert(document.forms["requestform"].elements["_art[]"][i].value);
                    if (isNaN(document.forms["requestform"].elements["_qtyc[]"].value)) {
                        _success = false;
                        alert("Debe ingresar cantidades validas!");
                        $('#btn_add').attr('disabled', false);
                        return false;
                    } //validate numeric

                    /*se quita candado de stock insuficiente*/
                    if ((parseFloat(document.forms["requestform"].elements["_qtyc[]"].value) > parseFloat(document.forms["requestform"].elements["_stock[]"].value)) && document.forms["requestform"].elements["_statuspart[]"].value == "") {
                        _success = false;
                        alert('Articulo ' + document.forms["requestform"].elements["_art[]"].value + ' | ' + document.forms["requestform"].elements["_desc[]"].value + ' con Stock insuficiente');
                        $('#btn_add').attr('disabled', false);
                        return false
                    }//valida numeric

                    if (parseFloat(document.forms["requestform"].elements["_qtyc[]"].value) > parseFloat(document.forms["requestform"].elements["_qtyo[]"].value)) {
                        _success = false;
                        alert('Articulo ' + document.forms["requestform"].elements["_art[]"].value + ' | ' + document.forms["requestform"].elements["_desc[]"].value + ' excede cantidad');
                        $('#btn_add').attr('disabled', false);
                        return false
                    }//valida qty pending

                }else{
                    //alert(document.forms["requestform"].elements["_art[]"][i].value);
                    if (isNaN(document.forms["requestform"].elements["_qtyc[]"][i].value)) {
                        _success = false;
                        alert("Debe ingresar cantidades validas!");
                        $('#btn_add').attr('disabled', false);
                        return false;
                    } //validate numeric

                    if ((parseFloat(document.forms["requestform"].elements["_qtyc[]"][i].value) > parseFloat(document.forms["requestform"].elements["_stock[]"][i].value)) && document.forms["requestform"].elements["_statuspart[]"][i].value == "" ) {
                        _success = false;
                        alert('Articulo ' + document.forms["requestform"].elements["_art[]"][i].value + ' | ' + document.forms["requestform"].elements["_desc[]"][i].value + ' con Stock insuficiente');
                        $('#btn_add').attr('disabled', false);
                        return false
                    }//valida numeric


                    if (parseFloat(document.forms["requestform"].elements["_qtyc[]"][i].value) > parseFloat(document.forms["requestform"].elements["_qtyo[]"][i].value)) {
                        _success = false;
                        alert('Articulo ' + document.forms["requestform"].elements["_art[]"][i].value + ' | ' + document.forms["requestform"].elements["_desc[]"][i].value + ' excede cantidad');
                        $('#btn_add').attr('disabled', false);
                        return false
                    }//valida qty pending
                }
            }//if _aut_ valinput checked
        }//else not null
        i+=1;
    }); //for validation
    //}
    //return false;
    if(_success)
        document.forms["requestform"].submit();

}


function hde(obj){
    if(obj.value != "" ){
        $(".error").fadeOut();
        return false;
    }
}

function checkDisp(val){
    $.ajax({
        url: 'checkdisp.php',
        type: "POST",
        data: 'user='+val,
        success: function(datos){
            $("#_disp").html(datos);
        }
    });
}

function checkPartnerCode(){

    var _tmp = "";

    if($("#_type option:selected").val() == "C"){
        _tmp = $('#_code').val().split("-");
        $('#_code').val('C-'+_tmp[1])
    }else{
        _tmp = $('#_code').val().split("-");
        $('#_code').val('P-'+_tmp[1])
    }

}

function validatePaymentForm(){

    var montrec = $('#_mpay').val();
    var montpend = $('#_mpending').val();

    if(montrec.length <= 0 ){
        alert("Debe ingresar un monto!");
        return false;
    }

    montrec = montrec.replace(",","");
    montpend = montpend.replace(",","");

    if(isNaN(montrec)){
        alert("Debe ingresar un monto valido!");
        return false;
    }

    if(montrec<0){
        alert("Debe ingresar monto mayor a 0");
        return false;
    }

    var montpendf = parseFloat(montpend.replace(",",""));
    var montrecf = parseFloat(montrec.replace(",",""));

    //alert(parseFloat(montrec) + '||' + parseFloat(montpend));

    if(montrecf > montpendf){
        alert("Debe ingresar un monto menor o igual al pendiente!");
        return false;
    }

    return true;

}

/*
function putText(){
var combo = document.getElementById("_cliente");
var selected = combo.options[combo.selectedIndex].text;
document.getElementById('_dscardcode').value = selected;
}
function putText2(){
var combo = document.getElementById("_proyecto");
var selected = combo.options[combo.selectedIndex].text;
document.getElementById('_dsproyecto').value = selected;
}
*/

function calculateQty(){

    var qtyacum = 0;

    $('input[name="_qtyr[]"]').each(function() {
        if(isNaN($(this).val())){
            alert('Debe ingresar valores validos');
            return false;
        }else {
            //alert($(this).val());
            qtyacum += parseFloat($(this).val());
        }
    });

    $("#_totalqty").text(qtyacum);
}

function currencyFormat (num) {
    return "$" + num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}
