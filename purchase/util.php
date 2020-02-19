<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 12/11/2018
 * Time: 05:49 PM
 */

require_once("../includes/config.php");
//_includes();
$mysql = new Mysql;
//print_r($_REQUEST);
    if (isset($_POST['_type'])) {
        $list = Partner::getAllByType($mysql, $_REQUEST['_type'], $_REQUEST['_string']);
        //print_r($list);
        header('Content-type: application/json; charset=utf-8');
        //$jsondata["data"] = $list;
        $jsondata = array();

        for ($i = 0; $i < count($list); $i++) {
            $jsondata['data'][$i] = ['pnid' => $list[$i]->pnid, 'dscode' => $list[$i]->dscode, 'dsname' => $list[$i]->dsname];
        }
        //$json_var = ['partner'=>$jsondata];
        echo json_encode($jsondata, JSON_FORCE_OBJECT);
        //echo json_encode($list);
    }
    elseif(isset($_POST['_article'])) {
        //print_r($_REQUEST);
        //$list = Item::getAllByFilter($mysql,$_POST['_article'],$_POST['_type'],$_POST['_cliente']);
        //echo "<br />IDCLIENT:".$_REQUEST['_partner'];
        if ($_POST['_typed'] == "C")
            $list = Item::getAllByFilterBySN($mysql, $_POST['_article'], $_POST['_partner']);
        else
            $list = Item::getAllByFilter($mysql, $_POST['_article'], $_POST['_cliente']);

        header('Content-type: application/json; charset=utf-8');
        $jsondata = array();

        for ($i = 0; $i < count($list); $i++) {

            $pricewithdisc = 0.0;
            if($list[$i]->pricewithdisc != "")
                $pricewithdisc = $list[$i]->pricewithdisc;

            $jsondata['article'][$i] = ['pnid' => $list[$i]->pnid,
                'dscode' => $list[$i]->dscode,
                'dsname' => $list[$i]->dsname,
                'dsserial' => $list[$i]->dsserial,
                'pricewithdisc' => $pricewithdisc];
        }
        echo json_encode($jsondata, JSON_FORCE_OBJECT);
    }
    elseif($_POST['_article2']){
        $list = Item::getAllByFilterBySNAndWhsCode($mysql, $_POST['_article2'], $_POST['_partner'], $_POST['_whsfrom']);

        header('Content-type: application/json; charset=utf-8');
        $jsondata = array();

        for ($i = 0; $i < count($list); $i++) {

            $pricewithdisc = 0.0;
            $stock = 0;
            if($list[$i]->pricewithdisc != "")
                $pricewithdisc = $list[$i]->pricewithdisc;

            if($list[$i]->stock != "")
                $stock = $list[$i]->stock;

                $jsondata['article'][$i] = ['pnid' => $list[$i]->pnid,
                'dscode' => $list[$i]->dscode,
                'dsname' => $list[$i]->dsname,
                'dsserial' => $list[$i]->dsserial,
                'stock' => $stock];
        }
        echo json_encode($jsondata, JSON_FORCE_OBJECT);
    }
    elseif(isset($_POST['_article3'])){
        $list = Item::getAllByFilterExactly($mysql, $_POST['_article3']);

        header('Content-type: application/json; charset=utf-8');
        $jsondata = array();

        for ($i = 0; $i < count($list); $i++) {

            $pricewithdisc = 0.0;
            $stock = 0;
            if($list[$i]->pricewithdisc != "")
                $pricewithdisc = $list[$i]->pricewithdisc;

            if($list[$i]->stock != "")
                $stock = $list[$i]->stock;

            $jsondata['article'][$i] = ['pnid' => $list[$i]->pnid,
                'dscode' => $list[$i]->dscode,
                'dsname' => $list[$i]->dsname,
                'dsserial' => $list[$i]->dsserial,
                'stock' => $stock];
        }
        echo json_encode($jsondata, JSON_FORCE_OBJECT);
    }
    elseif(isset($_POST['_checkser'])){
        $_POST['_checkser'] = strtoupper($_POST['_checkser']);
        if($_POST['_doc']=="entry"){
            $list = Purchase::checkSerieIntoInventoryEntry($mysql,$_POST['_checkser']);
            header('Content-type: application/json; charset=utf-8');
            $jsondata = array();

            $serie = "";
            for($i=0; $i<count($list); $i++){
                $serie .= $list[$i]->fnidserial." | ";
            }

            $jsondata['serrepeat'][0] = ['serrep' => $serie];
        }
        elseif($_POST['_doc']=="delivery"){

            //print_r($_POST);
            $list = Sales::checkSerieIntoInventoryExit($mysql,$_POST['_checkser'],$_POST['_item']);

            header('Content-type: application/json; charset=utf-8');
            $jsondata = array();

            $serie = "";
            for($i=0; $i<count($list); $i++){
                $serie .= $list[$i]->dsserial." | ";
            }

            $jsondata['serrepeat'][0] = ['serrep' => $serie];
        }
        elseif($_POST['_doc']=="checkindb") {//delivery

            $list = Sales::checkSerieInDB($mysql,$_POST['_checkser']);
            $serie = "";
            for($i=0; $i<count($list); $i++){
                $serie .= $list[$i]->dsserial." | ";
            }

            $jsondata['serrepeat'][0] = ['serrep' => $serie];

        }
        elseif($_POST['_doc']=="return"){//check in db

            $list = Sales::checkSerieValidToReturn($mysql,$_POST['_checkser'],
                                $_POST['_item'],$_POST['_ref'],$_POST['_cardcode']);
            header('Content-type: application/json; charset=utf-8');
            $jsondata = array();

            //for ($i = 0; $i < count($list); $i++) {
            if(count($list)>0){
                $jsondata['serrepeat'][0] = ['serrep' => $list[0]->fnidserial];
                $jsondata['itemid'][0] = ['itemid' => $list[0]->fniditem];
                $jsondata['lineid'][0] = ['line' => $list[0]->fnidline];
                $jsondata['headerid'][0] = ['headerid' => $list[0]->fnidheader];
                $jsondata['fnidware'][0] = ['fnidware' => $list[0]->fnidware];
            }else{
                $jsondata['serrepeat'][0] = ['serrep' => ""];
                $jsondata['itemid'][0] = ['itemid' => ""];
                $jsondata['lineid'][0] = ['line' => ""];
                $jsondata['headerid'][0] = ['headerid' => ""];
                $jsondata['fnidware'][0] = ['fnidware' => $list[0]->fnidware];
            }
            //echo json_encode($jsondata, JSON_FORCE_OBJECT);

        }
        elseif($_POST['_doc']=="transfer"){
            $list = Transfer::checkSerieValidToTransfer($mysql,$_POST['_checkser'],
                $_POST['_item'],$_POST['_whscode']);
            header('Content-type: application/json; charset=utf-8');
            $jsondata = array();
            //for ($i = 0; $i < count($list); $i++) {
            if(count($list)>0){
                $jsondata['serrepeat'][0] = ['serrep' => $list[0]->fnidserial];
                $jsondata['itemid'][0] = ['itemid' => $list[0]->fniditem];
                $jsondata['lineid'][0] = ['line' => $list[0]->fnidline];
                $jsondata['headerid'][0] = ['headerid' => $list[0]->fnidheader];
            }else{
                $jsondata['serrepeat'][0] = ['serrep' => ""];
                $jsondata['itemid'][0] = ['itemid' => ""];
                $jsondata['lineid'][0] = ['line' => ""];
                $jsondata['headerid'][0] = ['headerid' => ""];
            }
        }
        elseif($_POST['_doc']=="returnNOTSerial"){
            $list = Sales::checkSerieValidToReturnWithOutSerial($mysql,null,$_POST['_item'],$_POST['_ref'],$_POST['_cardcode']);
            header('Content-type: application/json; charset=utf-8');
            $jsondata = array();

            //for ($i = 0; $i < count($list); $i++) {
            if(count($list)>0){
                $jsondata['itemid'][0] = ['itemid' => $list[0]->fniditem];
                $jsondata['lineid'][0] = ['line' => $list[0]->fnidline];
                $jsondata['headerid'][0] = ['headerid' => $list[0]->fnidheader];
                $jsondata['fnidware'][0] = ['fnidware' => $list[0]->fnidware];
            }else{
                $jsondata['itemid'][0] = ['itemid' => ""];
                $jsondata['lineid'][0] = ['line' => ""];
                $jsondata['headerid'][0] = ['headerid' => ""];
                $jsondata['fnidware'][0] = ['fnidware' => $list[0]->fnidware];
            }
            //echo json_encode($jsondata, JSON_FORCE_OBJECT);
        }
        echo json_encode($jsondata, JSON_FORCE_OBJECT);
    }
    elseif(isset($_POST['getDetail'])){

        $list = Collection::getRowCollect($mysql, $_POST['id'],$_POST['iddelivery']);//Sales::getRow($mysql, $_POST['id']);

        header('Content-type: application/json; charset=utf-8');
        $jsondata = array();

        for ($i = 0; $i < count($list); $i++) {
            $jsondata['data'][$i] =
                ['idline' => $list[$i]->pnidline,
                'sku' => $list[$i]->dsitemcode,
                'dsname' => $list[$i]->dsitemname,
                'dnquantity' => ($list[$i]->dnquantity - $list[$i]->Dev),
                'dnprice' => $list[$i]->dnprice,
                 'dsnamep'=> $list[$i]->dsnamep,
                 'disc'=> $list[$i]->dddiscountperc
                ];
        }

        echo json_encode($jsondata, JSON_FORCE_OBJECT);
    }
    elseif(isset($_POST['getDetailPay'])){
      $listpay = Sales::getHystoricPaymentsBySO($mysql,null,$_REQUEST['id']);

      header('Content-type: application/json; charset=utf-8');
      $jsondata = array();

      $jsondata['data'][0] = ['id'=>$listpay[0]->pnid,
                              'fec'=>$listpay[0]->dddate,
                              'ref'=>$listpay[0]->dsreference,
                              'dstype'=>trim($listpay[0]->dspaymentype),
                              'amount'=>$listpay[0]->dsamount,
                              'user'=>$listpay[0]->dsuser];

      echo json_encode($jsondata, JSON_FORCE_OBJECT);
    }
?>
