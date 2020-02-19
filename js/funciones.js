var grid = null;
var timeSlide = 1000;


function paint(url,values){
	 if(values.indexOf('assign') > -1){
	 	if($('#_idsel').val()==""){
			alert('Debe seleccionar Ticket para Asignar');
			return;
		}
	 }

	 $.ajax({
			url: url,
			type: "POST",
			data: values,  //_filter=10
			success: function(datos){
				$("#_content").html(datos);
			}
		});
}

function paint_del(url,values,urlback){
 		document.location.href='userquery.php?dispatch=delete&'+values;
}

function sendAction(url,values,urlback){
	if(values.indexOf('delete') > -1)
            if(!confirm('Esta seguro que desea eliminar el registro seleccionado?'))
                return;

	if(values.indexOf('delete') > -1)
		paint_del(url,values,urlback);
	else
		paint(url,values);
}

function showAlertBox(msg){
	if(msg.indexOf("Error")!=-1 || msg.indexOf("error")!=-1){
		$('#alertBoxes').html('<div class="box-error"></div>');
		$('.box-error').hide(0).html(msg);
		$('.box-error').slideDown(timeSlide);
		setInterval(function hidediv(){$('.box-error').slideUp(300)},7000);
	}else{
		$('#alertBoxes').html('<div class="box-success"></div>');
		$('.box-success').hide(0).html(msg);
		$('.box-success').slideDown(timeSlide);
		setInterval(function hidediv(){$('.box-success').slideUp(300)},7000); //quit the message error
	}
}

function addTableRow(tbl,args){
	if(tbl == 'c_detailser')
		var table = document.getElementById(tbl).getElementsByTagName('tbody')[0];
	else
        var table = document.getElementById(tbl);

	var row = table.insertRow(table.rows.length);
	row.id=table.rows.length-1;
	var cell;
	for(var i=0; i<args.length; i++){
		cell = row.insertCell(i);
		cell.innerHTML=args[i];
		cell.align="center";
	}//for
	row.style.backgroundColor="#DDDDDD";
}//function

function deleteTableRow(tbl,row){
	if(!confirm("Desea eliminar la fila?"))
		return false;

    if(tbl == 'c_detailser')
        var table = document.getElementById(tbl).getElementsByTagName('tbody')[0];
	else
        var table = document.getElementById(tbl);

	table.deleteRow(document.getElementById(row).rowIndex);
	return true;
	//calculateSub();
}//function

function deleteTableRowSer(tbl,r){
    if(!confirm("Desea eliminar la fila?"))
        return false;

    var i = r.parentNode.parentNode.rowIndex;
    document.getElementById(tbl).deleteRow(i);

    return true;
}

function deleteTableAllRow(){

    var n = 0;
    var trs=$("#c_detail tr").length;
    $('#c_detail tr').each(function() {
        if (n>1){
            $(this).remove();
        }
        n++;
    });

}

function copyAuxToDetailSer(){

}

function cleanValues(){
    document.getElementById('articulo').value = "";
    document.getElementById('desc').value = "";
    document.getElementById('precio').value = "";
    document.getElementById('manser').value = "";
    document.getElementById('arrive').value = "";
}

function valideateToAdd() {

	/*var sertofind = $('#_serieaG').val();
	if( sertofind.length > 13 && sertofind.length < 17){
		alert('IMEI invalido, longitud!');
		return false;
	}
	*/


/*
	var _serlentmp = $('#_serieaG').val();
	if(_serlentmp.length != 15){
        alert('IMEI invalido, longitud!');
        return false;
    }
*/
	/*if( value.length < 14){
		alert('IMEI invalido, longitud!');
		return false;
	}*/

	//code to check not excess quantity

    if($('#_dsnameG').val() == ""){
        alert("Debe ingresar UPC valido!");
        return false;
    }

    if($('#_dscodeaG').val() == ""){
        alert("Debe ingresar UPC valido!");
        return false;
    }

    console.log("validateAdd");

    var linetocheck = $('#_lineaG').val();
    var contser = 0;
    $('input[name="_linea[]"]').each(function() {
        //alert( $(this).val());
        if(linetocheck == $(this).val()){
        	contser += 1;
        }
    });

    /*if(contser >= $('#_qtyaG').val()){
    	alert('Se complementaron las series para: ' + $('#_dscodeaG').val());
        $('#ok_'+linetocheck).val(1);
    	return false;
	}*/

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

    console.log("fin validateAdd");

    if(!_exist)
        return true;
    else
        return false;


}

