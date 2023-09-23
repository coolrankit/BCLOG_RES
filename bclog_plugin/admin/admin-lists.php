<?php
include(BES_DIR.'/admin/lists/bes-question.php');
include(BES_DIR.'/admin/lists/bes-referee.php');

add_action( 'plugins_loaded', function () {
	AdminList::get_instance();
} );

class AdminList {

	static $instance;
	static $bes_ques_obj;
	static $bes_refr_obj;

	public function __construct() {
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
		add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
	}
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	public static function set_screen( $status, $option, $value ) {
		return $value;
	}
	public function screen_option() {

		$option = 'per_page';
		$args   = array(
			'label'   => 'Number of items per page:',
			'default' => 10,
			'option'  => 'items_per_page'
		);

		add_screen_option( $option, $args );
	}
	public function screen_option1(){$this->screen_option(); $this->bes_ques_obj = new besQuestions_List();}
	public function screen_option2(){$this->screen_option(); $this->bes_refr_obj = new besReferees_List();}

	public function plugin_menu() {
		$hook1 = add_submenu_page('bes-admin', 'Questions', 'Questions', 'administrator', 'bes-quest', array($this, 'bes_admin_question_page'));
		add_action( "load-$hook1", array( $this, 'screen_option1' ) );

		$hook2 = add_submenu_page('bes-admin', 'Referees', 'Referees', 'administrator', 'bes-referee', array($this, 'bes_admin_referee_page'));
		add_action( "load-$hook2", array( $this, 'screen_option2' ) );
	}

	function bes_admin_question_page() {
		?>
		<?php $this->bes_ques_obj->prepare_items();?>
		<style>
		.bes-form label {display:inline-block;width:150px;font-weight:bold;}
		.bes-form .abox {height:4em;width:100%;}
		.bes-form b, .bes-form strong{font-weight:bold;}
	</style>
		<div class="wrap bes-admin">
			<h1 class="wp-heading-inline">Questions for Evaluations</h1>
			<a href="?page=bes-quest&addanq=new" class="page-title-action">Add New</a>
			<hr class="wp-header-end">
			<?php $this->bes_ques_obj->display(); ?>
		</div>
	<?php
		/*echo '<script>jQuery(".chosen").each(function(){jQuery(this).chosen({width:"200px"});jQuery(this).before(\'<input type="hidden" name="\'+jQuery(this).attr("name")+\'">\').change(function(){jQuery(this).prev().val(jQuery(this).val());}).removeAttr("name");jQuery(this).prev().val(jQuery(this).val());}); jQuery(\'.chosen\').live(\'chosen:updated\', function(event){jQuery(this).prev().val(\'\');});</script>';*/
	}
	function bes_admin_referee_page() {
		?>
		<?php $this->bes_refr_obj->prepare_items();?>
		<style>
			.bes-form label {display:inline-block;width:150px;font-weight:bold;}
		</style>
		<div class="wrap bes-admin">
			<h1 class="wp-heading-inline">Referees to Evaluations</h1>
			<a href="?page=bes-referee&addanr=new" class="page-title-action">Add New</a>
			<hr class="wp-header-end">
			<?php $this->bes_refr_obj->display(); ?>
		</div>
	<?php

	}
}
?>