<?php
function spb_image_resizer_get_pixelratio() {
    if ( isset($_COOKIE["spb_image_resizer_pixel_ratio"]) ) {
        $pixel_ratio = $_COOKIE["spb_image_resizer_pixel_ratio"];
        
        $debug_mode = false;
        if ( isset($_GET['spb_debug']) ) {
        	$debug_mode = $_GET['spb_debug'];
        }
        
        if ( $debug_mode ) {
        	echo 'IMAGE DEBUG -- PIXEL RATIO (' . $pixel_ratio . ') '."\n";
        }
        
        if ( $pixel_ratio >= 2 ) {
           // echo "Is HiRes Device";

			/**
			* Include AQ Resizer
			*/
			require( plugin_dir_path( __FILE__ ) . 'spb_image_resizer-2x.php' );

		}else{
            //echo "Is NormalRes Device";

			/**
			* Include AQ Resizer
			*/
			require( plugin_dir_path( __FILE__ ) . 'spb_image_resizer-1x.php' );
        }
    } else {
		require( plugin_dir_path( __FILE__ ) . 'spb_image_resizer-1x.php' );
?>
    <script>function spbImageResizer_writeCookie(){the_cookie=document.cookie,the_cookie&&window.devicePixelRatio>=2&&(the_cookie="spb_image_resizer_pixel_ratio="+window.devicePixelRatio+";"+the_cookie,document.cookie=the_cookie)}spbImageResizer_writeCookie();</script>
<?php
    }//isset($_COOKIE["spb_image_resizer_pixel_ratio"])
}//get_pixelratio
    add_action( 'wp_enqueue_scripts', 'spb_image_resizer_get_pixelratio' );
?>