function validateToAddPurchase(){

    //code to check not excess quantity
    var sertofind = $('#_serieaG').val();
    if( sertofind.length != 15 ){
        alert("Longitud IMEI inválido!");
        return false;
    }

    if($('#_dsnameG').val() == ""){
        alert("Debe ingresar UPC valido!");
        return false;
    }

    if($('#_dscodeaG').val() == ""){
        alert("Debe ingresar UPC valido!");
        return false;
    }

    console.log("validateAdd");

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

    console.log("fin validateAdd");

    if(!_exist)
        return true;
    else
        return false;


    }


    function valideateToAddReturn(){

        /* valida si la serie fue agregada*/
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

    /*validate exist into db*/

    $.ajax({
        url: '../purchase/util.php',
        type: "POST",
        dataType: "json",
        data: {"_checkser":$('#_serieaG').val(), "_doc":'return',
               "_item":$('#_dscodeaG').val(),"_ref":$('#_reflineG').val(),
               "_cardcode":$('#_cliente').val()},
        async: false,
        success: function(data){
            if(data.serrepeat[0].serrep.trim().length <= 0){
                document.getElementById('_seriesok').value = 0;
                processdelivery = false;
                alert('Series No Existen: '+ data.serrepeat[0].serrep.trim()+' para articulo '+$('#_dscodeaG').val());
                console.log('_seriesok =  false');
                return false;
            }else{
                processdelivery = true;
                document.getElementById('_seriesok').value = 1;
                console.log('_seriesok =  true');
                $('#_idlineG').val(data.lineid[0].line);
                $('#_idheaderG').val(data.headerid[0].headerid);
                $('#_fniditemG').val(data.itemid[0].itemid);
                $('#_fnidwareG').val(data.fnidware[0].fnidware);
                return true;
            }
        } ,
        error: function(){console.log('Error al consultar');}
    });


    if($('#_seriesok').val()==0 || _exist){
        return false;
    }else{
        return true;
    }
    /*end validate into db*/
}

