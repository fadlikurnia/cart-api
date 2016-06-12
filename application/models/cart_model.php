<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * Author : Parama Fadli Kurnia
 */

class Cart_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // insert data facebook page into table fb_page
    function insert_page($data) {
        $id = $data["pg_id"];
        $q = "";
        $q = "SELECT * FROM fb_page "
                . "WHERE pg_id = '$id'";

        $result = $this->db->query($q);
        if ($result->num_rows() == 0) {
            $this->db->insert('fb_page', $data);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    // this function handle insert process for table fb_post, fb_comment, fb_sub comment
    function special_insert($table, $data, $mode) {
        $id = "";
        $field_id = "";
        if ($mode == 1) {
            $id = $data["ps_id"];
            $field_id = "ps_id";
        } else if ($mode == 2) {
            $id = $data["cm_id"];
            $field_id = "cm_id";
        } else if ($mode == 3) {
            $id = $data["scm_id"];
            $field_id = "scm_id";
        }
        $q = "";
        $q = "SELECT * FROM $table "
                . "WHERE $field_id = '$id'";

        // get result value
        $result = $this->db->query($q);
        if ($result->num_rows() == 0) {
            $this->db->insert($table, $data);
        } else {
            $this->db->where($field_id, $id);
            // remove key is_index to update data
            unset($data["is_index"]);
//            var_dump($data);
            $this->db->update($table, $data);
        }
    }

    // function general for insert data into specific table
    function insert($table, $data) {
        $this->db->insert($table, $data);
    }

    // handle update data at table fb_crawl by pg_id
    function update($table, $key_id, $value_id, $data) {
        $this->db->where($key_id, $value_id);
        $this->db->update($table, $data);
    }

    function delete_data_array($table, $condition) {
        $this->db->where($condition);
        $this->db->delete($table);
    }

    // general function for select all data from specufuc table
    function get_all($table) {
        $q = "";
        $q = "SELECT * FROM $table";
        // output: all user information
        $result = $this->db->query($q);
        return $result;
    }

    function validate_apikey($apikey) {
        $q = "SELECT * FROM account WHERE acc_apikey = '$apikey'";
        $result = $this->db->query($q);
        if ($result->num_rows() == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function validate_item($id_item) {
        $q = "SELECT * FROM item WHERE it_id = '$id_item'";
        $result = $this->db->query($q);
        if ($result->num_rows() == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function validate_new_transaction($id_transaction) {
        $q = "SELECT * FROM transaction WHERE tr_code = '$id_transaction' "
                . "AND tr_payment_status='paid'";
        $result = $this->db->query($q);
//        echo $q;
        if ($result->num_rows() == 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    function validate_coupon($coupon_code) {
        $q = "SELECT * FROM coupon WHERE cp_code = '$coupon_code'";
        $result = $this->db->query($q);
        if ($result->num_rows() == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function get_user_info($apikey) {
        $q = "";
        $q = "SELECT * FROM account WHERE acc_apikey = '$apikey'";
        // output: all user information
        $result = $this->db->query($q);

        $data = array();
        // get user info
        foreach ($result->result_array() as $row) {
            $data["acc_id"] = $row["acc_id"];
            $data["acc_code"] = $row["acc_code"];
            $data["acc_username"] = $row["acc_username"];
        }
        return $data;
    }

    function get_item_info($id_item) {
        $q = "";
        $q = "SELECT * FROM item WHERE it_id = '$id_item'";
        // output: all user information
        $result = $this->db->query($q);

        $data = array();
        foreach ($result->result_array() as $row) {
            $data["it_id"] = $row["it_id"];
            $data["it_name"] = $row["it_name"];
            $data["it_stock"] = $row["it_stock"];
            $data["it_price"] = $row["it_price"];
            $data["it_info"] = $row["it_info"];
        }
        return $data;
    }
    
    function get_coupon_info($coupon_code) {
        $q = "";
        $q = "SELECT * FROM coupon WHERE cp_code = '$coupon_code'";
        // output: all user information
        $result = $this->db->query($q);

        $data = array();
        foreach ($result->result_array() as $row) {
            $data["cp_id"] = $row["cp_id"];
            $data["discount"] = $row["cp_price"];
            $data["info"] = $row["cp_info"];
        }
        return $data;
    }

    function get_transaction_info($id_transaction, $acc_id) {
        $q = "";
        $q = "SELECT * FROM transaction WHERE tr_code = '$id_transaction'";
        // output: all user information
        $result = $this->db->query($q);
        $data = array();
        if ($result->num_rows() == 0) {
            $input = array();
            $input["tr_code"] = $id_transaction;
            $input["acc_id"] = $acc_id;
            $this->insert("transaction", $input);

            $q = "SELECT * FROM transaction WHERE tr_code = '$id_transaction'";
            // output: all user information
            $result1 = $this->db->query($q);
            foreach ($result1->result_array() as $row) {
                $data["tr_id"] = $row["tr_id"];
                $data["tr_code"] = $row["tr_code"];
                $data["acc_id"] = $row["acc_id"];
                $data["cp_id"] = $row["cp_id"];
                $data["tr_total_item"] = $row["tr_total_item"];
//                $data["tr_total_input"] = $row["tr_total_input"];
                $data["tr_total_amount"] = $row["tr_total_amount"];
                $data["tr_payment_status"] = $row["tr_payment_status"];
            }
        } else {
            foreach ($result->result_array() as $row) {
                $data["tr_id"] = $row["tr_id"];
                $data["tr_code"] = $row["tr_code"];
                $data["acc_id"] = $row["acc_id"];
                $data["cp_id"] = $row["cp_id"];
                $data["tr_total_item"] = $row["tr_total_item"];
//                $data["tr_total_input"] = $row["tr_total_input"];
                $data["tr_total_amount"] = $row["tr_total_amount"];
                $data["tr_payment_status"] = $row["tr_payment_status"];
            }
        }
        return $data;
    }

    function get_transaction_detail_info($trd_code) {
        $q = "";
        $q = "SELECT it_name, trd_total_item, trd_amount "
                . "FROM transaction_detail td, item it "
                . "WHERE td.it_id = it.it_id "
                . "AND trd_code = '$trd_code'";
        // output: all user information
        $result = $this->db->query($q);

        $data = array();
        $idx = 0;
        foreach ($result->result_array() as $row) {
            $temp = array();
            $temp["name"] = $row["it_name"];
            $temp["total_item"] = $row["trd_total_item"];
            $temp["amount"] = $row["trd_amount"];
            $data[$idx] = $temp;
            $idx++;
        }
        return $data;
    }

    function get_transaction_group_info($trd_code) {
        $q = "";
        $q = "SELECT sum(trd_total_item) as sum_item, sum(trd_amount) as sum_amount "
                . "FROM transaction_detail "
                . "WHERE trd_code = '$trd_code' "
                . "GROUP BY trd_code";
        // output: all user information
        $result = $this->db->query($q);

        $data = array();
        if ($result->num_rows() > 0) {
            foreach ($result->result_array() as $row) {
                $data["sum_item"] = $row["sum_item"];
                $data["sum_amount"] = $row["sum_amount"];
            }
        } else {
            $data["sum_item"] = 0;
            $data["sum_amount"] = 0;
        }
        return $data;
    }
    
    function get_item_cart_info($id_transaction, $id_item) {
        $q = "";
        $q = "SELECT * FROM transaction_detail "
                . "WHERE trd_code = '$id_transaction' "
                . "AND it_id = '$id_item'";
        // output: all user information
        $result = $this->db->query($q);

        $data = array();
        foreach ($result->result_array() as $row) {
            $data["total_item"] = $row["trd_total_item"];
        }
        return $data;
    }

}

?>