/*cobranza*/
function getAllLinesByOV(idov, iddelivery){

    $.ajax({
        url: '../purchase/util.php',
        type: "POST",
        dataType: "json",
        async:false,
        data: {"getDetail": 'y',"id": idov, "iddelivery":iddelivery},
        success: processSuccessLinesByOV ,
        error: function () {
            console.log('Error al getAllBatch');
        }
    });
}


function processSuccessLinesByOV(data, response) {
    $("#myTable2 > tbody").empty();

    var i = 1;

    var cant = 0;
    var price = 0;
    var tot = 0;
    var namep = "";
    var totacum = 0;
    var cantacum = 0;

    var disc = 0;

    $.each(data.data, function (i, item) {
        // availableTags[_cont++] = data.data[i].dscode + "&&"+data.data[i].dsname+"&&"+data.data[i].pnid;
        price = parseFloat(data.data[i].dnprice);
        //price = parseFloat(data.data[i].dnprice - (data.data[i].dnprice * (data.data[i].disc / 100)));
        cant = parseFloat(data.data[i].dnquantity);
        cantacum += cant;
        tot = parseFloat(price * cant);

        //console.log("tot linea:"+tot);
        //console.log("disc: "+data.data[i].disc);
        //console.log("tot con desc: "+(tot - ( tot * (data.data[i].disc / 100))));
        tot = (tot - ( tot * (data.data[i].disc / 100)));

        totacum += tot;
        $("#myTable2").append("<tr>" +
            "<td>" + i + "</td>" +
            "<td style='font-size: small'>" + data.data[i].sku + "</td>" +
            "<td style='font-size: small'>" + data.data[i].dsname + "</td>" +
            "<td><input type='text' class='form-control form-control-sm' value='" + currencyFormat(price) + "' readonly/></td>" +
            "<td><input type='text' class='form-control form-control-sm' value='"+ cant+"' readonly/></td>"+
            "<td><input type='text' class='form-control form-control-sm' value='"+ currencyFormat(tot)+"' readonly/></td><tr>");
        namep = data.data[i].dsnamep;
        i++;
      });

      $("#myTable2").append("<tr>" +
          "<td colspan=4></td>" +
          "<td><input type='text' style='text-align=center' class='form-control' value='"+ cantacum+"' readonly/></td>"+
          "<td><input type='text' style='text-align=rigth'  class='form-control' value='"+ currencyFormat(totacum)+"' readonly/></td>"+
          "<tr>");

      namep = data.data[i].dsnamep;

    $('#_partnernameb').val(namep);
}

/*pago*/
function getAllLinesByPayment(idov){
  $.ajax({
      url: '../purchase/util.php',
      type: "POST",
      dataType: "json",
      async:false,
      data: {"getDetailPay": 'y',"id": idov},
      success: processSuccessLinesByPay ,
      error: function () {
          console.log('Error al getAllBatch');
      }
  });
}

function processSuccessLinesByPay(data, response) {
    $("#myTable3 > tbody").empty();

    var i = 1;

    var cant = 0;
    var price = 0;
    var tot = 0;
    var namep = "";

    $.each(data.data, function (i, item) {
        // availableTags[_cont++] = data.data[i].dscode + "&&"+data.data[i].dsname+"&&"+data.data[i].pnid;
        price = parseFloat(data.data[i].amount);

        $("#myTable3").append("<tr>" +
            "<td style='font-size: small'>" + data.data[i].id + "</td>" +
            "<td style='font-size: small'>" + data.data[i].dstype + "</td>" +
            "<td style='font-size: small'>" + data.data[i].ref + "</td>" +
            "<td><input type='text' class='form-control form-control-sm' value='" + currencyFormat(price) + "' readonly/></td>" +
            "<td style='font-size: small'>" + data.data[i].user + "</td>" +
            "</tr>");
        namep = data.data[i].fec;
        i++;

    });

    $('#_datep').val(namep);
}