function valideateToAddDelivery(){

    //validate length
    var value = $('#_serieaG').val();
    if( value.length != 15 ){
        alert('IMEI invalido, longitud!');
        return false;
    }

    //code to check not excess quantity
    var linetocheck = $('#_lineaG').val();
    var contser = 0;
    var processdelivery = true;

    $('input[name="_linea[]"]').each(function() {
        //alert( $(this).val());
        if(linetocheck == $(this).val()){
            contser += 1;
        }
    });

    /*validate exist into db*/
    $.ajax({
        type: "POST",
        url: '../purchase/util.php',
        dataType: "json",
        data: {"_checkser":$('#_serieaG').val(), "_doc":'delivery',"_item":$('#_iditemaG').val()},
        async: false,
        success: function(data){
            if(data.serrepeat[0].serrep.trim().length > 0){
                document.getElementById('_seriesok').value = 0;
                processdelivery = false;
                alert('Series No Disponible: '+ data.serrepeat[0].serrep.trim()+' para articulo '+$('#_dsnameG').val());
                console.log('_seriesok =  false');
                return false;
            }else{
                processdelivery = true;
                document.getElementById('_seriesok').value = 1;
                console.log('_seriesok =  true');
                return true;
            }
        } ,
        error: function(){console.log('Error al consultar');}
    });


    if($('#_seriesok').val()==0){
        return true;
    }
	/*end validate into db*/


    /**/
    console.log('contser:'+contser+'| _qtyaG: '+$('#_qtyaG').val());
    if(contser >= $('#_qtyaG').val()){
        alert('Se complementaron las series para: ' + $('#_dscodeaG').val());
        $('#ok_'+linetocheck).val(1);
        return false;
    }else if((parseInt(contser) + 1) == $('#_qtyaG').val()){
        //$('#_idimg_'+linetocheck).attr('src', '../images/buttons/complete.png');
        $('#ok_'+linetocheck).val(1);
        $('#_idimg_'+linetocheck).attr('src', '../images/buttons/complete.png');
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

    console.log("exist:" + _exist);

    if(!_exist && document.getElementById('_seriesok').value)
        return true;
    else
        return false;

}


function enterpressalert (e){
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code == 13) { //Enter keycode
    	//alert('enter');
		return true;
    }
    return false;
}
/*
function calculateSub(){

	var qtyarr = document.getElementsByName("_qty[]");
	var pricearr = document.getElementsByName("_price[]");
    var pricedarr = document.getElementsByName("_priced[]");
	var subarr = document.getElementsByName("_sub[]");
	var subdesc = document.getElementsByName("_curr[]");
	var sumsub = parseFloat(0.0);
	var rowdesc = parseFloat(0.0);
	var sumdesc = parseFloat(0.0);
	var sumsubdesc = parseFloat(0.0);
	var pricewithdiscount = parseFloat(0.0);


    / *  code to validate is Numeric* /
    $('input[name="_curr[]"]').each(function() {
        var aValue = $(this).val();

        if (isNaN(aValue)){
            alert('Debe Ingresar descuento valido para articulos!');
            $(this).val(0);
            return;
        }else if(!isNaN(aValue) && aValue <0){
            alert('Debe Ingresar descuento valido para articulos!');
            $(this).val(0);
            return;
        }
    });
    / * end code to validate is Numeric* /

	for(i = 0; i < subarr.length; i++){
      pricearr[i].value = parseFloat(pricearr[i].value.replace(/[^0-9-.]/g, ''));
      pricewithdiscount = parseFloat(pricearr[i].value * subdesc[i].value)/100;
      subarr[i].value = parseFloat(qtyarr[i].value) *  parseFloat(pricearr[i].value); //subtotal without discount
	  rowdesc = qtyarr[i].value * pricewithdiscount;
	  pricedarr[i].value = currencyFormat(parseFloat(pricearr[i].value - pricewithdiscount));
	  pricearr[i].value = currencyFormat(parseFloat(pricearr[i].value));
      sumdesc += rowdesc;
      sumsub +=  parseFloat(subarr[i].value);
	}

    document.getElementById('subtot').value = currencyFormat((sumsub - sumdesc));
    document.getElementById('discount').value = currencyFormat(sumdesc);
	sumsubdesc = sumsub - sumdesc;
	document.getElementById('ivatot').value = currencyFormat((sumsubdesc) * 0.16);
	document.getElementById('total').value = currencyFormat((sumsubdesc * 0.16) + sumsubdesc);
}
*/
function currencyFormat (num) {
    return "$" + num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}

function calculateNewValue(obj){
	alert(obj.value);
}

function setValuesToReadSeries(idline, iditem, itemcode, itemquantity, _obj, itemname, whscode, idcount){
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

    document.getElementById('_serieaG').focus();
    $("#_serieaG").focus();

}

function validateToSelectNextLine(){

    var linetocheck = $('#_lineaG').val();
    var contser = 0;
    var processdelivery = true;

    $('input[name="_linea[]"]').each(function() {
        //alert( $(this).val());
        if(linetocheck == $(this).val()){
            contser += 1;
        }
    });

    console.log('contser:'+contser+'| _qtyaG: '+$('#_qtyaG').val());
    if(contser >= $('#_qtyaG').val()){
        alert('Se complementaron las series para: ' + $('#_dscodeaG').val());
        return true;
    }else if((parseInt(contser) + 1) == $('#_qtyaG').val()){
        //$('#_idimg_'+linetocheck).attr('src', '../images/buttons/complete.png');
        //$('#_idimg_'+linetocheck).attr('src', '../images/buttons/complete.png');
        return true;
    }

    return false;//no cambia de linea
}
