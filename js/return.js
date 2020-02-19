
function setValuesToReadSeriesReturn(_codetosearch) {

    /*validate exist into db*/
    $.ajax({
        type: "POST",
        url: '../purchase/util.php',
        dataType: "json",
        data: {"_article3": _codetosearch, "_typed": 'S'},
        async: false,
        success: function (data) {
            if (data.article[0].dsname.trim().length <= 0) {
                alert('Articulo No Encontrado: ' + _codetosearch);
                $('#manser').val('');
                return false;
            } else {
                console.log('_seriesok =  true');
                $('#desc').val(data.article[0].dsname.trim());
                $('#manser').val(data.article[0].dsserial.trim());

                return true;
            }
        },
        error: function () {
            console.log('Error al consultar');
        }
    });

    console.log("exist:" + _exist);
}

function enterpressalertReturn (e){
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code == 13) { //Enter keycode

        if($('#manser').val()=="1"){
            return valideateToAddReturn();
            //return true;
        }else if($('#manser').val()=="0"){
            return validateToAddReturnWithOutSerial();
        }else{
            alert('Debe ingresar artículo valido!');
            return false;
        }
        //alert('enter');
    }
    return false;
}


function validateToAddReturnWithOutSerial(){

    /* valida si la serie fue agregada*/
    //code to validate repeats
    var _exist = false;

    /*validate exist into db*/
    $.ajax({
        url: '../purchase/util.php',
        type: "POST",
        dataType: "json",
        data: {"_checkser":$('#_serieaG').val(), "_doc":'returnNOTSerial',
            "_item":$('#_dscodeaG').val(),"_ref":$('#_reflineG').val(),
            "_cardcode":$('#_cliente').val()},
        async: false,
        success: function(data){
            if(data.itemid[0].itemid == null){
                processdelivery = false;
                alert('No existe datos para devolución con articulo '+$('#_dscodeaG').val());
                console.log('_seriesok =  false');
                document.getElementById('_seriesok').value = 0;
                //return false;
            }
            else if(data.itemid[0].itemid.trim().length <= 0){
                //document.getElementById('_seriesok').value = 0;
                processdelivery = false;
                alert('No existe datos para devolución con articulo '+$('#_dscodeaG').val());
                document.getElementById('_seriesok').value = 0;
                console.log('_seriesok =  false');
                //return false;
            }else{
                processdelivery = true;
                document.getElementById('_seriesok').value = 1;
                console.log('_seriesok =  true');
                $('#_idlineG').val(data.lineid[0].line);
                $('#_idheaderG').val(data.headerid[0].headerid);
                $('#_fniditemG').val(data.itemid[0].itemid);
                $('#_fnidwareG').val(data.fnidware[0].fnidware);
                _exist = true;
                //return true;
            }

        } ,
        error: function(){console.log('Error al consultar');}
    });


    if($('#_seriesok').val()==0 || !_exist){
        return false;
    }else{
        return true;
    }
    /*end validate into db*/
}