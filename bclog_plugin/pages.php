<?php
add_shortcode('ReviewForm', 'review_form_html');
function review_form_html(){
	$uid = get_current_user_id();
?>
<!-- <div>Game Score : <span>--</span>%</div> -->
		<!-- <h1 class="page-title">BCLOG Referee Evaluation Form</h1> -->
		<?php
		if($uid>0 && current_user_can('add_review')){
		$form = true; $pop = false;
		if($_SERVER['REQUEST_METHOD']=="POST" && $_POST['bes-form-action']=="addnewrev"){
			//BESMain::addReview($_POST['anr']);
			
			$anr = $_POST['anr']; $flag=true;
			if(!($anr[referee]>0 && $anr[partner]>0 && $anr[divileag]!='' && $anr[location]!='' && $anr[hometeam]!='' && $anr[awayteam]!='' && $anr[gdate]!='' && $anr[gtime]!='' && isset($anr[verify]))){$flag=false;}
			if(is_array($anr[reviews])){foreach($anr[reviews] as $k=>$v){if($flag){if(!($v!='')){$flag=false;}}}}
			if($flag){
				$rev = serialize($anr[reviews]);
				$com = serialize($anr[comments]);
				$data = $anr;
				unset($data[reviews]); unset($data[comments]);
				$data[reviews] = $rev; $data[comments] = $com;
				if(!isset($data[verify])){$data[verify]=0;}
				$id = BESMain::addReview($data);
				if($id){submissionEmail($id); echo 'Reviews submitted successfully.'; $pop=true;}
				else{echo 'Reviews couldn\'t be submitted, olease try again later.';}
				$form = false;
			} else{echo 'Please fill all the fields correctly.'; $form = true; $pop=true; $pop2=true;} 
			
		}
		?>
		<form method="post">		
		<?php $refs =  BESMain::getReferees(); $html = ''; if($refs){foreach($refs as $r){$html .= '<option value="'.$r->ID.'">'.$r->firstname.' '.$r->lastname.'</option>';}}?>
		<div class="tc-50 fleft padded">
		<p>Official's Name : <select class="select2-single" name="anr[referee]"><option value="0">Select</option><?php echo (($html)? $html:'');?></select></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Partner's Name : <select class="select2-single" name="anr[partner]"><option value="0">Select</option><?php echo (($html)? $html:'');?></select></p>
		</div>
		<?php 
		$divs = BESMain::getDivisions(); $html = ''; if($divs && count($divs)>0){$html .= '<optgroup label="Divisions">'; foreach($divs as $d){$html .= '<option value="'.$d.'"'.((($pop || $pop2) && $d==$anr[divileag])? ' selected':'').'>'.$d.'</option>';} $html .= '</optgroup>';}
		$legs = BESMain::getLeagues(); if($legs && count($legs)>0){$html .= '<optgroup label="Leagues">'; foreach($legs as $l){$html .= '<option value="'.$l.'"'.((($pop || $pop2) && $l==$anr[divileag])? ' selected':'').'>'.$l.'</option>';} $html .= '</optgroup>';}
		?>
		<div class="tc-50 fleft padded">
		<p>Division/League : <select class="select2-single" name="anr[divileag]"><?php echo $html;?></select></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Location/Arena : <input type="text" name="anr[location]" <?php if($pop || $pop2){echo 'value="'.$anr[location].'"';}?>></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Home Team : <input type="text" name="anr[hometeam]" <?php if($pop || $pop2){echo 'value="'.$anr[hometeam].'"';}?>></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Visiting Team : <input type="text" name="anr[awayteam]" <?php if($pop || $pop2){echo 'value="'.$anr[awayteam].'"';}?>></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Date : <input class="dtpicker" type="text" name="anr[gdate]" <?php if($pop || $pop2){echo 'value="'.$anr[gdate].'"';}?>></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Start Time of Game : <input type="time" name="anr[gtime]" placeholder="00:00" <?php if($pop || $pop2){echo 'value="'.$anr[gtime].'"';}?>></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Game Type : <select type="text" name="anr[gtype]" style="padding:4px;"><?php foreach(BESMain::getGameTypes() as $v){echo '<option'.((($pop || $pop2) && $v==$anr[gtype])? ' selected':'').'>'.$v.'</option>';} ?></select></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Game Number : <input type="text" name="anr[gnumber]" <?php if($pop || $pop2){echo 'value="'.$anr[gnumber].'"';}?>></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Home Team Score : <input type="number" name="anr[htscore]" <?php if($pop || $pop2){echo 'value="'.$anr[htscore].'"';}?>></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Away Team Score : <input type="number" name="anr[atscore]" <?php if($pop || $pop2){echo 'value="'.$anr[atscore].'"';}?>></p>
		</div>
		<!-- <div class="tc-50 fleft padded">
		</div> -->
		<div class="clearfix"></div>
		<hr>
		<div class="padded">
		<?php
			$rows = BESMain::getQuestions();
			$i=0; $j=1;
			if($rows){foreach($rows as $q){
				if($i!=$q->group){echo '<h3>'.BESMain::getQuestionGroup($q->group).'</h3>'; $i=$q->group;}
				echo '<div class="question-wrap">';
				echo '<p>'.$q->qno.(($q->qno2)? "-".$q->qno2:"").'. '.$q->question.'</p>';
				//if($q->periodic){echo '<p>Score : 1st <input type="number" step="1" min="1" max="'.$q->mark.'"> 2nd <input type="number" step="1" min="1" max="'.$q->mark.'"> 3rd <input type="number" step="1" min="1" max="'.$q->mark.'"> [Benchmark: '.$q->benchmark.'/'.$q->mark.']</p><p><input type="text" placeholder="Comments"></p>';}
				$qid = $q->ID;
				echo '<p><select name="anr[reviews]['.$q->ID.']">';
				echo '<option>See Notes Below</option>';
				foreach(unserialize($q->answers) as $a){echo '<option '.(($pop2 && $a==$anr[reviews][$qid])? ' selected':'').'>'.$a.'</option>';}
				echo '</select></p>';
				echo '<p><textarea placeholder="Your Comment" name="anr[comments]['.$q->ID.']">'.(($pop2)? $anr[comments][$qid]:'').'</textarea></p>';
				echo '</div>';
				$j++;
			}}
		echo '<input type="hidden" name="anr[evaluator]" value="'.$uid.'">';
		?>
		</div>
		<hr>
		<div class="padded">
		<p>Additional Comment : <textarea name="anr[comment]"><?php if($pop2){echo $anr[comment];}?></textarea></p>
		</div>
		<div class="padded">
		<p><i>This form shall be distributed via email and is considered confidential. This form and its contents are property of the BC Lacrosse Officials Group.</i></p>
		<br>
		<p><input type="checkbox" name="anr[verify]" value="1"> * I verify that I have spoken with this referee in person.</p>
		<p align="center"><input type="submit" value="Submit"></p>
		</div>
		<input type="hidden" name="bes-form-action" value="addnewrev">
		
		</form>
<?php
		} elseif($uid>0){echo '<div class="padded"><h3 align="center">You do not have permission to view this.</h3></div>';}
		else {echo '<div class="padded"><h3 align="center">Please login to evaluate.</h3></div>'; login_code(home_url());}
}

