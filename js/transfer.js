// JavaScript Document
var availableTags = [];
var availableTagsDM = [];

function validateFormTransfer(e){

    var i = 0;
    var _success = true;

    e.preventDefault();
    //$('#btn_add').attr('disabled', true);

    console.log("lineas:"+document.getElementById('c_detail').rows.length);

    if(document.getElementById('c_detail').rows.length<3){
        alert('Debe ingresar articulos!');
        return false;
    }

    if ($('#dispatch').val() == "add") {
        document.forms["requestform"].submit();
        return;
    }


    var qtyinline = 0;
    var nlj = 0;
    var skuinline = "";

    console.log('_idline[]: '+$('input[name="_idline[]"]').length);

    for (j = 0; j < $('input[name="_idline[]"]').length; j++) {

        if($('input[name="_idline[]"]').length > 1) {
            qtyinline = document.forms["requestform"].elements["_qtyr[]"][j].value;
            nlj = document.forms["requestform"].elements["_idline[]"][j].value;
            skuinline = document.forms["requestform"].elements["_art[]"][j].value;
        }
        else {
            qtyinline = document.forms["requestform"].elements["_qtyr[]"].value;
            nlj = document.forms["requestform"].elements["_idline[]"].value;
            skuinline = document.forms["requestform"].elements["_art[]"].value;
        }

        console.log('nlj:'+nlj);
        if($('#manser_'+nlj).val()==0){
            qtyinline = 0;
        } else {
            for (k = 0; k < $('input[name="_linea[]"]').length; k++) {
                if ($('input[name="_linea[]"]').length > 1) {
                    if (nlj == document.forms["requestform"].elements["_linea[]"][k].value)
                        qtyinline--;
                }
                else {
                    if (nlj == document.forms["requestform"].elements["_linea[]"].value)
                        qtyinline--;
                }
            }
        }//else

        if(qtyinline > 0) {
            alert('Faltan series para SKU: '+skuinline+' ID linea: ' + nlj);
            return false;
        }
    }

    /*fin valida que las cantidades coincidan con las series ingresadas*/

    /**/
    var i = 0;
    if($('input[name="_qtyo[]"]').length==1){

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
    document.forms["requestform"].submit();
    return true;

    //code to validate series into bd
    /*
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
        data: {"_checkser":_tmpser, "_doc":'transferSerial'},
        success: processSuccessSerTransfer ,
        error: function(){console.log('Error al consultar');}
    });
    */



}

/*
function processSuccessSerTransfer(data,response) {

    var _cont = 0;

    if(data.serrepeat[0].serrep.trim().length > 0){
        alert('Series inválidas almacén y artículo: '+data.serrepeat[0].serrep.trim());
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
*/

function validateToAddTransfer() {

    //code to check not excess quantity

    if($('#itemcode').val() == ""){
        alert("Debe ingresar UPC valido!");
        return false;
    }

    if($('#itemcode').val() == ""){
        alert("Debe ingresar UPC valido!");
        return false;
    }

    if($('select[name=_whsfromg]').val() == $('select[name=_whstog]').val() ||
        $('select[name=_whsfromg]').val() == 0  ||$('select[name=_whstog]').val() == 0){
        alert('Debe seleccionar almacen origen y destino');
        return false;
    }

    if( isNaN($('#qty').val())){
        alert("¡Debe ingresar cantidad!");
        return false;
    }

    if(parseFloat($('#qty').val())<0){
        alert("¡Debe ingresar cantidad válida!");
    }

    if(parseFloat($('#qty').val())> parseFloat($('#stock').val())){
        alert('Stock insuficiente');
        return false;
    }

    console.log("validateAddTransfer");

    return true;

    /*
    var linetocheck = $('#_lineaG').val();
    var contser = 0;
    $('input[name="_linea[]"]').each(function() {
        //alert( $(this).val());
        if(linetocheck == $(this).val()){
            contser += 1;
        }
    });

    if(contser >= $('#_qtyaG').val()){
        alert('Se complementaron las series para: ' + $('#_dscodeaG').val());
        $('#ok_'+linetocheck).val(1);
        return false;
    }else if((parseInt(contser) + 1) == $('#_qtyaG').val()){
        $('#_idimg_'+linetocheck).attr('src', '../images/buttons/complete.png');
        $('#ok_'+linetocheck).val(1);
    }

    //code to validate repeats
    var sertofind = $('#_serieaG').val();
    var _exist = false;
    $('input[name="_seriea[]"]').each(function() {
        //alert( $(this).val());
        if(sertofind == $(this).val()){
            alert('La seria ya fue agregada!');
            _exist = true
            return false;
        }
    });

    console.log("fin validateAddTransfer");

    if(!_exist)
        return true;
    else
        return false;

    */
}

function validateToAddTransferSerial() {

    if ($('#_dsnameG').val() == "") {
        alert("Debe ingresar UPC valido!");
        return false;
    }

    if ($('#_dscodeaG').val() == "") {
        alert("Debe ingresar UPC valido!");
        return false;
    }

    console.log("validateAdd");

    var linetocheck = $('#_lineaG').val();
    var contser = 0;
    $('input[name="_linea[]"]').each(function () {
        //alert( $(this).val());
        if (linetocheck == $(this).val()) {
            contser += 1;
        }
    });

    /*if(contser >= $('#_qtyaG').val()){
        alert('Se complementaron las series para: ' + $('#_dscodeaG').val());
        $('#ok_'+linetocheck).val(1);
        return false;
    }*/

    if (contser >= $('#_qtyaG').val()) {
        alert('Se complementaron las series para: ' + $('#_dscodeaG').val());
        $('#ok_' + linetocheck).val(1);
        return false;
    } else if ((parseInt(contser) + 1) == $('#_qtyaG').val()) {
        $('#_idimg_' + linetocheck).attr('src', '../images/buttons/complete.png');
        $('#ok_' + linetocheck).val(1);
    }

    //code to validate repeats
    var sertofind = $('#_serieaG').val();
    var _exist = false;
    $('input[name="_seriea[]"]').each(function () {
        //alert( $(this).val());
        if (sertofind == $(this).val()) {
            alert('La seria ya fue agregada!');
            _exist = true
            return false;
        }
    });

    /*validate exist into db*/

    $.ajax({
        async: false,
        url: '../purchase/util.php',
        type: "POST",
        dataType: "json",
        data: {"_checkser":$('#_serieaG').val(), "_doc":'transfer',"_item":$('#_iditemaG').val(),"_whscode":$('#_whscodeG').val()},
        success: function(data){
            if(data.serrepeat[0].serrep.trim().length <= 0){
                document.getElementById('_seriesok').value = 0;
                processdelivery = false;
                alert('Series No Disponible: '+ data.serrepeat[0].serrep.trim()+' para articulo '+$('#_dsnameG').val());
                console.log('validateToAddTransferSerial _seriesok =  false');
                return false;
            }else{
                processdelivery = true;
                document.getElementById('_seriesok').value = 1;
                console.log('validateToAddTransferSerial _seriesok =  true');
                return true;
            }
        } ,
        error: function(){console.log('Error al consultar');}
    });


    if($('#_seriesok').val()==0){
        return true;
    }

    /*end validate into db*/


    console.log("fin validateAdd");

    if (!_exist)
        return true;
    else
        return false;

}//validateToAddTransferSerial

/*realiza autollenado de articulos*/

function checkInputTrans(){

    var _get = $('#itemcode').val(); //'< ?php echo $_REQUEST['_filter'];?>';
    if (_get.length >= 4 ){
        callServicesDMTrans();
        $( "#itemcode" ).autocomplete({
            source: availableTagsDM,
            close: function(event, ui){
                setValuesSOAPTrans();
            }
        });
    }//if

}//function checkInputSN

function callServicesDMTrans(){

    //console.log('callServiceDM');
    if($('#itemcode').val().length >= 4){
        //console.log($('#articulo').val());

        $.ajax({
            url: '../purchase/util.php',
            type: "POST",
            dataType: "json",
            data:{"_article2":$("#itemcode").val(),"_partner":$("#_pnidcliente").val(),"_typed":$("#_dstype").val(),"_whsfrom":$('select[name=_whsfromg]').val()},
            success: processSuccessDMTrans,
            error: processErrorTrans
        });
    }
}//callServicesDM

function processSuccessDMTrans(data,response) {

    var _cont = 0;
    availableTagsDM = [];

    $.each(data.article, function(i,item){
        availableTagsDM[_cont++] = data.article[i].dscode + "&&"+data.article[i].dsname + "&&"+data.article[i].dsserial+"&&"+data.article[i].pnid+"&&"+data.article[i].stock;
    });

} //processSuccessDM

function processErrorTrans(data, status, req) {
    console.log(req.responseText + " " + status);
    //window.location = 'pending.php';
}

function setValuesSOAPTrans(){
    //code to copy
    //console.log('setValuesSOAP');
    var datosselec = document.getElementById('itemcode').value;
    var arr = datosselec.split('&&');
    //console.log('valida articulo');

    if(arr.length>1){
        document.getElementById('itemcode').value = arr[0].trim();
        document.getElementById('itemname').value = arr[1].trim();
        document.getElementById('manser').value = parseInt(arr[2].trim());
        document.getElementById('_idarticle').value = parseInt(arr[3].trim());
        document.getElementById('stock').value = arr[4].trim();
    }

}//function

/* fin realiza autollnado de articulos*/

function cleanValuesTrans(){
    document.getElementById('itemcode').value = "";
    document.getElementById('itemname').value = "";
    document.getElementById('manser').value = "";
    document.getElementById('stock').value = "0";
    document.getElementById('qty').value = "0";
}

function setValuesToReadSeriesTransf(idline, iditem, itemcode, itemquantity, _obj, itemname, whscode, idcount, whscodeto){
    /*    console.log("entro a serValuesToReadSeries val:" + idline);
        console.log("entro a serValuesToReadSeries val:" + iditem);
        console.log("entro a serValuesToReadSeries val:" + itemcode);
        console.log("entro a serValuesToReadSeries val:" + itemquantity);
     */
    console.log("entro a setValuesToReadSeries whscode:" + whscode);

    //console.log("_obj:"+_obj);

    var qty = 0;

    if($('#_dstype').val()=='S') {
        if (document.forms["requestform"].elements["_qtyr[]"].length > 1)
            qty = document.forms["requestform"].elements["_qtyr[]"][_obj].value;
        else
            qty = document.forms["requestform"].elements["_qtyr[]"].value;
    }else {
        if (document.forms["requestform"].elements["_qtyr[]"].length > 1)
            qty = document.forms["requestform"].elements["_qtyr[]"][_obj].value;
        else
            qty = document.forms["requestform"].elements["_qtyr[]"].value;
    }
    $('#_lineaG').val(idline);
    $('#_iditemaG').val(iditem);
    $('#_dscodeaG').val(itemcode);
    $('#_qtyaG').val(qty);
    $('#_dsnameG').val(itemname);
    $('#_whscodeG').val(whscode);
    $('#_linenumaG').val(idcount);
    $('#_whscodetoG').val(whscodeto);

    document.getElementById('_serieaG').focus();
    $("#_serieaG").focus();

}


