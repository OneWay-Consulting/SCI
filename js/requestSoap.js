// JavaScript Document
var availableTags = [];
var availableTagsDM = [];
var idupdate = "";
var whscode = "";

var webServiceURL = 'http://13.68.74.131:4090/ManagerQuote.asmx?op=ListCustomer';
var webServiceURLDM = 'http://13.68.74.131:4090/ManagerQuote.asmx?op=SearchItemsXML';
var webServiceCreate = 'http://13.68.74.131:4090/ManagerQuote.asmx?op=CreateDocument';
var webServiceRoute = 'http://13.68.74.131:4090/ManagerQuote.asmx?op=ListRouteXML';
var webServiceShipTo = 'http://13.68.74.131:4090/ManagerQuote.asmx?op=ListShipToBySNXML';
var webServiceWare = 'http://13.68.74.131:4090/ManagerQuote.asmx?op=ListWareXML';
var webServiceStock = 'http://13.68.74.131:4090/ManagerQuote.asmx?op=ListStockWareXML';



function callServicesCreate(ticket, series, warehouse){
	
	idupdate = ticket[0]['idtdticket'];	
	var messageSoapCreate = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/"><soapenv:Header/>'+
		'<soapenv:Body><tem:CreateDocument><tem:_doc><tem:Referencia>'+ticket[0]['dsreference']+'|'+idupdate+
		'</tem:Referencia><tem:CardCode>'+ticket[0]['fnidcardcode']+'</tem:CardCode><tem:DocDueDate>'+ticket[0]['dddatecreated']+
		'</tem:DocDueDate><tem:DocDate>'+ticket[0]['dddatecreated']+'</tem:DocDate><tem:Series>'+series+
		'</tem:Series><tem:Currency></tem:Currency><tem:ShipTo>'+ticket[0]['dsshipto']+
		'</tem:ShipTo><tem:Route>'+ticket[0]['dsroutecode']+'</tem:Route><tem:MainUsage>'+ticket[0]['dsusoprincipal']+
		'</tem:MainUsage><tem:FormPay>'+ticket[0]['dsformapago']+'</tem:FormPay><tem:MethodPay>'+ticket[0]['dsmetodopago']+
		'</tem:MethodPay><tem:WareHouse>'+warehouse+'</tem:WareHouse><tem:Comments>'+ticket[0]['dscomments']+'</tem:Comments><tem:Item>';
	
	//console.log(ticket);
	for(var i=0; i<ticket.length; i++){
			messageSoapCreate = messageSoapCreate + '<tem:Fac_Item><tem:ItemCode>'+ticket[i]['dsitemcode']+
				'</tem:ItemCode><tem:Quantity>'+ticket[i]['ddquantity']+'</tem:Quantity><tem:Price>'+ticket[i]['ddprice']+
				'</tem:Price><tem:Discount>'+ticket[i]['dddiscount']+'</tem:Discount>'+
				'<tem:FreeText>'+ticket[i]['dsopentext']+'</tem:FreeText></tem:Fac_Item>';
	}
	messageSoapCreate = messageSoapCreate +'</tem:Item></tem:_doc></tem:CreateDocument></soapenv:Body></soapenv:Envelope>';
	
	//console.log(messageSoapCreate);

	$.ajax({
			url: webServiceCreate, 
			type: "POST",
			dataType: "xml", 
			data: messageSoapCreate, 
			processData: false,
			contentType: "text/xml; charset=\"utf-8\"",
			success: processSuccessCreate , 
			error: processError
	});
	
	
}//function

function callServicesRoute(){
	var soapMessage = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/"><soapenv:Header/><soapenv:Body><tem:ListRouteXML><tem:_filter></tem:_filter></tem:ListRouteXML></soapenv:Body></soapenv:Envelope>';

	$.ajax({
				url: webServiceRoute, 
				type: "POST",
				dataType: "xml", 
				data: soapMessage, 
				processData: false,
				contentType: "text/xml; charset=\"utf-8\"",
				success: processSuccessRoute , 
				error: processError
		});

}//function callServicesRoute

function callServicesShipTo(){
	var soapMessage = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/"><soapenv:Header/><soapenv:Body><tem:ListShipToBySNXML><tem:_filter>'+document.getElementById('_cliente').value+'</tem:_filter></tem:ListShipToBySNXML></soapenv:Body></soapenv:Envelope>';

	//console.log(soapMessage);

	$.ajax({
				url: 'util.php',
				type: "POST",	
				dataType: "json",
				data: soapMessage, 
				processData: false,
				contentType: "text/xml; charset=\"utf-8\"",
				success: processSuccessShipTo , 
				error: processError
		});
}

function callServicesDM(){

    //console.log('callServiceDM');
    if($('#articulo').val().length >= 4){
        //console.log($('#articulo').val());

        $.ajax({
            url: '../purchase/util.php',
            type: "POST",
            dataType: "json",
            data:{"_article":$("#articulo").val(),"_partner":$("#_pnidcliente").val(),"_typed":$("#_dstype").val()},
            success: processSuccessDM,
            error: processError
        });
    }
}//callServicesDM

