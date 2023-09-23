<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class besQuestions_List extends WP_List_Table {

	private static $table = 'bes_questions';
	private static $base_request = 'admin.php?page=bes-quest';
	private static $plural = 'questions';
	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'Question', 'RegulusReign' ),
			'plural'   => __( 'Questions', 'RegulusReign' ),
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			'ajax'     => true
		) );
	}

	function get_columns() {
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'serial' => __( 'No.', 'RegulusReign' ),
			'question' => __( 'Question', 'RegulusReign' ),
			'group' => __( 'Category', 'RegulusReign' ),
			//'marks' => __( 'Benchmarks/Marks', 'RegulusReign' ),
			'status' => __( 'Status', 'RegulusReign' ),
		);
		return $columns;
	}
	public function get_sortable_columns() {
		$sortable_columns = array(
			'group' => array( 'group', false ),
			'question' => array( 'question', false ),
			'status' => array( 'status', false ),
		);
		return $sortable_columns;
	}
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'serial':
				return $item['qno'].'-'.$item['qno2'];
			case 'status':
				return (($item['status'])? 'Active':'Inactive');
			case 'group':
				return BESMain::getQuestionGroup($item[$column_name]);
			case 'question':
				$html = $item[$column_name];
				$eurl = admin_url('admin.php?page=bes-quest&editanq='.$item['ID']);
				$html .= '<div class="row-actions"><span class="edit"><a href="'.$eurl.'">Edit</a></span></div>';
				return $html;
			default:
				return (($item[$column_name])? $item[$column_name]:'');
		}
	}
	function column_cb( $item ) {
		return sprintf('<input type="checkbox" name="bulk-ids[]" value="%s" />', $item['ID']);
	}

	public static function get_results( $per_page = 10, $page_number = 1 ) {

		global $wpdb; $table = $wpdb->prefix . self::$table;
		$uid = get_current_user_id();

		$sql = "SELECT * FROM `$table` WHERE 1=1";
		
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$orderby = $_REQUEST['orderby'];
			$sql .= ' ORDER BY ' . esc_sql( $orderby );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}
	/*
	}*/
	public static function record_count($col='', $val='') {
		global $wpdb; $table = $wpdb->prefix . self::$table;
		$uid = get_current_user_id();

		$sql = "SELECT COUNT(*) FROM `$table` WHERE 1=1";
		
		if($col!='' && $val!=''){
			$sql .= " AND `$col`='$val'";
		}

		return $wpdb->get_var( $sql );
	}
	
	public function get_bulk_actions() {
		$actions = array(
			'bulk-activate' => 'Activate',
			'bulk-deactivate' => 'Deactivate',
			//'bulk-edit' => 'Edit',
			//'bulk-delete' => 'Delete',
		);
		return $actions;
	}
	public function process_bulk_action() {
		$nonce = $_REQUEST['_wpnonce'];
		if ( (isset( $_POST['action'] ) && $_POST['action'] == 'bulk-activate') || (isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-activate') ) {
			if ( isset($_REQUEST['_wpnonce']) && !wp_verify_nonce($nonce, 'bulk-' . ((is_object($this) && $this->_args['plural'])? $this->_args['plural']:self::$plural)) ) {die( 'Go get a life script kiddies' );}
			else {
				$ids = esc_sql( $_POST['bulk-ids'] );
				if(is_array($ids)){foreach ($ids as $id) {
					self::activate_item($id);
				} new BESNotice('success', 'Selected questions activated successfully.');}
			}		
		}
		if ( (isset( $_POST['action'] ) && $_POST['action'] == 'bulk-deactivate') || (isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-deactivate') ) {
			if ( isset($_REQUEST['_wpnonce']) && !wp_verify_nonce($nonce, 'bulk-' . ((is_object($this) && $this->_args['plural'])? $this->_args['plural']:self::$plural)) ) {die( 'Go get a life script kiddies' );}
			else {
				$ids = esc_sql( $_POST['bulk-ids'] );
				if(is_array($ids)){foreach ($ids as $id) {
					self::deactivate_item($id);
				} new BESNotice('success', 'Selected questions deactivated successfully.');}
			}		
		}
	}
	public function get_views(){
		/*if(self::is_base_request()){$class = 'class="current"';} else {$class = '';}
		$status_links['all'] = "<a href='?page=spo-admin-product' $class>".'All <span class="count">('.self::record_count().')</span></a>';

		if(self::is_base_request('status', '1')){$class = 'class="current"';} else {$class = '';}
		$status_links['new'] = "<a href='?page=spo-admin-product&status=1' $class>".'New <span class="count">('.self::record_count('status', '1').')</span></a>';*/

		return $status_links;
	}
	public function views() {
		$views = self::get_views();
		$views = apply_filters( "views_{$this->screen->id}", $views );
		if ( empty( $views ) ) {
			return;
		} else {
			echo "<ul class='subsubsub'>\n";
			foreach ( $views as $class => $view ) {
	
				$views[ $class ] = "\t<li class='$class'>$view";
			}
			echo implode( " |</li>\n", $views ) . "</li>\n";
			echo "</ul>";
		}
	}
	public function prepare_items() {
	
		$this->_column_headers = $this->get_column_info();

		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'items_per_page', 10 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		) );

		$this->items = self::get_results( $per_page, $current_page );
	}
	public function no_items() {
		_e( 'No questions available.', 'RegulusReign' );
	}
	public function single_row( $item ) {
		echo '<tr id="item-'.$item[ID].'" class="items-row">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}
	public function display(){
	?>
		<form method="post">
			<?php 
			if($_REQUEST['addanq']=='new'){
				self::displayForm();
			} elseif (isset($_REQUEST['editanq']) && is_numeric($_REQUEST['editanq']) && $_REQUEST['editanq']>0){
				self::displayForm($_REQUEST['editanq']);
			} else {
				self::views();
				parent::display();
			}
			?>
		</form>
	<?php
	}
	protected function is_base_request($pid='', $pd='') {
		$i = 0;
		if(!empty($_GET['page'])){$i++;}
		if(!empty($_GET['order'])){$i++;}
		if(!empty($_GET['orderby'])){$i++;}
		if (empty($_GET)) {
			return false;
		} elseif (count($_GET)==$i && !$pid && !$pd) {
			return true;
		} elseif (count($_GET)>$i && $pid!='' && $pd!='' && $_GET[$pid]==$pd) {
			return true;
		} else {
			return false;
		}
	}
	public function displayForm($id=0){
		$update = false;
		if($id>0){$anq = self::getQuestion($id, ARRAY_A); $update=true; @$ans = implode("\r\n", unserialize($anq[answers]));}
		elseif(isset($_POST['anq'])){$anq = $_POST['anq'];}
		echo '<div class="bes-form">';
		echo '<h3>Add New Question for Evaluation</h3>';
		echo '<hr>';
		echo '<p><label>Question No. *</label> : <input type="number" class="tbox" name="anq[qno]" value="'.$anq[qno].'"> - <input type="text" class="tbox" name="anq[qno2]" value="'.$anq[qno2].'"></p>';
		echo '<p><label>Question *</label> : <input type="text" class="tbox" name="anq[question]" value="'.$anq[question].'"></p>';
		$html = ''; foreach(BESMain::getQuestionGroups() as $k=>$v){$html .= '<option value="'.$k.'"'.(($anq[group]==$k)? ' selected="Selected"':'').'>'.$v.'</option>';}
		echo '<p><label>Question Category *</label> : <select class="sbox" name="anq[group]">'.$html.'</select></p>';
		echo '<p><strong>Answers * : </strong> [Enter each answer in a separate line (seperated by \'enter\').] <textarea name="anq[answers]" class="abox" placeholder="Enter each answer in a separate line (seperated by \'enter\').">'.$ans.'</textarea></p>';
		echo '<p><label>Active Question?</label> : <input type="checkbox" class="cbox" name="anq[status]" value="1"'.(($anq[status])? ' checked="Checked"':'').'> *[Only Active questions will be shown in the form.]</p>';
		if($update){echo '<input type="hidden" name="bes-form-action" value="updateanq">';}
		else{echo '<input type="hidden" name="bes-form-action" value="addnewanq">';}
		if($update){echo '<p><input type="submit" class="bbox button" value="Update Question"></p>';}
		else{echo '<p><input type="submit" class="bbox button" value="Add Question"></p>';}
		
		echo '</div>';
	}
	public function getQuestion($id=0, $type=OBJECT){
		global $wpdb; $table = $wpdb->prefix . self::$table;
		$row = $wpdb->get_row("SELECT * FROM `$table` WHERE `ID`=$id", $type);
		return $row;
	}
	public function addQuestion(){
		global $wpdb; $table = $wpdb->prefix . self::$table;
		$anq = $_POST['anq'];
		if(!isset($_POST['anq'][status])){$anq[status]=0;}
		if(!empty($anq[question]) && !empty($anq[answers]) && !empty($anq[qno])){
			$anq[answers] = explode("\r\n", $anq[answers]);
			$anq[answers] = array_map("trim", $anq[answers]);
			$anq[answers] = array_filter($anq[answers]);
			$anq[answers] = serialize($anq[answers]);
			$id = $wpdb->insert($table, $anq);
			if($id){new BESNotice('success', 'Question added successfully.', true);}
			else {new BESNotice('error', 'An unknown error occurred, please try again.', true);}
			wp_redirect(admin_url('admin.php?page=bes-quest'));
		} else {
			new BESNotice('error', 'Please enter valid input data.');
		}
	}
	public function updateQuestion($id=0){
		global $wpdb; $table = $wpdb->prefix . self::$table;
		$anq = $_POST['anq'];
		if(!isset($_POST['anq'][status])){$anq[status]=0;}
		if(!empty($anq[question]) && !empty($anq[answers]) && !empty($anq[qno])){
			$anq[answers] = explode("\r\n", $anq[answers]);
			$anq[answers] = array_map("trim", $anq[answers]);
			$anq[answers] = array_filter($anq[answers]);
			$anq[answers] = serialize($anq[answers]);
			$result = $wpdb->update($table, $anq, array('ID'=>$id));
			if($result===false){new BESNotice('error', 'An unknown error occurred, please try again.', true);}
			else {new BESNotice('success', 'Question updated successfully.', true);}
			wp_redirect(admin_url('admin.php?page=bes-quest'));
		} else {
			new BESNotice('error', 'Please enter valid input data.');
		}
	}
	public function activate_item($id){
		global $wpdb; $table = $wpdb->prefix . self::$table;
		$wpdb->update($table, array('status'=>1), array('ID'=>$id));
	}
	public function deactivate_item($id){
		global $wpdb; $table = $wpdb->prefix . self::$table;
		$wpdb->update($table, array('status'=>0), array('ID'=>$id));
	}
}
?>