add_shortcode('EditReviewForm', 'editreview_form_html');
function editreview_form_html(){
	$uid = get_current_user_id();
	$fid = $_GET["fid"];
	$row = BESMain::getReview($fid);
?>
<!-- <div>Game Score : <span>--</span>%</div> -->
		<?php
		$form = true;
		if($uid>0 && $row && ((current_user_can('edit_review') && $uid==$row->evaluator) || (current_user_can('edit_others_review')))){
		if($_SERVER['REQUEST_METHOD']=="POST" && $_POST['bes-form-action']=="editnewrev"){
			//BESMain::addReview($_POST['anr']);
			
			$anr = $_POST['anr']; $flag=true;
			if(!($anr[referee]>0 && $anr[partner]>0 && $anr[divileag]!='' && $anr[location]!='' && $anr[hometeam]!='' && $anr[awayteam]!='' && $anr[gdate]!='' && $anr[gtime]!='' && isset($anr[verify]))){$flag=false;}
			if(is_array($anr[reviews])){foreach($anr[reviews] as $k=>$v){if($flag){if(!($v!='')){$flag=false;}}}}
			if($flag){
				$rev = serialize($anr[reviews]);
				$com = serialize($anr[comments]);
				$data = $anr;
				unset($data[reviews]); unset($data[comments]);
				$data[reviews] = $rev; $data[comments] = $com;
				if(!isset($data[verify])){$data[verify]=0;}
				$id = BESMain::updateReview($data, $fid);
				echo 'Reviews updated successfully.';
				//else{echo 'Reviews couldn\'t be updated, please try again later.';}
				$form = false;
			} else{echo 'Please fill all the fields correctly.'; $form = true;}
			
		}
		if($form){
		?>
		<form method="post">	
		<?php $refs =  BESMain::getReferees(); $html = $html2 = ''; if($refs){foreach($refs as $r){$html .= '<option value="'.$r->ID.'"'.(($r->ID==$row->referee)? ' selected':'').'>'.$r->firstname.' '.$r->lastname.'</option>'; $html2 .= '<option value="'.$r->ID.'"'.(($r->ID==$row->partner)? ' selected':'').'>'.$r->firstname.' '.$r->lastname.'</option>';}}?>
		<div class="tc-50 fleft padded">
		<p>Official's Name : <select class="select2-single" name="anr[referee]"><option value="0">Select</option><?php echo (($html)? $html:'');?></select></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Partner's Name : <select class="select2-single" name="anr[partner]"><option value="0">Select</option><?php echo (($html2)? $html2:'');?></select></p>
		</div>
		<?php 
		$divs = BESMain::getDivisions(); $html = ''; if($divs && count($divs)>0){$html .= '<optgroup label="Divisions">'; foreach($divs as $d){$html .= '<option value="'.$d.'"'.(($d==$row->divileag)? ' selected':'').'>'.$d.'</option>';} $html .= '</optgroup>';}
		$legs = BESMain::getLeagues(); if($legs && count($legs)>0){$html .= '<optgroup label="Leagues">'; foreach($legs as $l){$html .= '<option value="'.$l.'"'.(($l==$row->divileag)? ' selected':'').'>'.$l.'</option>';} $html .= '</optgroup>';}
		?>
		<div class="tc-50 fleft padded">
		<p>Division/League : <select class="select2-single" name="anr[divileag]"><?php echo $html;?></select></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Location/Arena : <input type="text" name="anr[location]" value="<?php echo $row->location;?>"></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Home Team : <input type="text" name="anr[hometeam]" value="<?php echo $row->hometeam;?>"></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Visiting Team : <input type="text" name="anr[awayteam]" value="<?php echo $row->awayteam;?>"></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Date : <input class="dtpicker" type="text" name="anr[gdate]" value="<?php echo $row->gdate;?>"></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Start Time of Game : <input type="time" name="anr[gtime]" placeholder="00:00" value="<?php echo $row->gtime;?>"></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Game Type : <select type="text" name="anr[gtype]" style="padding:4px;"><?php foreach(BESMain::getGameTypes() as $v){echo '<option'.(($v==$row->gtype)? ' selected':'').'>'.$v.'</option>';} ?></select></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Game Number : <input type="text" name="anr[gnumber]" value="<?php echo $row->gnumber;?>"></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Home Team Score : <input type="number" name="anr[htscore]" value="<?php echo $row->htscore;?>"></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Away Team Score : <input type="number" name="anr[atscore]" value="<?php echo $row->atscore;?>"></p>
		</div>
		<!-- <div class="tc-50 fleft padded">
		</div> -->
		<div class="clearfix"></div>
		<hr>
		<div class="padded">
		<?php
			$rows = BESMain::getQuestions();
			$i=0; $j=1;
			if($rows){foreach($rows as $q){
				if($i!=$q->group){echo '<h3>'.BESMain::getQuestionGroup($q->group).'</h3>'; $i=$q->group;}
				echo '<div class="question-wrap">';
				echo '<p>'.$q->qno.(($q->qno2)? "-".$q->qno2:"").'. '.$q->question.'</p>';
				echo '<p><select name="anr[reviews]['.$q->ID.']">';
				echo '<option>See Notes Below</option>';
				$qid = $q->ID; $revs = unserialize($row->reviews); $coms = unserialize($row->comments);
				foreach(unserialize($q->answers) as $a){echo '<option'.(($a==$revs[$qid])? ' selected':'').'>'.$a.'</option>';}
				echo '</select></p>';
				echo '<p><textarea placeholder="Your Comment" name="anr[comments]['.$q->ID.']">'.$coms[$qid].'</textarea></p>';
				echo '</div>';
				$j++;
			}}
		?>
		</div>
		<hr>
		<div class="padded">
		<p>Additional Comments <textarea name="anr[comment]"><?php echo $row->comment;?></textarea></p>
		</div>
		<div class="padded">
		<p><i>This form shall be distributed via email and is considered confidential. This form and its contents are property of the BC Lacrosse Officials Group.</i></p>
		<br>
		<p><input type="checkbox" name="anr[verify]" value="1" <?php echo (($row->verify)? 'checked':'');?>> * I verify that I have spoken with this referee in person.</p>
		<p align="center"><input type="submit" value="Update"></p>
		</div>
		<input type="hidden" name="bes-form-action" value="editnewrev">
		
		</form>
<?php	}
		} elseif(!($uid>0)) {echo '<div class="padded"><h3 align="center">Please login to evaluate.</h3></div>'; login_code(home_url());}
		elseif(!$row) {echo '<div class="padded"><h4 align="center">Referee evaluation not found.</h4></div>';}
		else{echo '<div class="padded"><h3 align="center">You do not have permission to view this.</h3></div>';}
}
add_shortcode('ViewForm', 'view_form_html');
function view_form_html(){
	$uid = get_current_user_id();
	$fid = $_GET["fid"];
	if(is_numeric($fid)){$rev = BESMain::getReview($fid);}
?>
<!-- <div>Game Score : <span>--</span>%</div> -->
		<!-- <h1 class="page-title">BCLOG Referee Evaluation Form</h1> -->
		<?php
		if($uid>0 && $rev && ((current_user_can('view_review') && $uid==$rev->evaluator) || (current_user_can('view_others_review')))){
		?>
		
		<?php $refs =  BESMain::getReferees(); $html = ''; if($refs){foreach($refs as $r){$html .= '<option value="'.$r->ID.'">'.$r->firstname.' '.$r->lastname.'</option>';}}?>
		<div class="tc-50 fleft padded">
		<p>Official's Name : <span class="boxed"><?php echo BESMain::getRefereeName($rev->referee);?></span></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Partner's Name : <span class="boxed"><?php echo BESMain::getRefereeName($rev->partner);?></span></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Division/League : <span class="boxed"><?php echo $rev->divileag;?></span></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Location/Arena : <span class="boxed"><?php echo $rev->location;?></span></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Home Team : <span class="boxed"><?php echo $rev->hometeam;?></span></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Visiting Team : <span class="boxed"><?php echo $rev->awayteam;?></span></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Date : <span class="boxed"><?php echo $rev->gdate;?></span></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Start Time of Game : <span class="boxed"><?php echo $rev->gtime;?></span></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Game Type : <span class="boxed"><?php echo $rev->gtype;?></span></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Game Number : <span class="boxed"><?php echo $rev->gnumber;?></span></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Home Team Score : <span class="boxed"><?php echo $rev->htscore;?></span></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Away Team Score : <span class="boxed"><?php echo $rev->atscore;?></span></p>
		</div>
		<!-- <div class="tc-50 fleft padded">
		</div> -->
		<div class="clearfix"></div>
		<hr>
		<div class="padded">
		<?php
			$rows = unserialize($rev->reviews);
			$coms = unserialize($rev->comments);
			$i=0; $j=1;
			if($rows){foreach($rows as $k=>$v){
				$group = BESMain::getQuestionsGroup($k);
				$question = BESMain::getQuestionText($k);
				$qno = BESMain::getQuestionNo($k);
				if($i!=$group){echo '<h3>'.BESMain::getQuestionGroup($group).'</h3>'; $i=$group;}
				echo '<div class="question-wrap">';
				echo '<p>'.$qno.'. '.$question.'</p>';
				echo '<p><span class="boxed"><b>Review: </b>'.$v.'</span></p>';
				echo '<div style="padding-left: 20px;"><div class="boxed"><b>Comment: </b>'.$coms[$k].'</div></div>';
				echo '</div>';
				$j++;
			}}
		?>
		</div>
		<hr>
		<div class="padded">
		<div>Additional Comments : <div class="boxed"><?php echo $rev->comment;?></div></div>
		</div>
		<hr>
		<div class="padded">
		<p><i>This form shall be distributed via email and is considered confidential. This form and its contents are property of the BC Lacrosse Officials Group.</i></p>
		<br>
		<p><input type="checkbox" checked disabled> * I verify that I have spoken with this referee in person.</p>
		</div>
		<?php $evu = get_userdata($rev->evaluator);?>
		<div>
		<div class="tc-50 fleft padded">
		<p>Evaluators's Name : <span class="boxed"><?php echo $evu->first_name.' '.$evu->last_name;?>&nbsp;</span></p>
		</div>
		<div class="tc-50 fleft padded">
		<p>Evaluator's Contact : <span class="boxed"><?php echo $evu->user_email;?>&nbsp;</span></p>
		</div>
		</div>
		
<?php
		}  elseif(!($uid>0)) {echo '<div class="padded"><h3 align="center">Please login to view this.</h3></div>'; login_code(home_url());}
		elseif(!$rev) {echo '<div class="padded"><h4 align="center">Referee evaluation not found.</h4></div>';}
		else{echo '<div class="padded"><h3 align="center">You do not have permission to view this.</h3></div>';}
}

