<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * Author: Parama Fadli Kurnia
 */

class Cart extends CI_Controller {
    /* init the controller */

    // define the constructor
    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('functional');
        $this->load->library('transaction');
        $this->load->library('mtime');
        $this->load->model('cart_model');
        MY_EXECUTION_TIME;
        MY_MEMORY_LIMIT;
        MYTIME;
    }

    /* add-item:
      rest.labanian.com/cart/addItem/TRANS1/BARANG1/2?apikey=1234XXX
     * http://localhost/cart-api/cart/addItem/AXBD123/2/2?apikey=DKpsPzAYK5utMJcaIwlMWTYx|1659864696117076
     */

    function addItem() {
        $data = array();
        $result = array();
        $message = "";
        $error = "";
        $id_transaction = ($this->uri->segment(3) ? $this->uri->segment(3) : "NULL");
        $id_item = ($this->uri->segment(4) ? $this->uri->segment(4) : "NULL");
        $total_item = ($this->uri->segment(5) ? $this->uri->segment(5) : "NULL");

        if (isset($_GET['apikey'])) {
//            $message .= "API KEY exist. ";
            $apikey = $_GET['apikey'];
            $status_apikey = $this->cart_model->validate_apikey($apikey);
            if ($status_apikey) {
//                $message .= "Known API. ";
                if (($id_item != "NULL") && ($id_transaction != "NULL") && ($total_item != "NULL")) {
                    $status_transcation = $this->cart_model->validate_new_transaction($id_transaction);
                    if ($status_transcation) {
                        $status_item = $this->cart_model->validate_item($id_item);
                        if ($status_item) {
//                    $message .= "Known ITEM. ";
                            $status_total = is_numeric($total_item);
                            if ($status_total) {
//                        $message .= "Total item is integer. ";
                                $result = $this->transaction->processAddItem($apikey, $id_transaction, $id_item, $total_item);
                                $data["process_add_item"] = $result;
                            } else {
                                $data["error"] = "Wrong data type of total item. ";
                            }
                        } else {
                            $data["error"] = "Unknown ITEM. ";
                        }
                    } else {
                        $data["error"] = "This transaction already paid. Try another transaction ID!";
                    }
                } else {
                    $data["error"] = "Missing paramater. ";
                }
            } else {
                $data["error"] = "Unknown API. ";
            }
        } else {
            $data["error"] = "API KEY doesnt exist. Please input your API KEY or register the new one!";
        }
        $result = json_encode($data);
        return $this->output->set_content_type('application/json')->set_output($result);
    }

    /* remove-item
      rest.labanian.com/cart/removeItem/TRANS1/BARANG1?apikey=1234XXX
     * http://localhost/cart-api/cart/removeItem/AXBD123/2?apikey=DKpsPzAYK5utMJcaIwlMWTYx|1659864696117076
     */

    function removeItem() {
        $data = array();
        $result = array();
        $message = "";
        $error = "";
        $id_transaction = ($this->uri->segment(3) ? $this->uri->segment(3) : "NULL");
        $id_item = ($this->uri->segment(4) ? $this->uri->segment(4) : "NULL");

        if (isset($_GET['apikey'])) {
//            $message .= "API KEY exist. ";
            $apikey = $_GET['apikey'];
            $status_apikey = $this->cart_model->validate_apikey($apikey);
            if ($status_apikey) {
//                $message .= "Known API. ";
                if (($id_item != "NULL") && ($id_transaction != "NULL")) {
                    $status_transcation = $this->cart_model->validate_new_transaction($id_transaction);
                    if ($status_transcation) {
                        $status_item = $this->cart_model->validate_item($id_item);
                        if ($status_item) {
//                    $message .= "Known ITEM. ";
                            $result = $this->transaction->processRemoveItem($apikey, $id_transaction, $id_item);
                            $data["process_remove_item"] = $result;
                        } else {
                            $data["error"] = "Unknown ITEM. ";
                        }
                    } else {
                        $data["error"] = "This transaction already paid. Try another transaction ID!";
                    }
                } else {
                    $data["error"] = "Missing paramater. ";
                }
            } else {
                $data["error"] = "Unknown API. ";
            }
        } else {
            $data["error"] = "API KEY doesnt exist. Please input your API KEY or register the new one!";
        }
        $result = json_encode($data);
        return $this->output->set_content_type('application/json')->set_output($result);
    }

    /* checkout
      rest.labanian.com/cart/checkout/TRANS1/COUPON1?apikey=1234XXX
     * http://localhost/cart-api/cart/checkout/AXBD123/PROMOX1?apikey=DKpsPzAYK5utMJcaIwlMWTYx|1659864696117076
     */

    function checkout() {
        $data = array();
        $result = array();
        $message = "";
        $error = "";
        $id_transaction = ($this->uri->segment(3) ? $this->uri->segment(3) : "NULL");
        $coupon = ($this->uri->segment(4) ? $this->uri->segment(4) : "NULL");

        if (isset($_GET['apikey'])) {
//            $message .= "API KEY exist. ";
            $apikey = $_GET['apikey'];
            $status_apikey = $this->cart_model->validate_apikey($apikey);
            if ($status_apikey) {
//                $message .= "Known API. ";
                if ($id_transaction != "NULL") {
                    $status_transcation = $this->cart_model->validate_new_transaction($id_transaction);
                    if ($status_transcation) {
                        $result = $this->transaction->processCheckout($apikey, $id_transaction, $coupon);
                        $data["process_checkout"] = $result;
                    } else {
                        $data["error"] = "This transaction already paid. Try another transaction ID!";
                    }
                } else {
                    $data["error"] = "Missing paramater. ";
                }
            } else {
                $data["error"] = "Unknown API. ";
            }
        } else {
            $data["error"] = "API KEY doesnt exist. Please input your API KEY or register the new one!";
        }
        $result = json_encode($data);
        return $this->output->set_content_type('application/json')->set_output($result);
    }

}

?>