<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://swiftideas.com/swift-framework
 * @since      1.0.0
 *
 * @package    swift-framework
 * @subpackage swift-framework/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    swift-framework
 * @subpackage swift-framework/admin
 * @author     Swift Ideas
 */
class SwiftFramework_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $SwiftFramework    The ID of this plugin.
	 */
	private $SwiftFramework;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $SwiftFramework       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $SwiftFramework, $version ) {

		$this->SwiftFramework = $SwiftFramework;
		$this->version = $version;

	}

	/**
	 * Add about page menu
	 *
	 * @since    1.0.0
	 */
	public function add_swiftframework_menu() {
		//add_menu_page( 'Swift Framework', 'Swift Framework', 'manage_options', 'swift-framework/admin/swift-framework-admin-page.php', '', plugin_dir_url(__FILE__).'/img/logo.png', 100 );
		add_menu_page(
		    'Swift Framework',
		    'Swift Framework',
		    'manage_options',
		    'swift-framework',
		    array($this, 'swift_framework_about_content'),
		    plugin_dir_url(__FILE__).'/img/logo.png'
		);

		add_submenu_page(
	        'swift-framework',
	        'Swift Framework',
	        'Swift Framework',
	        'manage_options',
	        'swift-framework'
	    );

		add_submenu_page(
	        'swift-framework',
	        'Instagram Authentication',
	        'Instagram Auth',
	        'manage_options',
	        'swift-framework-instagram',
	        array($this, 'swift_framework_instagram_content')
	    );

	    add_submenu_page(
	        'swift-framework',
	        'Google Maps Authentication',
	        'Google Maps Auth',
	        'manage_options',
	        'swift-framework-gmaps',
	        array($this, 'swift_framework_gmaps_content')
	    );
	}

	/**
	 * Add about page content
	 *
	 * @since    1.0.0
	 */
	public function swift_framework_about_content() {
	  ?>
		<div class="sf-about-wrap">
			<h1>Swift Framework</h1>

			<h2 class="nav-tab-wrapper">
				<a class="nav-tab nav-tab-active" href="<?php echo admin_url('index.php?page=swift-framework') ?>">About</a>
				<a class="nav-tab" href="<?php echo admin_url('admin.php?page=swift_framework_opts_options') ?>">Options</a>
				<a class="nav-tab" href="<?php echo admin_url('admin.php?page=swift-framework-instagram'); ?>">Instagram Authentication</a>
				<a class="nav-tab" href="<?php echo admin_url('admin.php?page=swift-framework-gmaps'); ?>">Google Maps Authentication</a>
				<a class="nav-tab" href="http://pagebuilder.swiftideas.com/documentation/" target="_blank">Swift Page Builder Documentation</a>
			</h2>

			<div class="about-content">

				<?php if ( !class_exists( 'ReduxFramework' ) ) { ?>
					<h4>Please install the Redux Framework plugin, as this is required for the Swift Framework plugin to function correctly.</h4>
					<br/><br/>
				<?php } ?>

				<p></p>
				<h3>Coming Soon</h3>
				<ul>
					<li>Preview functionality for elements.</li>
				</ul>

				<div class="divide"></div>
				<h3>Latest Update (v2.5.23)</h3>
				<p></p>
				<ul>
					<li>FRONTEND: Icon Box Character mode now supports 2 characters</li>
					<li>FRONTEND: Minor fixes</li>
				</ul>
				<div class="divide"></div>
				<h3>Previous Update (v2.5.21)</h3>
				<p></p>
				<ul>
					<li>FRONTEND: Added testimonial image size filter</li>
				 	<li>FRONTEND: Fixed row styling issues</li>
					<li>FRONTEND: Fixed image caption output</li>
					<li>FRONTEND: Fixed image banner output</li>
					<li>BACKEND: Added author support for Portfolio</li>
					<li>BACKEND: Fixed minor responsive issues</li>
					<li>BACKEND: Fixed multi-select fields defaulting to first item</li>
				</ul>
			</div>
		</div>
	  <?php
	}

	/**
	 * Add instagram tab content
	 *
	 * @since    2.3.0
	 */
	public function swift_framework_instagram_content() {
		if ( isset($_POST['updated']) && $_POST['updated'] === 'true' ) {
			$this->handle_instagram_auth_form();
		}
	  ?>
		<div class="sf-about-wrap">
			<h1>Swift Framework - Instagram Authentication</h1>

			<h2 class="nav-tab-wrapper">
				<a class="nav-tab" href="<?php echo admin_url('index.php?page=swift-framework') ?>">About</a>
				<a class="nav-tab" href="<?php echo admin_url('admin.php?page=swift_framework_opts_options') ?>">Options</a>
				<a class="nav-tab nav-tab-active" href="<?php echo admin_url('admin.php?page=swift-framework-instagram'); ?>">Instagram Authentication</a>
				<a class="nav-tab" href="<?php echo admin_url('admin.php?page=swift-framework-gmaps'); ?>">Google Maps Authentication</a>
				<a class="nav-tab" href="http://pagebuilder.swiftideas.com/documentation/" target="_blank">Swift Page Builder Documentation</a>
			</h2>

			<div class="instagram-generate-content about-content">
				<p>In order for the theme to display your Instagram photos, you are required to provide an Instagram Access Token. You can retrieve this by clicking the button below. You'll be prompted by Instagram to authorize Swift Framework to access your Instagram photos, and you may need to enter your Instagram login credentials.</p>

				<a href="https://instagram.com/oauth/authorize/?client_id=756db9880cc84c3dab85118df38f9b91&scope=basic+public_content&redirect_uri=http://www.swiftideas.com/instagram-authentication-redirect/?return_uri=<?php echo admin_url('admin.php?page=swift-framework-instagram'); ?>&response_type=token" class="sf-instagram-button sf-auth-button"><?php _e('Generate Access Token', 'swift-framework-plugin'); ?></a>
                
               <div class="divide"></div>

               <div id="sf_instagram_info"></div>

                <form method="post">
                	<input type="hidden" name="updated" value="true" />
                	<?php wp_nonce_field( 'sf_instagram_form_update', 'sf_instagram_form' ); ?>
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="access_token">Access Token</label></th>
								<td><input name="access_token" id="access_token" type="text" class="regular-text" value="<?php echo get_option('sf_instagram_access_token'); ?>" /></td>
							</tr>
							<tr>
								<th><label for="user_id">User ID</label></th>
								<td><input name="user_id" id="user_id" type="text" class="regular-text" value="<?php echo get_option('sf_instagram_user_id'); ?>" /></td>
							</tr>
						</tbody>
					</table>
					<p class="submit">
						<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
					</p>
				</form>

				<div class="divide"></div>

				<h2>FAQ</h2>

				<p><strong>What is an access token, and why do I need one?</strong> An Access Token is a string of characters unique to your account that grants other applications access to your Instagram feed. The token is required for providing a secure way for your website to ask Instagram's permission to access your profile and display its images.</p>

				<p><strong>Is it safe for you to have access to my token?</strong> Yes - we (Swift Ideas), do not have access to your Instagram tokens, nor do we intend to use them or your photos for any purpose.</p>
			</div>

		</div>
	  <?php
	}

	/**
	 * Register the instagram auth form
	 *
	 * @since    2.3.0
	 */
	public function handle_instagram_auth_form() {
	    if(
			! isset( $_POST['sf_instagram_form'] ) ||
			! wp_verify_nonce( $_POST['sf_instagram_form'], 'sf_instagram_form_update' )
		){ ?>
	    	<div class="error">
	           <p>Sorry, your nonce was not correct. Please try again.</p>
	    	</div> <?php
	    	exit;
	    } else {
	    	// Handle our form data
	    	$access_token = sanitize_text_field( $_POST['access_token'] );
			$user_id = sanitize_text_field( $_POST['user_id'] );

			update_option( 'sf_instagram_access_token', $access_token );
			update_option( 'sf_instagram_user_id', $user_id );?>
			<div class="updated">
				<p>Your Instagram authentication details were saved.</p>
			</div> <?php
	    }
	}

	/**
	 * Add about page menu
	 *
	 * @since    2.4.0
	 */
	public function swift_framework_gmaps_content() {
		if ( isset($_POST['updated']) && $_POST['updated'] === 'true' ) {
			$this->handle_gmaps_auth_form();
		}
	  ?>
		<div class="sf-about-wrap">
			<h1>Swift Framework - Google Maps Authentication</h1>

			<h2 class="nav-tab-wrapper">
				<a class="nav-tab" href="<?php echo admin_url('index.php?page=swift-framework') ?>">About</a>
				<a class="nav-tab" href="<?php echo admin_url('admin.php?page=swift_framework_opts_options') ?>">Options</a>
				<a class="nav-tab" href="<?php echo admin_url('admin.php?page=swift-framework-instagram'); ?>">Instagram Authentication</a>
				<a class="nav-tab nav-tab-active" href="<?php echo admin_url('admin.php?page=swift-framework-gmaps'); ?>">Google Maps Authentication</a>
				<a class="nav-tab" href="http://pagebuilder.swiftideas.com/documentation/" target="_blank">Swift Page Builder Documentation</a>
			</h2>

			<div class="gmaps-generate-content instagram-generate-content about-content">
				<p>All Google maps now require an API key to function. You can read about this change <a href="https://googlegeodevelopers.blogspot.co.za/2016/06/building-for-scale-updates-to-google.html" target="_blank">here</a>. Please click the button below, and then copy your API key to the form below and save. Once you have done this, your maps will show after 5 minutes or so.</p>

				<a href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend&keyType=CLIENT_SIDE&reusekey=true" class="sf-gmaps-button sf-auth-button" target="_blank"><?php _e('Generate API Key', 'swift-framework-plugin'); ?></a>
                
                <div class="divide"></div>

                <form method="post">
                	<input type="hidden" name="updated" value="true" />
                	<?php wp_nonce_field( 'sf_gmaps_auth_form_update', 'sf_gmaps_auth_form' ); ?>
					<table class="form-table">
						<tbody>
							<tr>
								<th><label for="api_key">API Key</label></th>
								<td><input name="api_key" id="api_key" type="text" class="regular-text" value="<?php echo get_option('sf_gmaps_api_key'); ?>" /></td>
							</tr>
						</tbody>
					</table>
					<p class="submit">
						<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
					</p>
				</form>

				<div class="divide"></div>

				<h2>FAQ</h2>

				<p><strong>What is an API key, and why do I need one?</strong> An API Key is a string of characters unique to your account that grants other applications access to talk to one another. Google allows you to allow specific domains access to your API key, so that it is kept safe, and we'd recommend you enable this.</p>

				<p><strong>Is it safe for you to have access to my API key?</strong> Yes - we (Swift Ideas), do not have direct access to your API Key, and Google provide the configuration to set only allowed domains to have permission to use the key.</p>
			</div>

		</div>
	  <?php
	}

	/**
	 * Register the gmaps auth form
	 *
	 * @since    2.3.0
	 */
	public function handle_gmaps_auth_form() {
	    if(
			! isset( $_POST['sf_gmaps_auth_form'] ) ||
			! wp_verify_nonce( $_POST['sf_gmaps_auth_form'], 'sf_gmaps_auth_form_update' )
		){ ?>
	    	<div class="error">
	           <p>Sorry, your nonce was not correct. Please try again.</p>
	    	</div> <?php
	    	exit;
	    } else {
	    	// Handle our form data
			$api_key = sanitize_text_field( $_POST['api_key'] );

			update_option( 'sf_gmaps_api_key', $api_key );?>
			<div class="updated">
				<p>Your Google Maps API key was saved.</p>
			</div> <?php
	    }
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SwiftFramework_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SwiftFramework_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->SwiftFramework, plugin_dir_url( __FILE__ ) . 'css/swiftframework-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in SwiftFramework_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The SwiftFramework_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->SwiftFramework, plugin_dir_url( __FILE__ ) . 'js/swiftframework-admin.js', array( 'jquery' ), $this->version, false );

	}

}
