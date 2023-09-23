<?php
include(BES_DIR.'/admin/admin-pages.php');

add_action('admin_init', 'bes_admin_init');
function bes_admin_init(){
	getRedirectNotices();
	
	if($_SERVER['REQUEST_METHOD']=="POST" && $_POST['bes-form-action']=="addnewanq"){
		besQuestions_List::addQuestion();
	}
	if($_SERVER['REQUEST_METHOD']=="POST" && $_POST['bes-form-action']=="updateanq" && is_numeric($_REQUEST['editanq']) && $_REQUEST['editanq']>0){
		besQuestions_List::updateQuestion($_REQUEST['editanq']);
	}
	if($_SERVER['REQUEST_METHOD']=="POST" && $_POST['bes-form-action']=="addnewanr"){
		besReferees_List::addReferee();
	}
	if($_SERVER['REQUEST_METHOD']=="POST" && $_POST['bes-form-action']=="updateanr" && is_numeric($_REQUEST['editanr']) && $_REQUEST['editanr']>0){
		besReferees_List::updateReferee($_REQUEST['editanr']);
	}
	if($_GET['page']=="bes-admin"){
		besQuestions_List::process_bulk_action();
	}
	if($_GET['page']=="bes-referee"){
		besReferees_List::process_bulk_action();
	}
	if($_SERVER['REQUEST_METHOD']=="POST" && $_POST['bes-form-action']=="updatebesd" && $_GET['page']=="bes-divleag"){
		$besd = $_POST['besd'];
		$divs = explode(',', $besd[divisions]);
		$divs = array_map("trim", $divs);
		$divs = array_filter($divs);
		update_option('bes-divisions', $divs);

		$ligs = explode(',', $besd[leagues]);
		$ligs = array_map("trim", $ligs);
		$ligs = array_filter($ligs);
		update_option('bes-leagues', $ligs);

		new BESNotice('success', 'Divisions & leagues updated successfully.');
	}
}

add_action( 'show_user_profile', 'bes_user_profile_fields', 10, 1 );
add_action( 'edit_user_profile', 'bes_user_profile_fields', 10, 1 );
function bes_user_profile_fields( $profileuser ) {
?>
	<table class="form-table">
		<tr>
			<th>
				<label for="user_region"><?php esc_html_e( 'Region' ); ?></label>
			</th>
			<td>
				<input type="text" name="user_region" id="user_region" value="<?php echo esc_attr( get_the_author_meta( 'region', $profileuser->ID ) ); ?>" class="regular-text" />
				<br><span class="description"><?php esc_html_e( 'Your region.', 'text-domain' ); ?></span>
			</td>
		</tr>
	</table>
<?php
}
add_action('edit_user_profile_update', 'update_bes_profile_fields');
add_action('personal_options_update', 'update_bes_profile_fields');
function update_bes_profile_fields($user_id) {
	update_user_meta($user_id, 'region', $_POST['user_region']);
}

function getRedirectNotices(){
	$notices = $_SESSION['bes-notices'];
	if(!empty($notices) && is_array($notices)){foreach($notices as $notice){
		new BESNotice($notice[0], $notice[1]);
		unset($_SESSION['bes-notices']);
	}}
}
class BESNotice {
	static $msg = '';
	//static $type = '';
	function __construct($type='', $text='', $redirect=false) {
		$this->msg = $text;
		if($redirect){
			$_SESSION['bes-notices'][] = array($type, $text);
		} else {
			if($type=='error'){add_action('admin_notices', array($this,'errorNotice'));}
			elseif($type=='success'){add_action('admin_notices', array($this,'successNotice'));}
		}
	}
	function errorNotice(){
		?>
		<div class="error notice">
			<p><?php echo $this->msg; ?></p>
		</div>
		<?php
	}
	function successNotice(){
		?>
		<div class="updated notice">
			<p><?php echo $this->msg; ?></p>
		</div>
		<?php
	}
}

?>