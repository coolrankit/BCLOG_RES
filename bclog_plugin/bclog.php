<?php
/**
 * @package BCLOG_ES
 * @version 1.0
 */
/*
Plugin Name: BCLOG Evaluation System
Plugin URI: http://www.regulusreign.com/
Description: This custom made plugin handle the BCLOG referee evaluation system, adds needful roles, capabilities and permissions, adds functions to setup the background data and frontend rating and evaluation functions.
Author: Pallab Sardar (RegulusReign Technologies Pvt. Ltd.)
Author URI: http://www.regulusreign.com/
Version: 1.0
*/
?>
<?php
		use PHPMailer\PHPMailer\PHPMailer;
		use PHPMailer\PHPMailer\Exception;
if (!session_id()) {
    session_start();
}
if(!defined("BES_DIR")){define("BES_DIR", plugin_dir_path( __FILE__ ));}
if(!defined("BES_URL")){define("BES_URL", plugins_url('',__FILE__));}

register_activation_hook(__FILE__, 'bes_activate');
function bes_createTable($theTable, $sql){
	global $wpdb;
	if($wpdb->get_var("show tables like '". $theTable . "'") != $theTable) {
		$wpdb->query($sql);
	}
	if (!get_option('besPages')) {addPages();}
}
function bes_activate(){
	global $wpdb;

	$table1 = $wpdb->prefix.'bes_questions';
	$sql1 = "CREATE TABLE `".$table1."` (
	`ID` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`question` TEXT NOT NULL ,
	`group` INT NOT NULL ,
	`answers` TEXT NOT NULL ,
	`qno` INT NOT NULL ,
	`qno2` TEXT NOT NULL ,
	`status` INT NOT NULL
	) ENGINE = MYISAM ;";
	bes_createTable($table1, $sql1);

	$table2 = $wpdb->prefix.'bes_referees';
	$sql2 = "CREATE TABLE `".$table2."` (
	`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`firstname` TEXT NOT NULL ,
	`lastname` TEXT NOT NULL ,
	`email` TEXT NOT NULL ,
	`certification` TEXT NOT NULL ,
	`sport` TEXT NOT NULL ,
	`status` INT NOT NULL
	) ENGINE = MYISAM ;";
	bes_createTable($table2, $sql2);

	$table3 = $wpdb->prefix.'bes_reviews';
	$sql3 = "CREATE TABLE `".$table3."` (
	`ID` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`referee` BIGINT NOT NULL ,
	`partner` BIGINT NOT NULL ,
	`divileag` TEXT NOT NULL ,
	`location` TEXT NOT NULL ,
	`hometeam` TEXT NOT NULL ,
	`awayteam` TEXT NOT NULL ,
	`gdate` DATE NOT NULL ,
	`gtime` TIME NOT NULL ,
	`gtype` TEXT NOT NULL ,
	`gnumber` TEXT NOT NULL ,
	`htscore` INT NOT NULL ,
	`atscore` INT NOT NULL ,
	`reviews` TEXT NOT NULL ,
	`comments` LONGTEXT NOT NULL ,
	`comment` TEXT NOT NULL ,
	`verify` INT NOT NULL ,
	`evaluator` BIGINT NOT NULL ,
	`dated` DATETIME NOT NULL
	) ENGINE = MYISAM ;";
	bes_createTable($table3, $sql3);
	
	bes_roles();
}
function bes_roles(){
	$caps = array(
		'read' => true,
		'edit_profile' => true,
		'add_review' => true,
		'view_review' => true,
		'view_others_review' => true,
		'edit_review' => true,
		'edit_others_review' => true,
		'print_review' => true,
		'print_blank' => true,
		'edit_evaluator' => true
	);
	$admin = get_role('administrator');
	foreach ($caps as $k=>$v){
		if($v){$admin->add_cap($k);}
	}
	
	$c0 = array(
		'read' => true,
		'edit_profile' => true,
		'add_review' => true,
		'view_review' => true,
		'print_review' => true,
		'print_blank' => true,
	);
	add_role('evaluator', 'Evaluator', $c0);
	
	$c1 = array(
		'read' => true,
		'edit_profile' => true,
		'add_review' => true,
		'view_review' => true,
		'view_others_review' => true,
		'print_review' => true,
		'print_blank' => true,
	);
	add_role('commissioner', 'Commissioner', $c1);
	
	add_role('executive', 'Executive', $caps);
	
}
function addPages() {
	$my_posta = array('post_title' => __('Login', 'RegulusReign'), 'post_content' => '[Login]', 'post_type' => 'page', 'post_status' => 'publish',
		'comment_status' => 'closed', 'post_author' => 1,);
	$my_postb = array('post_title' => __('Account', 'RegulusReign'), 'post_content' => '[Account]', 'post_type' => 'page', 'post_status' => 'publish',
		'comment_status' => 'closed', 'post_author' => 1,);
	$my_postc = array('post_title' => __('Evaluations', 'RegulusReign'), 'post_content' => '[MyEvaluations]', 'post_type' => 'page', 'post_status' => 'publish',
		'comment_status' => 'closed', 'post_author' => 1,);
	$my_postd = array('post_title' => __('Referee Evaluation Form', 'RegulusReign'), 'post_content' => '[ReviewForm]', 'post_type' => 'page', 'post_status' => 'publish',
		'comment_status' => 'closed', 'post_author' => 1,);
	$my_poste = array('post_title' => __('Edit Referee Evaluation', 'RegulusReign'), 'post_content' => '[EditReviewForm]', 'post_type' => 'page', 'post_status' => 'publish',
		'comment_status' => 'closed', 'post_author' => 1,);
	$my_postf = array('post_title' => __('Referee Evaluation', 'RegulusReign'), 'post_content' => '[ViewForm]', 'post_type' => 'page', 'post_status' => 'publish',
		'comment_status' => 'closed', 'post_author' => 1,);
	$pid = wp_insert_post($my_posta, false); if ($pid > 0) {$opt['login'] = $pid;}
	$pid = wp_insert_post($my_postb, false); if ($pid > 0) {$opt['account'] = $pid;}
	$pid = wp_insert_post($my_postc, false); if ($pid > 0) {$opt['meval'] = $pid;}
	$pid = wp_insert_post($my_postd, false); if ($pid > 0) {$opt['addreview'] = $pid;}
	$pid = wp_insert_post($my_poste, false); if ($pid > 0) {$opt['editreview'] = $pid;}
	$pid = wp_insert_post($my_postf, false); if ($pid > 0) {$opt['viewreview'] = $pid;}
	update_option('besPages', $opt);
}
if(is_admin()){
	include(BES_DIR.'/admin.php');
}
	include(BES_DIR.'/pages.php');

class BESMain {
	function getGameTypes(){
		$types = array('Playoffs', 'Exhibition', 'Tournament', 'Provincials', 'Championships');
		return $types;
	}
	function getRefereeName($id=0){
		$ref = self::getReferee($id);
		if($ref){
			return trim($ref->firstname).' '.trim($ref->lastname);
	} else {return false;}
	}
	function getQuestionGroups(){
		$groups = array(
			1 => 'Personal Characteristics',
			2 => 'Technical Characteristics',
			3 => 'Game Management'
		);
		return $groups;
	}
	function getQuestionGroup($i=0){
		$groups = self::getQuestionGroups();
		return $groups[$i];
	}
	function getQuestionsGroup($id=0){
		global $wpdb; $table = $wpdb->prefix . 'bes_questions';
		return $wpdb->get_var("SELECT `group` FROM `$table` WHERE `ID`=$id");
	}
	function getQuestionText($id=0){
		global $wpdb; $table = $wpdb->prefix . 'bes_questions';
		return $wpdb->get_var("SELECT `question` FROM `$table` WHERE `ID`=$id");
	}
	function getQuestionNo($id=0){
		global $wpdb; $table = $wpdb->prefix . 'bes_questions';
		$q = $wpdb->get_row("SELECT * FROM `$table` WHERE `ID`=$id");
		if($q){return $q->qno.(($q->qno2)? "-".$q->qno2:"");}
		else return false;
	}
	function getDivisions(){
		return get_option('bes-divisions');
	}
	function getLeagues(){
		return get_option('bes-leagues');
	}
	function getQuestions($type=OBJECT){
		global $wpdb; $table = $wpdb->prefix . 'bes_questions';
		$rows = $wpdb->get_results("SELECT * FROM `$table` WHERE `status`=1 ORDER BY `qno` ASC, `qno2` ASC", $type);
		return $rows;
	}
	function getReferees($type=OBJECT){
		global $wpdb; $table = $wpdb->prefix . 'bes_referees';
		$rows = $wpdb->get_results("SELECT * FROM `$table` WHERE `status`=1 ORDER BY `firstname`", $type);
		return $rows;
	}
	function getReferee($id=0, $type=OBJECT){
		global $wpdb; $table = $wpdb->prefix . 'bes_referees';
		$row = $wpdb->get_row("SELECT * FROM `$table` WHERE `ID`=$id", $type);
		return $row;
	}
	function getReviews($ref=0, $uid=0, $off=-1, $type=OBJECT){
		global $wpdb; $table = $wpdb->prefix . 'bes_reviews';
		$whr = " WHERE 1=1";
		if($ref>0){$whr .= " AND `referee`=$ref";}
		if($uid>0){$whr .= " AND `evaluator`=$uid";}
		if($off>=0){$lim = "LIMIT $off,20";} else {$lim="";}
		$rows = $wpdb->get_results("SELECT * FROM `$table` $whr ORDER BY `dated` DESC $lim", $type);
		return $rows;
	}
	function getReviewsCount($ref=0, $uid=0){
		global $wpdb; $table = $wpdb->prefix . 'bes_reviews';
		$whr = " WHERE 1=1";
		if($ref>0){$whr .= " AND `referee`=$ref";}
		if($uid>0){$whr .= " AND `evaluator`=$uid";}
		$var = $wpdb->get_var("SELECT COUNT(*) FROM `$table` $whr ORDER BY `dated` DESC");
		return $var;
	}
	function addReview($data){
		global $wpdb; $table = $wpdb->prefix . 'bes_reviews';
		$data[dated]=current_time('mysql');
		$id = $wpdb->insert($table, $data);
		if($id){return $wpdb->insert_id;} else{return false;}
	}
	function updateReview($data, $id=0){
		global $wpdb; $table = $wpdb->prefix . 'bes_reviews';
		$data[dated]=current_time('mysql');
		$id = $wpdb->update($table, $data, array('ID'=>$id));
		return $id;
	}
	function getReview($id=0, $uid=-1, $type=OBJECT){
		global $wpdb; $table = $wpdb->prefix . 'bes_reviews';
		$whr = " WHERE `ID`=$id";
		if($uid>=0){$whr .= " AND `evaluator`=$uid";}
		$row = $wpdb->get_row("SELECT * FROM `$table` $whr", $type);
		return $row;
	}
}
function processed_url($url='', $data=array()){
	if(!empty($data)){
		if(strstr($url,'?')){$url .= '&';} else{$url .= '?';}
		$url .= implode('&', $data);
	}
	return $url;
}
add_action('init', 'bes_front_init');
function bes_front_init(){
	
}
add_action('init', 'userlogout');
function userlogout() {
	if ($_GET['log'] && $_GET['log'] == 'out') {
		wp_logout();
		$redirect_to = home_url();
		wp_safe_redirect($redirect_to);
		exit();
	}
}
function submissionEmail($fid=0){
	if(!$fid){return;}
	$emails = array();
	$rev = BESMain::getReview($fid);
	$ref = BESMain::getReferee($rev->referee);
	$eva = get_userdata($rev->evaluator);
	if($ref->email){$emails[] = array('email'=>$ref->email, 'name'=>$ref->firstname.' '.$ref->lastname);}
	$emails[] = array('email'=>$eva->user_email, 'name'=>$eva->first_name.' '.$eva->last_name);
	$users = get_users(array('role'=>'evaluator'));
	if($users){foreach($users as $u){
	$emails[] = array('email'=>$u->user_email, 'name'=>$u->first_name.' '.$u->last_name);
	}}

		
		require 'PHPMailer/src/Exception.php';
		require 'PHPMailer/src/PHPMailer.php';
		
		$mail = new PHPMailer;
		$mail->isSendmail();
		$mail->setFrom(get_bloginfo('admin_email'), get_bloginfo('name'));
		$mail->addReplyTo(get_bloginfo('admin_email'), get_bloginfo('name'));
		$mail->Subject = 'New Referee Electronic Evaluation Submitted';
		$mail->msgHTML(file_get_contents(BES_DIR.'/contents.html'));
		$mail->AltBody = 'altbody';
		$url = processed_url(BES_URL.'/formpdf.php', array("fid=".$fid, "hard=email"));
		$mail->addStringAttachment(file_get_contents($url), 'REEF.pdf');
		
		foreach($emails as $e){
			$mail->addAddress($e[email], $e[name]);
			try{
				$mail->send();
			} catch (Exception $e){}
			$mail->clearAddresses();
		}
}
?>