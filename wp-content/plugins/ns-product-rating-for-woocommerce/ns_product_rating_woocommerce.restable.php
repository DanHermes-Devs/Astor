<?php
if ( ! defined( 'ABSPATH' ) ) {
    wp_die( __( 'This file cannot be called directly!', 'ns' ) );
    exit;
}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class TT_Example_List_Table extends WP_List_Table {

    var $ns_ratings_data = array(
        /*array(
            'ID'        => 8,
            'title'     => '2001',
            'rating'    => 'G',
            'director'  => 'Stanley Kubrick'
        ),*/
    );

    function __construct(){
        global $status, $page;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'movie',     //singular name of the listed records
            'plural'    => 'movies',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }

    function column_default($item, $column_name){
        switch($column_name){
            case 'id_prod':
            case 'ns_title':
            case 'ns_rating':
            case 'ns_n_rating':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_title($item){

        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&movie=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&movie=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
        );

        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ //$item['title'],
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }

    function get_columns(){
        $columns = array(
            //'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'id_prod'     => 'Id Product',
            'ns_title'     => 'Product Name',
            'ns_rating'    => 'Rating',
            'ns_n_rating'  => 'N. of votes'
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'id_prod'     => array('id_prod',false),     //true means it's already sorted
            'ns_title'     => array('ns_title',false),
            'ns_rating'    => array('ns_rating',false),
            'ns_n_rating'  => array('ns_n_rating',false)
        );
        return $sortable_columns;
    }

    /*function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }*/

    function prepare_items() {
        //global $wpdb; //This is used only if making any database queries
        global $wpdb; // you may not need this part. Try with and without it
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 5;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        //$this->process_bulk_action();

        $ns_product_rating_woocommerce_votes = 'SELECT * FROM '
            . $wpdb->prefix . 'postmeta pt WHERE pt.meta_key = "_ns_prw_post_rate" ORDER BY meta_value * 1 DESC';

        $ns_products_rated_all = $wpdb->get_results($ns_product_rating_woocommerce_votes);

        for($x = 0; $x < count($ns_products_rated_all); $x++ ){
            if($x < 3){
                $post_rate    = round(floatval( get_post_meta( $ns_products_rated_all[$x]->post_id, '_ns_prw_post_rate', true ) ), 2, PHP_ROUND_HALF_EVEN);
                $rating_count = intval( get_post_meta( $ns_products_rated_all[$x]->post_id, '_ns_prw_post_rate_count', true ) );
                $prod_title = get_post( $ns_products_rated_all[$x]->post_id );
                $title_product = $prod_title->post_title;
                $id = $ns_products_rated_all[$x]->post_id;
            }else{
                $post_rate = '-';
                $rating_count = '-';
                $title_product = '-';
                $id = '<a href="http://www.google.com/url?q=http%3A%2F%2Fwww.nsthemes.com%2Fproduct%2Fwoocommerce-product-rating%2F%3Futm_source%3DWooCommerce%2520Product%2520Rating%2520Tabella%2520Risultati%26utm_medium%3DTabella%2520risultati%2520dentro%2520settings%26utm_campaign%3DWooCommerce%2520Product%2520Rating%2520Tabella%2520risultati%2520premium&sa=D&sntz=1&usg=AFQjCNHAXm__Pa0ATMFElUZj1Z5fqxa8pg">
                                GET PRO VERSION
                                </a>';
            }


            $rated_product = array(
                'ID'        => $x,
                'id_prod'   => $id,
                'ns_title'     => $title_product,
                'ns_rating'    => $post_rate,
                'ns_n_rating'  => $rating_count
            );
            array_push($this->ns_ratings_data, $rated_product);
        }

        $data = $this->ns_ratings_data;

        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id_prod'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        //usort($data, 'usort_reorder');

        $current_page = $this->get_pagenum();

        $total_items = count($data);

        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
}

function tt_render_list_page(){

    //Create an instance of our package class...
    $testListTable = new TT_Example_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $testListTable->prepare_items();

    ?>
    <div class="wrap">

        <div id="icon-users" class="icon32"><br/></div>
        <h2>Ratings results page for NS - Product Rating for WooCommerce</h2>
        <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
            <p>This page contains all the votes issued about your products.</p>
            <p><strong>In the FREE version the vision is limited to a single result</strong></p>
        </div>

        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="movies-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $testListTable->display() ?>
        </form>

    </div>
    <?php
}