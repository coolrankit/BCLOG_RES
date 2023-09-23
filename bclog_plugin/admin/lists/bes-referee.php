<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class besReferees_List extends WP_List_Table {

	private static $table = 'bes_referees';
	private static $base_request = 'admin.php?page=bes-referee';
	private static $plural = 'referees';
	public function __construct() {
		parent::__construct( array(
			'singular' => __( 'Referee', 'RegulusReign' ),
			'plural'   => __( 'Referees', 'RegulusReign' ),
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			'ajax'     => true
		) );
	}

	function get_columns() {
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'name' => __( 'Name', 'RegulusReign' ),
			'email' => __( 'Email', 'RegulusReign' ),
			//'phone' => __( 'Phone No.', 'RegulusReign' ),
			'status' => __( 'Status', 'RegulusReign' ),
		);
		return $columns;
	}
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'firstname', false ),
			'status' => array( 'status', false ),
		);
		return $sortable_columns;
	}
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
				$html = $item['firstname'].' '.$item['lastname'];
				$eurl = admin_url('admin.php?page=bes-referee&editanr='.$item['ID']);
				$html .= '<div class="row-actions"><span class="edit"><a href="'.$eurl.'">Edit</a></span></div>';
				return $html;
			case 'status':
				return (($item['status'])? 'Active':'Inactive');
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
				} new BESNotice('success', 'Selected referees activated successfully.');}
			}		
		}
		if ( (isset( $_POST['action'] ) && $_POST['action'] == 'bulk-deactivate') || (isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-deactivate') ) {
			if ( isset($_REQUEST['_wpnonce']) && !wp_verify_nonce($nonce, 'bulk-' . ((is_object($this) && $this->_args['plural'])? $this->_args['plural']:self::$plural)) ) {die( 'Go get a life script kiddies' );}
			else {
				$ids = esc_sql( $_POST['bulk-ids'] );
				if(is_array($ids)){foreach ($ids as $id) {
					self::deactivate_item($id);
				} new BESNotice('success', 'Selected referees deactivated successfully.');}
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

		//$this->process_bulk_action();

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
		_e( 'No referees available.', 'RegulusReign' );
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
			if($_REQUEST['addanr']=='new'){
				self::displayForm();
			} elseif (isset($_REQUEST['editanr']) && is_numeric($_REQUEST['editanr']) && $_REQUEST['editanr']>0){
				self::displayForm($_REQUEST['editanr']);
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
		if($id>0){$anr = self::getItem($id, ARRAY_A); $update=true;}
		elseif(isset($_POST['anr'])){$anr = $_POST['anr'];}
		echo '<div class="bes-form">';
		echo '<h3>Add New Referee to Evaluate</h3>';
		echo '<hr>';
		echo '<p><label>First Name *</label> : <input type="text" class="tbox" name="anr[firstname]" value="'.$anr[firstname].'"></p>';
		echo '<p><label>Last Name *</label> : <input type="text" class="tbox" name="anr[lastname]" value="'.$anr[lastname].'"></p>';
		echo '<p><label>Email Address</label> : <input type="text" class="tbox" name="anr[email]" value="'.$anr[email].'"></p>';
		echo '<p><label>Sport</label> : <input type="text" class="tbox" name="anr[sport]" value="'.$anr[sport].'"></p>';
		echo '<p><label>Certification</label> : <input type="text" class="tbox" name="anr[certification]" value="'.$anr[certification].'"></p>';
		echo '<p><label>Active Referee?</label> : <input type="checkbox" class="cbox" name="anr[status]" value="1"'.(($anr[status]==1)? ' selected="Selected"':'').'> <em>[Only Active referees will be available to rate.]</em></p>';
		if($update){echo '<input type="hidden" name="bes-form-action" value="updateanr">';}
		else{echo '<input type="hidden" name="bes-form-action" value="addnewanr">';}
		if($update){echo '<p><input type="submit" class="bbox button" value="Update Referee"></p>';}
		else{echo '<p><input type="submit" class="bbox button" value="Add Referee"></p>';}
		
		echo '</div>';
	}
	public function getItem($id=0, $type=OBJECT){
		global $wpdb; $table = $wpdb->prefix . self::$table;
		$row = $wpdb->get_row("SELECT * FROM `$table` WHERE `ID`=$id", $type);
		return $row;
	}
	public function activate_item($id){
		global $wpdb; $table = $wpdb->prefix . self::$table;
		$wpdb->update($table, array('status'=>1), array('ID'=>$id));
	}
	public function deactivate_item($id){
		global $wpdb; $table = $wpdb->prefix . self::$table;
		$wpdb->update($table, array('status'=>0), array('ID'=>$id));
	}
	public function addReferee(){
		global $wpdb; $table = $wpdb->prefix . self::$table;
		$data = $_POST['anr'];
		if(!empty($data[firstname]) && $data[lastname]){
			$id = $wpdb->insert($table, $data);
			if($id){new BESNotice('success', 'Referee added successfully.', true);}
			else {new BESNotice('error', 'An unknown error occurred, please try again.', true);}
			wp_redirect(admin_url('admin.php?page=bes-referee'));
		} else {
			new BESNotice('error', 'Please enter valid input data.');
		}
	}
	public function updateReferee($id=0){
		global $wpdb; $table = $wpdb->prefix . self::$table;
		$data = $_POST['anr'];
		if(!empty($data[firstname]) && $data[lastname]){
			$result = $wpdb->update($table, $data, array('ID'=>$id));
			if($result===false){new BESNotice('error', 'An unknown error occurred, please try again.', true);}
			else {new BESNotice('success', 'Referee updated successfully.', true);}
			wp_redirect(admin_url('admin.php?page=bes-referee'));
		} else {
			new BESNotice('error', 'Please enter valid input data.');
		}
	}
}
?>