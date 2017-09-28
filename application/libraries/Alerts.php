<?php
/**
* Alert Class
*
* @package		CodeIgniter
* @subpackage	Libraries
* @category	Alerts / Notification
* @author		Twitter: @gficher / Facebook: /gficherFDA
* @link		https://gficher.com
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class Alerts {
    private $CI;
    private $data;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('session');

        $this->data = Array();

        if ($this->CI->session->flashdata('alerts')) {
            $this->data = $this->CI->session->flashdata('alerts');
        }
    }

    public function add($msg, $type = 'info') {
        $this->data[] = Array(
            'type' => $type,
            'message' => $msg,
        );
    }

    public function getArray() {
        return $this->data;
    }

    public function setArray($arr = null) {
        if (($arr) and (is_array($arr))) {
            $this->data = $arr;
        } else {
            return false;
        }
    }

    public function getHTML($type = null, $style = 'alert', $permanent = false) {
        $result = '';

        foreach ($this->data as $value) {
            if ((!$type) or ($value['type'] == $type) or ((is_array($type)) and (in_array($value['type'], $type)))) {
                if ($style == 'alert') {
                    if ($permanent) {
                        $result .= "<div class=\"alert alert-{$value['type']}\" role=\"alert\">{$value['message']}</div>";
                    } else {
                        $result .= "<div class=\"alert alert-{$value['type']} alert-dismissible\" role=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>{$value['message']}</div>";
                    }
                } else if ($style == 'login') {
                    $result .= "<p class=\"text-{$value['type']} text-center\" role=\"alert\">{$value['message']}</p>";
                }
            }
        }

        return $result;
    }

    public function clear($type = null) {
        $result = Array();

        if ($type) {
            foreach ($this->data as $value) {
                if ($value['type'] != $type) {
                    $result[] = Array(
                        'type' => $value['type'],
                        'message' => $value['message'],
                    );
                }
            }
        }

        $this->data = $result;
    }

    public function saveToFlash() {
        $this->CI->session->set_flashdata('alerts', $this->getArray());
    }
}
