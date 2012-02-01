<?php
/*
Plugin Name: Reco Widget
Plugin URI: http://www.angrycreative.se/oppen-kallkod/reco-for-wordpress/
Description: Visar en widget från reco.se
Version: 0.2
Author: Angry Creative AB
Author URI: http://angrycreative.se
License: GPLv2
*/

add_action('admin_menu', 'reco_admin_add_page');
function reco_admin_add_page() {
	add_options_page('Inställningar för Reco widget', 'Reco Widget', 'manage_options', 'reco-widget-settings', 'reco_options_page');
}

function reco_options_page() {
	require_once(dirname(__FILE__) .'/templates/reco-options-page.tpl.php');
}

add_action('admin_init', 'reco_admin_init');
function reco_admin_init(){
	register_setting( 'reco_widget_options', 'reco_widget_options', 'reco_widget_options_validate' );
	add_settings_section('reco_widget_main', 'API-Inställningar', 'reco_widget_main_section_text', 'reco_widget_sections');
	add_settings_field('reco_widget_setting_api_key', 'API-Nyckel', 'reco_widget_setting_api_key_string', 'reco_widget_sections', 'reco_widget_main');
	add_settings_field('reco_widget_setting_company_id', 'Företags ID:', 'reco_widget_setting_company_id_string', 'reco_widget_sections', 'reco_widget_main');
	add_settings_field('reco_widget_setting_num_recos', 'Antal recos att visa:', 'reco_widget_setting_num_recos_string', 'reco_widget_sections', 'reco_widget_main');
}

function reco_widget_main_section_text() {
	echo "<p>För närvarande måste du kontakta reco för att få reda på vilken API-Nyckel somt vilket företags id du skall använda.</p>";
}

function reco_widget_setting_api_key_string() {
	$options = get_option('reco_widget_options');
	echo "<input id='reco_widget_setting_api_key' name='reco_widget_options[reco_widget_setting_api_key]' size='40' type='text' value='{$options['reco_widget_setting_api_key']}' />";
}

function reco_widget_setting_company_id_string() {
	$options = get_option('reco_widget_options');
	echo "<input id='reco_widget_setting_company_id' name='reco_widget_options[reco_widget_setting_company_id]' size='40' type='text' value='{$options['reco_widget_setting_company_id']}' />";
}

function reco_widget_setting_num_recos_string() {
	$options = get_option('reco_widget_options');
	if(empty($options['reco_widget_setting_num_recos']))
		$options['reco_widget_setting_num_recos'] = 5;
	echo "<input id='reco_widget_setting_num_recos' name='reco_widget_options[reco_widget_setting_num_recos]' size='5' type='text' value='{$options['reco_widget_setting_num_recos']}' />";
	echo "<br/><small>Om du anger -1 som värde så kommer alla recos visas.</small>";
}

function reco_widget_options_validate($input) {
	return $input;
}

add_action('wp_enqueue_scripts', 'reco_add_scripts');
function reco_add_scripts() {
	$myStyleUrl = plugins_url('css/reco.css', __FILE__);
	$myStyleFile = dirname(__FILE__) .'/css/reco.css';
    if ( file_exists($myStyleFile) ) {
        wp_register_style('recoWidgetStyleSheets', $myStyleUrl);
        wp_enqueue_style( 'recoWidgetStyleSheets');
    }
	wp_enqueue_script( 'jquery' );
    wp_enqueue_script('reco',
      plugins_url('/js/reco.js', __FILE__), // where the this file is in /someplugin/
      NULL,
      '1.0',
  	  false);
}

add_shortcode('reco-widget', 'reco_widget_short_code');
function reco_widget_short_code($atts) {
	require(dirname(__FILE__) . '/api.php');
	$options = get_option('reco_widget_options');
	$r = new reco($options['reco_widget_setting_api_key'], $options['reco_widget_setting_company_id']);
	
	if($options['reco_widget_setting_num_recos'] == '-1') {
		$numRecos = 999;
	} else {
		$numRecos = $options['reco_widget_setting_num_recos'];
	}
	$itemList = $r->getReviews($numRecos);
	$data = $itemList->reviews;

	$buf = '';
	$buf .= '<div id="reco_reviews">';
	$buf .= '<div id="reco_logo">';
	$buf .= '<a href="http://reco.se/"><img src="'. plugins_url('/images/reco-logo.png', __FILE__) .'" /></a>';
	$buf .= '</div>';
	$buf .=  '<ul id="reco_reviews_list">';
	if(is_array($data)) {
		foreach(array_keys($data) as $i) {
			$buf .= '<li id="reco_review_item_'; 
			$buf .= $i+1; 
			$buf .= '" class="reco_review_item">';
            $buf .= '   <div class="reco_profile_badge">';
            $buf .= '        <a target="_blank" href="http://www.reco.se/friends/profile.seam?friendId='. $data[$i]->reviewer->id .'">';
            $buf .= '           <img src="'. $data[$i]->reviewer->thumbPicture .'" alt="'. $data[$i]->reviewer->screenName .'" width="70" height="70" class="reco_profile_pic" />';
            $buf .= '       </a>';
            $buf .= '   </div>';

			
            $buf .= '    <div class="reco_review_text_box">';
            $buf .= '       <span class="reco_user_name">';
            $buf .= '            <a target="_blank" href="http://www.reco.se/friends/profile.seam?friendId='. $data[$i]->reviewer->id .'">'. $data[$i]->reviewer->screenName .'</a>';
            $buf .= '        </span> skrev om <span class="reco_company_name"><a href="'. $itemList->restfulIdentifier .'">'. $itemList->name .'</a></span>: <br/>';
            $buf .=  $data[$i]->text;
            $buf .= '    </div>';
			
            $buf .= '<div class="reco_review_source twocol last">';
            $buf .= '   <div class="reco_rating_box_small">';
            $buf .= '       <div class="rating-stars-'. $data[$i]->grade .'">';
            $buf .= '       </div>';
            $buf .= '   </div>';
            $buf .= '    <div class="reco_review_date">';
            $buf .=         $r->decodeDate($data[$i]->created);
            $buf .= '   </div>';
            $buf .= '</div>';
			


			
			$buf .= '</li>';
		}
	}
	
	$buf .= '</ul><div id="toggle_displayed_reviews"><span>Visa fler</span></div></div>';
	$buf .= '<script type="text/javascript">';
	$buf .= ' jQuery(document).ready(function() { recoInit(); });';
	$buf .= '</script>';
	
	return $buf;
}