function callServices(){

	//console.log("***callServices***");
    //alert(document.getElementById('_dstype').value);
    //console.log($("#_dstype").val());
    //console.log($("#_cliente").val());

	if($('#_cliente').val().length >= 3){
        $.ajax({
            url: '../purchase/util.php',
            type: "POST",
            dataType: "json",
            data: {"_type":$("#_dstype").val(), "_string":$("#_cliente").val()},
            success: processSuccess ,
            error: processError
        });
	}
}

/*Code to consume Stock and warehouse*/
function callServiceWare(_whscode){

	whscode = _whscode;
    var soapMessage = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">'+
		  	 	 	  '<soapenv:Header/><soapenv:Body>'+
		              '<tem:ListWareXML><tem:_filter></tem:_filter></tem:ListWareXML></soapenv:Body></soapenv:Envelope>';

    $.ajax({
        url: webServiceWare,
        type: "POST",
        dataType: "xml",
        data: soapMessage,
        processData: false,
        contentType: "text/xml; charset=\"utf-8\"",
        success: processSuccessWare ,
        error: processError
    });
}//function callServiceWare

function processSuccessWare(data, status, req, xml, xmlHttpRequest, responseXML){
    var idroute = "";
    $('#_filter').empty();
    //alert($('#_coderoute').val());
    if(whscode == '')
	    $('#_filter').append(new Option('-Seleccione-','', true, true));

    $(req.responseXML)
        .find('ListWareXMLResult')
        .find('ResponseWare')
        .each(function(){
            if(whscode != '') {
                if (whscode == $(this).find('Code').text())
                    $('#_filter').append(new Option($(this).find('Name').text(), $(this).find('Code').text(), true, true));
            }else
                $('#_filter').append(new Option($(this).find('Name').text(), $(this).find('Code').text(), true, true));

        });

    //alert(whscode);
    if(whscode != '')
        $('#_filter').val(whscode);
    else
    	$('#_filter').val('');


}//function processSuccessWare

function callServiceStockWare(){

    var soapMessage = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/"><soapenv:Header/>'+
		              '<soapenv:Body><tem:ListStockWareXML><tem:_filter>'+$('#_art').val()+'</tem:_filter>'+
					  '<tem:_filterwhscode>'+$('#_filter').val()+'</tem:_filterwhscode>'+
		  			  '<tem:_cardcode>'+$('#_card').val()+'</tem:_cardcode>'+
        			  '</tem:ListStockWareXML>'+
					  '</soapenv:Body></soapenv:Envelope>';

    $.ajax({
        url: webServiceStock,
        type: "POST",
        dataType: "xml",
        data: soapMessage,
        processData: false,
        contentType: "text/xml; charset=\"utf-8\"",
        success: processSuccessStock ,
        error: processError
    });

}//callServiceStockWare

function processSuccessStock(data, status, req, xml, xmlHttpRequest, responseXML){

    var responsestock = [];
    var _cont = 0;

    $(req.responseXML)
        .find('ListStockWareXMLResult')
        .find('ResponseStock')
        .each(function() {
            responsestock[_cont] = new Array(5);
            responsestock[_cont][0] = $(this).find('Code').text();
            responsestock[_cont][1] = $(this).find('Name').text();
            responsestock[_cont][2] = $(this).find('Stock').text();
            responsestock[_cont][3] = $(this).find('WhsName').text();
            responsestock[_cont][5] = $(this).find('Discount').text();
            responsestock[_cont++][4] = $(this).find('Price').text();
        });
    var parametros = {"_stock" :responsestock,
                      "_prueba":1};
    console.log(parametros);
    $.ajax({
        url: '../request/filltablestock.php',
        type: "POST",
        data: parametros,  //_filter=10
        success: function(datos){
            $("#_tablefilter").html(datos);
        }
    });
}//function processSucessStock

/*End code to consume Stock and warehouse */

function processSuccess(data, response) {

    var _cont = 0;
	availableTags = [];

    //console.log("llega a processSuccess");

    $.each(data.data, function(i,item){
        //console.log(data.data[i].dscode);
        availableTags[_cont++] = data.data[i].dscode + "&&"+data.data[i].dsname+"&&"+data.data[i].pnid;
    });
}

function processSuccessDM(data,response) {

	var _cont = 0;
    availableTagsDM = [];

    $.each(data.article, function(i,item){
		availableTagsDM[_cont++] = data.article[i].dscode + "&&"+data.article[i].dsname + "&&"+data.article[i].dsserial+"&&"+data.article[i].pnid+"&&"+data.article[i].pricewithdisc;
	});

} //processSuccessDM

function processSuccessRoute(data, status, req, xml, xmlHttpRequest, responseXML){
	//var to save when is update
	var idroute = "";
			$('#_route').empty();
			$('#_route').append(new Option('-Seleccione-','', true, true));
			//alert($('#_coderoute').val());
			$(req.responseXML)
			.find('ListRouteXMLResult')
			.find('RouteResponse')
			.each(function(){
				if($('#_coderoute').val() != '')
					if($(this).find('Code').text() == $('#_coderoute').val())
						idroute = $(this).find('Code').text()+"&&"+$(this).find('Name').text();
	
				$('#_route').append(new Option($(this).find('Code').text() + "&&"+$(this).find('Name').text(), $(this).find('Code').text() + "&&"+$(this).find('Name').text(), true, true));
			});
			$('#_route').val(idroute);

			/*if($('#_route').val() == ''){
                $('#_route').val(1);
			}*/
}

