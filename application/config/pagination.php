<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['anchor_class'] = 'follow_link';
$config['num_links'] = 9;
$config['uri_segment'] = 3;
$config['page_query_string'] = TRUE;
$config['query_string_segment'] = 'page';
$config['full_tag_open'] = "<ul class='pagination'>";
$config['full_tag_close'] ="</ul>";
$config['first_link'] = '&laquo; Primeiro';
$config['first_tag_open'] = "<li>";
$config['first_tagl_close'] = "</li>";
$config['last_link'] = 'Último &raquo;';
$config['last_tag_open'] = "<li>";
$config['last_tagl_close'] = "</li>";
$config['next_link'] = '»';
$config['next_tag_open'] = "<li>";
$config['next_tagl_close'] = "</li>";
$config['prev_link'] = '«';
$config['prev_tag_open'] = "<li>";
$config['prev_tagl_close'] = "</li>";
$config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='javascript:void(0)'>";
$config['cur_tag_close'] = "</a></li>";
$config['num_tag_open'] = '<li>';
$config['num_tag_close'] = '</li>';
