<?php
    session_start();
    header('Content-Type: text/html; charset=utf-8');
    @$uid = $_POST['uid'];
    @$orderid = $_POST['orderid'];
    $return = array();

    try {

        $dbh = new PDO('mysql:host=localhost;dbname=shopping', 'root', '');
        //$dbh = new PDO('mysql:host=shop.othello1888.com;dbname=shopping', 'root', 'OthelloAbc*');
        $dbh->exec("SET NAMES 'utf8';");
        $return = array();

        if($uid!=""){
            foreach($dbh->query("SELECT id, order_no, create_time, status, pay_type, pay_status,negotiate_price,negotiate_status,confirm_remark,note FROM iwebshop_order where user_id='" . $uid . "' AND if_del=0 AND id=".$orderid." order by id desc;") as $row) {
                $order = array();
                $order['info']= $row;

                foreach($dbh->query("SELECT id, img, goods_price, real_price, goods_nums,goods_array FROM shopping.iwebshop_order_goods where order_id=".$row['id'].";") as $goods_row) {
                    $order['goods'][]= $goods_row;
                    //var_dump($goods_row);
                }

                $pay_type_recordset = $dbh->query("SELECT id, name, description FROM shopping.iwebshop_payment where id=".$row['pay_type']." limit 1;");
                $pay_type = $pay_type_recordset->fetch()['name'];

                //var_dump($pay_type_recordset->fetch()['description']);
                if($pay_type=="微信支付"){
                    if($order['info']['pay_status']==0 && $order['info']['negotiate_status'] == 1){

                        $pay_type = '未商议 '.$pay_type . " [未支付]";
                    }elseif($order['info']['pay_status']==0 && $order['info']['negotiate_status'] == 2)
                        $pay_type = '已商议 '.$pay_type . " [未支付]";
                    else{
                        $pay_type = $pay_type . " [已支付]";
                    }
                }
                $order['info']['pay_type']= $pay_type;
                array_push($return, $order);
            }
        }

        // var_dump($return);
        // exit();


        $dbh = null;
        //var_dump($return);
        echo json_encode($return, JSON_UNESCAPED_UNICODE);
        exit();

    } catch (PDOException $e) {
        $dbh = null;
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
        exit();
    }

?>