function processSuccessShipTo(data, status, req, xml, xmlHttpRequest, responseXML){

	//var to save when is update
	var idshipto = "";
		
	$('#_shipto').empty();
	$('#_shipto').append(new Option('-Seleccione-','', true, true));
	//alert($('#_shiptemp').val());
	$(req.responseXML)
	.find('ListShipToBySNXMLResult')
	.find('ShipToResponse')
	.each(function(){
		//$('#_shipto').append(new Option($(this).find('address').text(), $(this).find('address').text(), true, true));
		//selected
		if($('#_shiptemp').val() != '')
			if($(this).find('address').text() == $('#_shiptemp').val())
				idshipto = 	$(this).find('address').text();
			
		$('#_shipto').append(new Option($(this).find('address').text(), $(this).find('address').text(), true, true));		
	});
	$('#_shipto').val(idshipto);
}

function processSuccessCreate(data, status, req, xml, xmlHttpRequest, responseXML) {
		var _cont = 0;
		var _code = "";
		var _msg = "";
		var _key = "";
		
		//console.log(req.responseXML);
		$(req.responseXML)
		.find('CreateDocumentResult')
		.each(function(){
			_code = $(this).find('Code').text()
			_msg = $(this).find('Message').text()
			_key = $(this).find('Key').text()
		});			
		
		//console.log("finalizo la creación");
		
		var parametros = {
                "_code" : _code,
                "_msg" : _msg,
                "_key" : _key,
                "id" : idupdate
        };
		$.ajax({
                data:  parametros, //datos que se envian a traves de ajax
                url:   '../request/updateDataTicketFromSOAP.php', //archivo que recibe la peticion
                type:  'post', //método de envio                
				success: processSuccessCreateResult                
        });		
		
}//function

function processSuccessCreateResult(resp){
	//console.log(resp);
	//$("#res").html(res);
	window.location = 'pending.php';
}

function processError(data, status, req) {
		console.log(req.responseText + " " + status);
	//window.location = 'pending.php';
} 

function checkInputSN(){

    var _get = $('#_cliente').val();
    if (_get.length >= 3 ){
        callServices();
        $( "#_cliente" ).autocomplete({
            source: availableTags,
            close: function(event, ui){
                setValuesSOAP();
            }
        });
    }//if
}//function checkInputSN

function checkInputDM(){
	//alert('llega a checkinputDM');
	//console.log("llega checkInputDM");

	var _get = $('#articulo').val(); //'< ?php echo $_REQUEST['_filter'];?>';
	if (_get.length >= 4 ){	
		callServicesDM();		
		$( "#articulo" ).autocomplete({
		  source: availableTagsDM,
		  close: function(event, ui){
			  setValuesSOAP();
		  }
		});
	}//if


}//function checkInputSN

function setValuesSOAP(){
//code to copy 
    //console.log('setValuesSOAP');
	var datosselec = document.getElementById('articulo').value;
	var arr = datosselec.split('&&');
    //console.log('valida articulo');

    if(arr.length>1){
		document.getElementById('articulo').value = arr[0].trim();
		document.getElementById('desc').value = arr[1].trim();
        document.getElementById('manser').value = parseInt(arr[2].trim());
		document.getElementById('precio').value = parseFloat(arr[4].trim());
        document.getElementById('_idarticle').value = parseInt(arr[3].trim());
	}

	//console.log('paso validación de articulo');
	datosselec = document.getElementById('_cliente').value;
	arr = datosselec.split('&&');
	if(arr.length>1){
		document.getElementById('_cliente').value = arr[0].trim();
		document.getElementById('_clientename').value = arr[1].trim();
        document.getElementById('_pnidcliente').value = arr[2].trim();
	}
}//function

/*function setValuesSOAPAlt(){
	var datosselec = document.getElementById('artalter').value;
	var arr = datosselec.split('&&'); 
		
	if(arr.length>1){
		//$('#articulo').text() = arr[0];
		//$('#_desc').text() = arr[1];
		document.getElementById('articulo').value = arr[0].trim();
		document.getElementById('desc').value = arr[1].trim();
		document.getElementById('_precio').value = arr[2].trim();
		document.getElementById('_moneda').value = parseInt(arr[3].trim());
        document.getElementById('disc').value = parseFloat(arr[4].trim());
		//$('#artalter').empty();
	}		
}
*/
function setValuesRoute(){
	var datosselec = document.getElementById('_route').value;
	var arr = datosselec.split('&&'); 
		
	if(arr.length>1){
		document.getElementById('_coderoute').value = arr[0].trim();
		document.getElementById('_nameroute').value = arr[1].trim();
	}		
}




	