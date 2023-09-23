<?php
	require_once("../../../wp-load.php");
	include('MPDF57/mpdf.php');
	$mpdf=new mPDF('utf-8', 'A4');
	$fname = 'BCLOG-Referee-Evaluation';
	$xuid = get_current_user_id();
	$fid = $_GET["fid"];
	if(is_numeric($fid)){$rev = BESMain::getReview($fid);}
	if(($xuid>0 && $rev && current_user_can('print_review') && ((current_user_can('view_review') && $xuid==$rev->evaluator) || (current_user_can('view_others_review')))) || ($rev && $_GET["hard"]=='email')){
		$html = '';
		$html .= '<html><body><table width="100%">';
		$html .= '<tr><th align="left"><img src="'.BIT_URL.'/images/formlogo.jpg" width="120px" style="display:inline-block;"></th><th align="right"><h1 style="display:inline-block;">Referee Electronic Evaluation Form</h1><th></tr>';
		$html .= '</table><hr><table width="100%"><tr><td colspan="2" style="background:#004080;padding:3px;font-weight:bold;color:#ffffff;">Game & Official Details</td></tr>';
		$html .= '<tr><td align="left">';
		$html .= '<p><b>Official\'s Name :</b> '.BESMain::getRefereeName($rev->referee).'</p>';
		$html .= '<p><b>Division/League :</b> '.$rev->divileag.'</p>';
		$html .= '<p><b>Home Team :</b> '.$rev->hometeam.'</p>';
		$html .= '<p><b>Game Date :</b> '.$rev->gdate.'</p>';
		$html .= '<p><b>Game Type :</b> '.$rev->gtype.'</p>';
		$html .= '<p><b>Home Team Score :</b> '.$rev->htscore.'</p>';
		$html .= '</td><td align="left">';
		$html .= '<p><b>Partner\'s Name :</b> '.BESMain::getRefereeName($rev->partner).'</p>';
		$html .= '<p><b>Location/Arena :</b> '.$rev->location.'</p>';
		$html .= '<p><b>Away Team :</b> '.$rev->awayteam.'</p>';
		$html .= '<p><b>Game Starting Time :</b> '.$rev->gtime.'</p>';
		$html .= '<p><b>Game Number :</b> '.$rev->gnumber.'</p>';
		$html .= '<p><b>Away Team Score :</b> '.$rev->atscore.'</p>';
		$html .= '</td></tr>';
		$html .= '</table><table width="100%">';
		$rows = unserialize($rev->reviews);
		$coms = unserialize($rev->comments);
		$i=0; $j=1;
		if($rows){foreach($rows as $k=>$v){
			$group = BESMain::getQuestionsGroup($k);
			$question = BESMain::getQuestionText($k);
			$qno = BESMain::getQuestionNo($k);
			if($i!=$group){$html .= '<tr><td style="background:#004080;padding:3px;font-weight:bold;color:#ffffff;"><p>'.BESMain::getQuestionGroup($group).' Evaluation</p></td></tr>'; $i=$group;}
			$html .= '<tr><td>';
			$html .= '<p><b>'.$qno.'. '.$question.' :</b> '.$v.'</p>';
			$html .= '<div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Comment: '.$coms[$k].'</div>';
			$html .= '</td></tr>';
			$j++;
		}}
		$html .= '<tr><td style="background:#004080;padding:3px;font-weight:bold;color:#ffffff;"><p>Additional Comments</p></td></tr>';
		$html .= '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$rev->comment.'</td></tr>';
		$html .= '</table>';
		$evu = get_userdata($rev->evaluator);
		$html .= '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
		$html .= '<tr><td style="background:#004080;padding:3px;font-weight:bold;color:#ffffff;"><p><b>Evaluators\'s Name :</b> '.$evu->first_name.' '.$evu->last_name.'</p></td><td style="background:#004080;padding:3px;font-weight:bold;color:#ffffff;"><p><b>Evaluators\'s Contact :</b> '.$evu->phone.'</p></td></tr>';
		$html .= '</table>';
		$html .= '';
		$html .= '';
		$html .= '';
		$html .= '';
		$html .= '<p><i>This form shall be distributed via email and is considered confidential. This form and its contents are property of the BC Lacrosse Officials Group.</i></p>';
		$html .= '<p align="center" style="font-size:10px;">';
		$html .= '<input type="checkbox"'.(($rev->verify)? ' checked="Checked"':'').'> ';
		$html .= 'I verify that I have spoken with this referee in person.';
		$html .= '</p></body></html>';
		$html .= '';
	$mpdf->WriteHTML($html);
	$mpdf->Output($fname.'.pdf', 'I');
	}
	exit;
?>