add_shortcode('Login', 'login_code');
function login_code($url=false) {
	$rr_user = wp_get_current_user();
	if (!$rr_user->ID > 0) {
	?>
		<form name="loginform" id="loginform1" action="<?php echo site_url('wp-login.php');?>" method="post">
			<div class="padded"><p><label><?php _e('Username', 'RegulusReign');?> :</label>
			<input type="text" name="log" id="user_login" class="tbox" value="<?php echo esc_attr($user_login); ?>" tabindex="10" /></p></div>
			<div class="padded"><p><label><?php _e('Password', 'RegulusReign');?> :</label>
			<input type="password" name="pwd" id="user_pass" class="tbox" value="" tabindex="20" /></p></div>
			<input type="hidden" name="redirect_to" value="<?php echo (($url)? $url:home_url());?>" />
			<div class="padded"><p><input type="submit" name="wp-submit" 
			id="wp-submit" class="butn" value="<?php _e('Log In', 'RegulusReign'); ?>" tabindex="100" /></p></div>
		</form>
	<?php
	} else {echo '<p class="msg">'.__('You are already registered and logged in.', 'RegulusReign').'</p>';}
}

add_shortcode('Account', 'account_code');
function account_code() {
	global $usrs; global $wpdb; $output = '';
	$rr_user = wp_get_current_user();
	if ($rr_user->ID > 0 && current_user_can('edit_profile')) {
	if ($rr_user->ID > 0) {$rr_info = get_userdata($rr_user->ID);}
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['faction'] == "acpf") {
		$user = $_POST['user']; $userm = $_POST['userm'];
		if ($user[first_name] == '') {$user[first_name] = $rr_info->first_name;}
		if ($user[last_name] == '') {$user[last_name] = $rr_info->last_name;}
		if ($_POST['passwrd'] && $_POST['passwrd'] != '') {$arg = array('ID' => $rr_user->ID, 'first_name' => $user[first_name], 'last_name' => $user[last_name], 'user_pass' => $_POST['passwrd']);}
		else {$arg = array('ID' => $rr_user->ID, 'first_name' => $user[first_name], 'last_name' => $user[last_name]);}
		wp_update_user($arg);
		
		update_user_meta($rr_user->ID, 'region', $userm['region']);
		
	}
		$output .= '<div id="ac-form">';
		$output .= '<form name="acpfform" id="acpfform" action="" method="post">
		<div class="padded"><p><label>'.__('Username', 'RegulusReign').' * :</label>
		<input class="tbox" name="user[user_login]" type="text" value="'.$rr_info->user_login.'" disabled></p></div>
		<div class="padded"><p><label>'.__('Password', 'RegulusReign').' :</label>
		<input class="tbox" name="passwrd" type="text"><em>[Leave blank if you don\'t want to change.]</em></p></div>
		<div class="padded"><p><label>'.__('Email', 'RegulusReign').' * :</label>
		<input class="tbox" name="user[user_email]" type="text" value="'.$rr_info->user_email.'" disabled></p></div>
		<div class="padded"><p><label>'.__('First Name', 'RegulusReign').' :</label>
		<input class="tbox" name="user[first_name]" type="text" value="'.$rr_info->first_name.'"></p></div>
		<div class="padded"><p><label>'.__('Last Name', 'RegulusReign').' :</label>
		<input class="tbox" name="user[last_name]" type="text" value="'.$rr_info->last_name.'"></p></div>
		<div class="padded"><p><label>'.__('Region', 'RegulusReign').' :</label>
		<input class="tbox" name="userm[region]" type="text" value="'.$rr_info->region.'"></p></div>
		<input type="hidden" name="faction" value="acpf">
		<div class="padded"><p><input type="submit" value="'.__('Update', 'RegulusReign').'" class="butn"></p></div>
		</form>
		<div class="clear"></div>';
		$output .= '</div>';
		echo $output;
	} elseif($rr_user->ID > 0){echo '<div class="padded"><h3 align="center">You do not have permission to view this.</h3></div>';}
	else {login_code($_SERVER["REQUEST_URI"]);}
}

