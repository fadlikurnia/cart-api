<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * This class contain of all support fuction for development
 * @author : Parama_Fadli_Kurnia
 * Developer can make some additional code in this class
 */
require_once dirname(__FILE__) . '/functional.php';
require_once dirname(__FILE__) . '/mtime.php';

class Transaction {
    /* validate gender by user input */

    function __construct() {
        $this->functional = new Functional();
        $this->mtime = new Mtime();
        $this->CI = & get_instance();
        $this->CI->load->model('cart_model');
    }

    function processAddItem($apikey, $id_transaction, $id_item, $total_item) {
        $output = array();
        $user_info = $this->CI->cart_model->get_user_info($apikey);
        $item_info = $this->CI->cart_model->get_item_info($id_item);

        $acc_id = $user_info["acc_id"];
        $transaction_info = $this->CI->cart_model->get_transaction_info($id_transaction, $acc_id);
        $transaction_detail_info = $this->CI->cart_model->get_transaction_detail_info($id_transaction);
        $transaction_group_info = $this->CI->cart_model->get_transaction_group_info($id_transaction);

        $output["user_info"] = $user_info["acc_username"];
        $output["transaction_detail"]["code"] = $transaction_info["tr_code"];
        $output["transaction_detail"]["total_item"] = $transaction_group_info["sum_item"];
        $output["transaction_detail"]["total_amount"] = $transaction_group_info["sum_amount"];
        $output["item_added"]["name"] = $item_info["it_name"];
        $output["item_added"]["available_stock"] = $item_info["it_stock"];
        $output["item_added"]["request_stock"] = $total_item;

        $diff_item = $item_info["it_stock"] - $total_item;
        if ($diff_item >= 0) {
            $input = array();
            $input["trd_code"] = $transaction_info["tr_code"];
            $input["it_id"] = $item_info["it_id"];
            $input["trd_total_item"] = $total_item;
            $input["trd_amount"] = $total_item * $item_info["it_price"];
            $this->CI->cart_model->insert("transaction_detail", $input);

            $update = array();
            $update["it_stock"] = $item_info["it_stock"] - $total_item;
            $this->CI->cart_model->update("item", "it_id", $id_item, $update);

            $transaction_detail_info = $this->CI->cart_model->get_transaction_detail_info($id_transaction);
            $transaction_group_info = $this->CI->cart_model->get_transaction_group_info($id_transaction);

            $output["transaction_detail"]["code"] = $transaction_info["tr_code"];
            $output["transaction_detail"]["total_item"] = $transaction_group_info["sum_item"];
            $output["transaction_detail"]["total_amount"] = $transaction_group_info["sum_amount"];
            $output["your_cart"] = $transaction_detail_info;
        } else {
            $output["your_cart"] = $transaction_detail_info;
            $item_added = $item_info["it_name"];
            $output["error_addItem"] = "cant add $item_added because your request larger than total stock!";
        }
//        print_r($output);
        return $output;
    }

    function processRemoveItem($apikey, $id_transaction, $id_item) {
        $output = array();
        $user_info = $this->CI->cart_model->get_user_info($apikey);
        $item_info = $this->CI->cart_model->get_item_info($id_item);
        $item_cart_info = $this->CI->cart_model->get_item_cart_info($id_transaction, $id_item);

        $update = array();
        $update["it_stock"] = $item_info["it_stock"] + $item_cart_info["total_item"];
        $this->CI->cart_model->update("item", "it_id", $id_item, $update);

        $condition = array();
        $condition["trd_code"] = $id_transaction;
        $condition["it_id"] = $id_item;
        $this->CI->cart_model->delete_data_array("transaction_detail", $condition);

        $acc_id = $user_info["acc_id"];
        $transaction_info = $this->CI->cart_model->get_transaction_info($id_transaction, $acc_id);
        $transaction_detail_info = $this->CI->cart_model->get_transaction_detail_info($id_transaction);
        $transaction_group_info = $this->CI->cart_model->get_transaction_group_info($id_transaction);

        $output["user_info"] = $user_info["acc_username"];
        $output["transaction_detail"]["code"] = $transaction_info["tr_code"];
        $output["transaction_detail"]["total_item"] = $transaction_group_info["sum_item"];
        $output["transaction_detail"]["total_amount"] = $transaction_group_info["sum_amount"];
        $output["item_removed"]["name"] = $item_info["it_name"];
        $output["item_removed"]["total item"] = $item_info["it_stock"];
        $output["your_cart"] = $transaction_detail_info;
        return $output;
    }

    function processCheckout($apikey, $id_transaction, $coupon) {
        $output = array();
        $coupon_code = "";
        $id_coupon = "";
        $discount = 0;
        $total_amount_before_discount = 0;
        $total_amount_after_discount = 0;
        $user_info = $this->CI->cart_model->get_user_info($apikey);

        $acc_id = $user_info["acc_id"];
        $transaction_info = $this->CI->cart_model->get_transaction_info($id_transaction, $acc_id);
        $transaction_detail_info = $this->CI->cart_model->get_transaction_detail_info($id_transaction);
        $transaction_group_info = $this->CI->cart_model->get_transaction_group_info($id_transaction);
        $count_cart_item = sizeof($transaction_detail_info);
        if ($count_cart_item > 0) {
            if (($coupon != "NULL") || ($coupon != "0")) {
                //validate_coupon($coupon_code)
                $status_coupon = $this->CI->cart_model->validate_coupon($coupon);
//                echo $status_coupon;
                if ($status_coupon) {
                    $coupon_info = $this->CI->cart_model->get_coupon_info($coupon);
//                    print_r($coupon_info);
                    $coupon_code = $coupon;
                    $id_coupon = $coupon_info["cp_id"];
                    $discount = $coupon_info["discount"];
                    $total_amount_before_discount = $transaction_group_info["sum_amount"];
                    $total_amount_after_discount = $transaction_group_info["sum_amount"] - $discount;
                } else {
                    $coupon_code = "0";
                    $discount = 0;
                    $id_coupon = "0";
                    $total_amount_before_discount = $transaction_group_info["sum_amount"];
                    $total_amount_after_discount = $transaction_group_info["sum_amount"];
                    $output["error_checkout"] = "wrong coupon code input!";
                }
            } else {
                $coupon_code = "0";
                $discount = 0;
                $id_coupon = "0";
                $total_amount_before_discount = $transaction_group_info["sum_amount"];
                $total_amount_after_discount = $transaction_group_info["sum_amount"];
            }
            $output["user_info"] = $user_info["acc_username"];
            $output["transaction_detail"]["code"] = $transaction_info["tr_code"];
            $output["transaction_detail"]["total_item"] = $transaction_group_info["sum_item"];
            $output["transaction_detail"]["total_amount"] = $transaction_group_info["sum_amount"];
            $output["your_cart"] = $transaction_detail_info;

            $output["checkout_detail"]["coupon"] = $coupon_code;
            $output["checkout_detail"]["discount"] = $discount;
            $output["checkout_detail"]["total_amount_before_discount"] = $total_amount_before_discount;
            $output["checkout_detail"]["total_amount_after_discount"] = $total_amount_after_discount;

            $update = array();
            $update["cp_id"] = $id_coupon;
            $update["tr_total_item"] = $transaction_group_info["sum_item"];
            $update["tr_total_amount"] = $total_amount_after_discount;
            $update["tr_payment_status"] = "paid";
            $this->CI->cart_model->update("transaction", "tr_code", $id_transaction, $update);
        } else {
            $output["error_checkout"] = "cant chackout your cart still empty!";
        }
        return $output;
    }

}
