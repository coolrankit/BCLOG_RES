<?php
include(BES_DIR.'/admin/admin-lists.php');

add_action('admin_menu', 'bes_admin_menu');
function bes_admin_menu(){
	add_menu_page('BCLOG Options', 'BCLOG Options', 'administrator', 'bes-admin', 'bes_admin_page');
}
function bes_admin_page(){
	?>
	<style>
		.bes-form label {display:inline-block;width:150px;font-weight:bold;}
		/*.bes-form .abox {height:4em;width:100%;}*/
	</style>
	<div class="wrap bes-admin">
		<h1 class="wp-heading-inline">BCLOG Plugin Options</h1>
		<hr class="wp-header-end">
		<?php 
		global $wpdb;
		if($_SERVER['REQUEST_METHOD']=="POST" && $_POST['bes-form-action']=="updateopts"){
			$bpg = $_POST["bpg"];
			update_option('besPages', $bpg);
			echo '<p>Page Options Updated Successfully.</p>';
		}
		if($_SERVER['REQUEST_METHOD']=="POST" && $_POST['importrefs']){
			$fileName = $_FILES["uprefs"]["tmp_name"];
			$table = $wpdb->prefix . 'bes_referees';
			if ($_FILES["uprefs"]["size"] > 0) {
				$file = fopen($fileName, "r");
				$i = 0;
				while (($cols = fgetcsv($file, 10000, ",")) !== FALSE) {
					if($i>0){
						$data = array('firstname'=>$cols[0], 'lastname'=>$cols[1], 'email'=>$cols[2], 'certification'=>$cols[3], 'sport'=>$cols[4], 'status'=>1);
						$wpdb->insert($table, $data);
					}
					$i++;
				}
			echo '<p>Referees added successfully.</p>';
				
			}
		}
		if($_SERVER['REQUEST_METHOD']=="POST" && $_POST['importevas']){
			$fileName = $_FILES["upevas"]["tmp_name"];
			if ($_FILES["upevas"]["size"] > 0) {
				$file = fopen($fileName, "r");
				$i = 0;
				while (($cols = fgetcsv($file, 10000, ",")) !== FALSE) {
					if($i>0){
						if($cols[4]==1){$role = 'evaluator';}
						if($cols[4]==2){$role = 'commissioner';}
						if($cols[4]==3){$role = 'executive';}
						$random_password = wp_generate_password(12, false);
						$data = array('user_login'=>$cols[3], 'user_pass'=>$random_password, 'first_name'=>$cols[1], 'last_name'=>$cols[0], 'user_email'=>$cols[3], 'role'=>$role);//'region'=>$cols[2], 
						$user_id = wp_insert_user($data);
						$wpdb->insert($table, $data);
						if ( ! is_wp_error( $user_id ) ) {
							wp_new_user_notification($user_id, null, 'both');
							update_user_meta($user_id, 'region', $cols[2]);
						}
					}
					$i++;
				}
			echo '<p>Evaluators added successfully.</p>';
				
			}
		}
		$bpg = get_option('besPages');
		$pages = get_pages($args);
		$pg1 = '';
		if($pages){
			foreach($pages as $p){
				$pg1 .= '<option value="'.$p->ID.'"'.(($p->ID==$bpg[login])? ' selected':'').'>'.$p->post_title.'</option>';
				$pg2 .= '<option value="'.$p->ID.'"'.(($p->ID==$bpg[account])? ' selected':'').'>'.$p->post_title.'</option>';
				$pg3 .= '<option value="'.$p->ID.'"'.(($p->ID==$bpg[meval])? ' selected':'').'>'.$p->post_title.'</option>';
				$pg4 .= '<option value="'.$p->ID.'"'.(($p->ID==$bpg[addreview])? ' selected':'').'>'.$p->post_title.'</option>';
				$pg5 .= '<option value="'.$p->ID.'"'.(($p->ID==$bpg[editreview])? ' selected':'').'>'.$p->post_title.'</option>';
				$pg6 .= '<option value="'.$p->ID.'"'.(($p->ID==$bpg[viewreview])? ' selected':'').'>'.$p->post_title.'</option>';
			}
		}
		echo '<form method="post">';
		echo '<div class="bes-form">';
		echo '<h3>Pages:</h3>';
		echo '<p>Login Page : <select name="bpg[login]">'.$pg1.'</select></P>';
		echo '<p>Account Page : <select name="bpg[account]">'.$pg2.'</select></P>';
		echo '<p>My Evaluations Page : <select name="bpg[meval]">'.$pg3.'</select></P>';
		echo '<p>Add Review Page : <select name="bpg[addreview]">'.$pg4.'</select></P>';
		echo '<p>Edit Review Page : <select name="bpg[editreview]">'.$pg5.'</select></P>';
		echo '<p>View Review Page : <select name="bpg[viewreview]">'.$pg6.'</select></P>';
		echo '<input type="submit" class="bbox button" value="Save">';
		echo '<input type="hidden" name="bes-form-action" value="updateopts">';
		echo '</div>';
		echo '</form>';
		echo '<hr>';
		?>
	</div>
	<form method="post" enctype="multipart/form-data">
	<p><b>Upload Referees in Bulk :</b> <input type="file" name="uprefs"> <input type="submit" name="importrefs" value="Upload"> [Max limit 9999]</p>
	</form>
	<form method="post" enctype="multipart/form-data">
	<p><b>Upload Evaluators in Bulk :</b> <input type="file" name="upevas"> <input type="submit" name="importevas" value="Upload"> [Max limit 9999]</p>
	</form>
	<?php
}
add_action('admin_menu', 'bes_admin_menu2', 12);
function bes_admin_menu2(){
	add_submenu_page('bes-admin', 'Divisions/Leagues', 'Divisions/Leagues', 'administrator', 'bes-divleag', 'bes_admin_divleag_page');
}
function bes_admin_divleag_page(){
	?>
	<style>
		.bes-form label {display:inline-block;width:150px;font-weight:bold;}
		.bes-form .abox {height:4em;width:100%;}
	</style>
	<div class="wrap bes-admin">
		<h1 class="wp-heading-inline">Divisions & Leagues</h1>
		<hr class="wp-header-end">
		<?php 
		@$divs = implode(', ', BESMain::getDivisions());
		@$ligs = implode(', ', BESMain::getLeagues());
		echo '<form method="post">';
		echo '<div class="bes-form">';
		echo '<p><b>Divisions : </b>[<em>Enter division names seperated by comma (,).</em>]</P>';
		echo '<p><textarea name="besd[divisions]" class="abox" placeholder="Enter division names seperated by comma (,).">'.$divs.'</textarea></p>';
		echo '<p><b>Leagues : </b>[<em>Enter league names seperated by comma (,).</em>]</P>';
		echo '<p><textarea name="besd[leagues]" class="abox" placeholder="Enter league names seperated by comma (,).">'.$ligs.'</textarea></p>';
		echo '<input type="hidden" name="bes-form-action" value="updatebesd">';
		echo '<input type="submit" class="bbox button" value="Save">';
		echo '</div>';
		echo '</form>';
		?>
	</div>
	<?php
}
?>