add_shortcode('MyEvaluations', 'bes_myEvaluations_html');
function bes_myEvaluations_html(){
	$uid = get_current_user_id();
	$html = '';
	
	if($uid>0 && current_user_can('view_review')){
	if(isset($_GET["pgd"]) && is_numeric($_GET["pgd"])){$paged = $_GET["pgd"];} else{$paged = 0;}
	$limit = 20;
	if($paged>1){$noff = ($paged + 1) * $limit; $poff = ($paged - 2) * $limit; $coff = ($paged - 1) * $limit; $ppgd = $paged-1; $npgd = $paged+1;}
	else{$noff = $limit; $coff = 0; $npgd=$paged+1;}
	
	if(current_user_can('view_others_review')){
	$revs = BESMain::getReviews(0, 0, $coff);
	$count = BESMain::getReviewsCount(0, 0);
	} else {
	$revs = BESMain::getReviews(0, $uid, $coff);
	$count = BESMain::getReviewsCount(0, $uid);
	}
	if($count<=$noff){unset($noff);unset($npgd);}
	
	if($revs){
		$html .= '<style>.tc-full{max-width:1200px;min-width:100%;}</style>';
		$html .= '<div class="padded">';
		$html .= '<table class="listtable" width="100%" border="1px" bordercolor="#000000" cellspacing="0"><tr><th>Evaluator</th><th>Division/League</th><th>Location</th><th>Teams</th><th>Timing</th><th>Referee</th><th>Actions</th></tr>';
		foreach($revs as $r){
			$html .= '<tr>';
			$ev = get_userdata($r->evaluator);
			$html .= '<td>'.$ev->first_name.' '.$ev->last_name.'</td>';
			$html .= '<td>'.$r->divileag.'</td>';
			$html .= '<td>'.$r->location.'</td>';
			$html .= '<td>'.$r->hometeam .' vs '.$r->awayteam.'</td>';
			$html .= '<td>'.date("M d, Y - h:ia", strtotime($r->gdate .' '.$r->gtime)).'</td>';
			$html .= '<td>'.BESMain::getRefereeName($r->referee).'</td>';
			//$html .= '<td>'.BESMain::getRefereeName($r->partner).'</td>';
			$bpg = get_option('besPages');
			
			$html .= '<td><a href="'.processed_url(get_permalink($bpg[viewreview]), array("fid=".$r->ID)).'">View</a>'.((current_user_can('edit_others_review') || $uid==$r->evaluator)? ' | <a href="'.processed_url(get_permalink($bpg[editreview]), array("fid=".$r->ID)).'">Edit</a>':'').' | <a target="_blank" href="'.processed_url(BES_URL.'/formpdf.php', array("fid=".$r->ID)).'">Print</a></td>';
			$html .= '</tr>';
		}
		$html .= '</table>';
		$url = get_permalink();
		$html .= '<p align="center" class="padded">';
		$html .= (($ppgd)? '<a href="'.processed_url($url, array('pgd='.$ppgd)).'">Pevious</a>':'');
		$html .= (($ppgd && $npgd)? ' | ':'');
		$html .= (($npgd)? '<a href="'.processed_url($url, array('pgd='.$npgd)).'">Next</a>':'');
		$html .= '</p>';
		$html .= '</div>';
	} else{$html .= '<div class="padded"><h3 align="center">No evaluations found.</h3></div>';}
	return $html;
	} elseif($uid>0){echo '<div class="padded"><h3 align="center">You do not have permission to view this.</h3></div>';}
	else{echo '<div class="padded"><h3 align="center">Please login to view this.</h3></div>'; login_code(home_url());}
}
?>