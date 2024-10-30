<?php
/**
 * Plugin Name: Bitt Link Shortener
 * Plugin URI: http://seerox.com
 * Description: Get free link shortner plugin for your wordpress.
 * Version: 2.2.2
 * Author: Seerox
 * Author URI: http://seerox.com
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Requires at least: 3.8
 * Tested up to: 6.3
 */

/**
 * Prevents direct file access
 */
if ( ! defined( 'ABSPATH' ) )
{

    die('What are You looking for ? ');

}

/**
 * Define Constants
 * Enqueue file path links(plugin_dir_url) from plugin directory
 * Define DataBase Table prefix
 * Define Plugins path e.g datatable
*/

/* Table Prefix */
define ( 'WPBLS_DATABASE_TABLE', $wpdb->prefix );

/* Define Table Name */
define ( 'TABLE_BITT_LINKS', 'wpbls_bitt_links' );

/* Define Plugin URL */
define ( 'WPBLS_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

/* Define Assets URL Path */
define ( 'WPBLS_PLUGIN_ASSETS_URL', WPBLS_PLUGIN_URL.'/assets' );

/* Define Custom Plugin  CSS */
define ( 'WPBLS_PLUGIN_CSS_URL', WPBLS_PLUGIN_ASSETS_URL.'/css/' );

/* Define Custom Plugin  JS */
define ( 'WPBLS_PLUGIN_JS_URL', WPBLS_PLUGIN_ASSETS_URL.'/js/' );

/* Define datatable Plugin URL */
define ( 'WPBLS_PLUGIN_DATATABLE_URL', WPBLS_PLUGIN_URL.'/assets/plugins/datatable' );

/* Define Datatable Plugin  CSS */
define ( 'WPBLS_PLUGIN_DATATABLE_CSS_URL', WPBLS_PLUGIN_DATATABLE_URL.'/css/' );

/* Define Datatable Plugin  JS */
define ( 'WPBLS_PLUGIN_DATATABLE_JS_URL', WPBLS_PLUGIN_DATATABLE_URL.'/js/' );

/**
 * Main Class for bitt link
 */
class Bitt_Link_Shorter
{

  	// Constructor
    public function __construct ()
    {

        /**
	     * Create plugin activation or deactivation actions
	     *
	     * @since 1.0.0
	     *
	     * @uses update_option()
	     */
        register_activation_hook( __FILE__, array( $this, 'wpbls_install' ) );
        register_deactivation_hook( __FILE__, array( $this, 'wpbls_uninstall' ) );

         /**
	     * Create necessary wordpress actions hook
	     * action admin_enqueue_scripts hook
	     * action admin_menu hook
	     *
	     * @since 1.0.0
	     *
	     * @uses action perfom
	     */
        add_action( 'admin_menu', array( $this, 'wpbls_add_menu' ) );
        add_action( 'admin_menu', array( $this, 'wpbls_post_type_sidebar' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'wpbls_enqueue_scripts' ) );

        /**
	     * Create necessary Ajax actions hooks
	     *
	     * @since 1.0.0
	     *
	     * @uses Ajax action perfom
	     */
        add_action( 'wp_ajax_wpbls_single_ajax', array( $this, 'wpbls_single_ajax' ) );
		add_action( 'wp_ajax_nopriv_wpbls_single_ajax', array( $this, 'wpbls_single_ajax' ) );
		add_action( 'wp_ajax_wpbls_bluk_link_ajax', array( $this, 'wpbls_bluk_link_ajax' ) );
		add_action( 'wp_ajax_nopriv_wpbls_bluk_link_ajax', array( $this, 'wpbls_bluk_link_ajax' ) );

    }

    /**
     * Actions perform at loading of admin menu
     *
     * @return [type] [description]
     */
    public function wpbls_add_menu ()
    {

        add_menu_page( 'Bitt Link Shorter', 'Bitt Link Shorter', 'manage_options', 'bitt-link-shorter', array( $this, 'wpbls_bitt_link_shorter_dashboard') );

    }

    /**
     * Actions perform on loading of menu pages
     * Bitt Link Shorter Dashboad Page
     *
     * @return [type] [description]
     */
    public function wpbls_post_type_sidebar ()
    {

    	//	For compaitibility with older versions of WP, check if the "add_meta_box" functionality exists, if not then do it the old way
		if( function_exists( 'add_meta_box' ) )
		{

			//	Use "add_meta_box" to create the meta box for public post types
			$post_types = get_post_types( array( 'public' => true ) );

			foreach( $post_types as $post_type )
			{
				add_meta_box( 'wpbls_box', __( 'Bitt Link Shorter', 'wpbls_textdomain' ), array( $this, 'wpbls_generate_sidebar' ), $post_type, 'side', 'high' );
			}

		}
		else
		{

			//	For older versions, add the meta box to post and page edit/create pages
			add_action( 'dbx_post_sidebar', array( $this, 'wpbls_generate_sidebar' ) );
			add_action( 'dbx_page_sidebar', array( $this, 'wpbls_generate_sidebar' ) );

		}

    }

    /**
	 * Generate the content within the Shortn.It meta box.
	 */
	public function wpbls_generate_sidebar ()
	{

		$post_id = get_the_ID();

		?>
		<div class="wpbls-sidebar">

			<!-- show generate bitt link -->
			<input type="text" name="wpbls_genrate_bitt_link_shorter" value="<?php echo get_post_meta( $post_id, 'wpbls_genrate_bitt_link_shorter', true ); ?>" id="wpbls_bitt_copy_link_post_id_<?php echo $post_id; ?>" readonly >

			<!-- get current page url -->
			<input type="hidden" name="wpbls_current_link" value="<?php echo get_permalink( $post_id ); ?>" >

			<!-- get page type status -->
			<input type="hidden" name="wpbls_bitt_individual" value="<?php echo get_post_type($post_id); ?>" >

			<!-- copy generate bitt link -->
			<input type="button" name="wpbls_bitt_copy_link_p_btn" data-id="<?php echo 'post_id_'.$post_id;?>" value="Copy Bitt Link" >

			<!-- get current page id -->
			<input type="hidden" name="wpbls_current_id" value="<?php echo get_the_ID(); ?>" >

			<!-- Get bitt link calling button -->
			<input type="button" name="wpbls_bitt_link_btn" value="Get Bitt Link" >
		</div>

		<?php

	}

	/**
     * Actions perform on loading of menu pages
     * Bitt Link Shorter Dashboad Page
     *
     * @return [type] [description]
     */
    public function wpbls_bitt_link_shorter_dashboard ()
    {

    	?>
    	<div class="bitt-link-shorter-dashboar">
    		<form method="POST" action="">
    			<div class="error notice wpbls-error-notice">
    				<p>Please fill the required field.</p>
    			</div>
    			<label>Bluk Bitt Link Shorter:</label>
    			<textarea name="wpbls_bluk_bitt_link"></textarea>
    			<input type="button" name="wpbls_bluk_bitt_link_btn" value="Get Bitt Link Shorter">
    		</form>
    		<br>
    		<label>Bluk Bitt Link Shorter Result:</label>
    		<div>
    			<table id="wpbls_bluk_datatable" class="display wpbls_genrate_bulk_bitt_link_shorter" >
    				<thead>
    					<tr>
    						<th>SR#</th>
    						<th>Link</th>
    						<th>Bitt Link</th>
    						<th>Bitt individual</th>
    						<th>Bitt Created</th>
    						<th></th>
    					</tr>
    				</thead>
    				<tbody>
    				<?php
    				$count = 1;
    				foreach ( $this->wpbls_fetch_bitt_links() as $key => $bitt ) {
    					?>
						<tr>
							<td class="dt-body-center"><?php echo $count; ?></td>
							<td>
								<a href="<?php echo urldecode( $bitt['link'] ); ?>" target="_blank"><?php echo urldecode( $bitt['link'] ); ?></a>
							</td>
							<td>
								<a href="<?php echo $bitt['bitt_link']; ?>" target="_blank">
									<input type="text" id="wpbls_bitt_copy_link_<?php echo $key; ?>" value="<?php echo $bitt['bitt_link']; ?>" readonly >
								</a>
							</td>
							<td><?php echo $bitt['bitt_individual']; ?></td>
							<td><?php echo $bitt['created']; ?></td>
							<td class="dt-body-center">
								<input style="display:block" type="button" name="wpbls_bitt_copy_link_btn" onclick="wpbls_bitt_copy_link_btn(<?php echo $key; ?>)" value="Copy Bitt Link" >
							</td>
						</tr>
						<?php
						$count++;
    				}
    				?>
    				</tbody>
    			</table>
    		</div>
    	</div>
    	<?php

    }

    /**
     * *
     * @param  [number] $bitt_id         [API bitt link is]
     * @param  [string] $link            [original link]
     * @param  [string] $bitt_link       [generate bitt link ]
     * @param  [string] $bitt_individual [bitt link type]
     * @return [void]                    [insert bitt link detail]
     */
    public function wpbls_insert_bitt_links ( $bitt_id, $link, $bitt_link, $bitt_individual )
	{

		global $wpdb;

		$wpdb->query( "INSERT INTO  ".TABLE_BITT_LINKS." ( bitt_id, link, bitt_link, bitt_individual, created ) VALUES('".$bitt_id."', '".$link."', '".$bitt_link."', '".$bitt_individual."', '".date("Y-m-d H:i:s")."') ");

	}

	/**
	 * Get all bit links for TABLE_BITT_LINKS by order created date
	 *
	 * @return [array] [description]
	 */
	public function wpbls_fetch_bitt_links ()
	{

		global $wpdb;

		return $wpdb->get_results( " SELECT * FROM ".TABLE_BITT_LINKS." ORDER BY created DESC ", ARRAY_A );

	}

	/**
	 *
	 * @param  [string] $link [original bitt link]
	 * @return [array]        [insert bitt link detail]
	 */
	public function wpbls_duplicate_bitt_links ( $link )
	{

		global $wpdb;

		return $wpdb->get_results( " SELECT * FROM ".TABLE_BITT_LINKS." WHERE link = '".$link."'", ARRAY_A );

	}

	/**
	 *
	 * @param  [string] $link [original bitt link]
	 * @return [void]       [description]
	 */
	public function wpbls_update_bitt_links ( $link )
	{

		global $wpdb;

		return $wpdb->update( TABLE_BITT_LINKS, array( 'created'  => date("Y-m-d H:i:s") ), array( 'link' => $link ) );

	}

	/**
	 * wpbls bitt link shorter
	 * wpbls single ajax
	 * parameter pass by ajax call
	 *
	 * @since 1.0.0
	 *
	 * @param String (string)
	 *
	 * @return String (json)
	 */
	public function wpbls_single_ajax ()
	{

		global $wpdb, $Bitt_Link_Shorter;

		if( isset( $_POST ) )
		{

			$link          = $this->sanitize_text_field( $_POST['wpbls_current_link'] );

			$url_json      = $this->wpbls_json_encode_bitt_url( array( $link ) );

			$json_responce = $this->wpbls_generate_bitt_url( $url_json );

			if( 'success' == $json_responce[0]->status ){

				$responce_id  = $json_responce[0]->id;

				$genrate_link = $json_responce[0]->result;

				update_post_meta( $_POST['wpbls_current_id'], 'wpbls_genrate_bitt_link_shorter', $genrate_link );

				$this->wpbls_insert_bitt_links( $responce_id, $link, $genrate_link, $_POST['wpbls_bitt_individual'] );

				echo $genrate_link;

			}
			else
			{

				echo 'Please try again!';

			}

			exit;

		}

	}

	/**
	 * wpbls bitt link shortener
	 * wpbls bluk link ajax
	 * parameter pass by ajax call
	 *
	 * @since 1.0.0
	 *
	 * @param String (string)
	 *
	 * @return void
	 */
	function wpbls_bluk_link_ajax ()
	{

		global $wpdb, $Bitt_Link_Shorter;

		if( isset( $_POST ) )
		{

			$bluk_arr      = preg_split( '/\r\n|[\r\n]/', $_POST['wpbls_bluk_bitt_link'] );

			$bluk_link_arr = array();

			foreach ( $bluk_arr as $key => $link ) {

				$link           = $this->sanitize_text_field( $link );
				$duplicate_link = $this->wpbls_duplicate_bitt_links( $link );

				if( count( $duplicate_link ) > 0 ){
					$this->wpbls_update_bitt_links( $link );
				}
				else
				{
					$bluk_link_arr[] = $link;
				}

			}

			$bluk_url_json = $this->wpbls_json_encode_bitt_url( $bluk_link_arr );
			$bluk_responce = $this->wpbls_generate_bitt_url( $bluk_url_json );

			if( !empty( $bluk_responce ) ){

				foreach ( $bluk_responce as $key => $responce ) {

					$this->wpbls_insert_bitt_links( $responce->id, $bluk_link_arr[$key], $responce->result, 'bluk' );

				}

			}

			exit;
		}

	}

	/**
	 * Get the relative Bitt link Shortener URL for the current post within "the loop".
	 *
	 * @return string The relative Bitt Link Shortener URL.
	 */
	public function wpbls_generate_bitt_url ( $json )
	{

		$curl = curl_init();

		curl_setopt_array( $curl, array(

			CURLOPT_URL            => "https://bitt.link/api.php",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_POSTFIELDS 	   => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"long_url\"\r\n\r\n$json\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
		    CURLOPT_HTTPHEADER     => array( "cache-control: no-cache", "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
		  	),

		));

		$response = curl_exec( $curl );

		$err      = curl_error( $curl );

		curl_close($curl);

		if ( $err ) {
		  	echo "cURL Error #:" . $err;
		} else {
		  	return $this->wpbls_json_decode_bitt_url( $response );
		}

	}

	/**
	 * Get the relative Bitt link Shorter URL for the current post within "the loop".
	 *
	 * @return json The relative Bitt Link Shorter URL.
	 */
	public function wpbls_json_encode_bitt_url ( $string )
	{

		return json_encode( $string );

	}

	/**
	 * Get the relative Bitt link Shorter URL for the current post within "the loop".
	 *
	 * @return string The relative Bitt Link Shorter URL.
	 */
	public function wpbls_json_decode_bitt_url ( $json )
	{

		return json_decode( $json );

	}

	/**
	 * Excape string by input value
	 *
	 * @return string.
	 */
	public function sanitize_text_field( $value )
	{
		return sanitize_text_field( $value );
	}

	/**
	 * Enqueue scripts and styles to be used bitt link short layout.
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function wpbls_enqueue_scripts ( $hook_suffix )
	{

		//plugin default css
		wp_register_style( 'wpbls_style', WPBLS_PLUGIN_CSS_URL . 'wpbls_style.css' );
    	wp_enqueue_style( 'wpbls_style' );

    	//plugin default js
	    wp_register_script( 'wpbls_admin', WPBLS_PLUGIN_JS_URL . 'wpbls_admin.js' );
	    wp_enqueue_script( 'wpbls_admin' , array('jquery') );

		//plugin datatable css
	    wp_register_style( 'wpbls_datatable_css', WPBLS_PLUGIN_DATATABLE_CSS_URL . 'datatables.min.css' );
    	wp_enqueue_style( 'wpbls_datatable_css' );

    	//plugin datatable js
	    wp_register_script( 'wpbls_datatable_js', WPBLS_PLUGIN_DATATABLE_JS_URL . 'datatables.min.js' );
	    wp_enqueue_script( 'wpbls_datatable_js' , array('jquery') );

	    //	Pass "admin-ajax.php" URL for use in Javascript
		wp_localize_script( 'wpbls_ajax_url', 'vars', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

	}

    /**
	 * Loads Activation Functions Run When plugin is Activated.
	 * Update Plugin activation time.
	 * Loads activation functions.
	 *
	 * @since 1.0.0
	 *
	 * @uses @update_option() Adds a new option if exists or update if exists.
	 *
	 * @return void
	 */
    public function wpbls_install ()
    {

    	update_option( 'wpbls_install', time() );

    	$this->wpbls_create_default_table();

    }

    /**
	 * Loads deactivation function.
	 * Clears any temporary data stored by plugin.
	 *
	 * @since 1.0.0
	 *
	 * @uses @update_option()
	 *
	 * @return void
	 */
    public function wpbls_uninstall ()
    {

    	update_option( 'wpbls_uninstall', time() );

    }

    /**
     * Create Default Custom Table Settings.
     *
     * @since 1.0.0
     *
     * @uses wpbls_create_default_table() Create Default Table Settings.
     *
     * @return void
     */
    public function wpbls_create_default_table ()
    {

        global $wpdb;

	    $charset_collate   = $wpdb->get_charset_collate();

	    $table_hidden_link = " CREATE TABLE IF NOT EXISTS " . TABLE_BITT_LINKS . " (
	        id INT NOT NULL AUTO_INCREMENT,
	        bitt_id INT(11),
	        link TEXT NOT NULL,
	        bitt_link VARCHAR(150) NOT NULL,
	        bitt_individual VARCHAR(50) NOT NULL,
	        created TIMESTAMP NOT NULL,
	        PRIMARY KEY(id)
	    ) $charset_collate; ";

	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	    dbDelta( $table_hidden_link );

    }

}

global $Bitt_Link_Shorter;

$Bitt_Link_Shorter = new Bitt_Link_Shorter ();