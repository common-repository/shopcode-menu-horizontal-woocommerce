<?php
/**
Plugin Name: ShopCode Menu Horizontal Woocommerce
Plugin URI: shopcode
Description: Show menu Horizontal WooCommerce with fillter categories and select menu form nav menus setting
Author: Shopcode
Version: 1.0
Author URI: shopcode.org
*/


#prefix: mhw
#function main: mhw_menu_horizontal_woocommerce
#CLASS Mhw_Menu_Horizontal_Woocommerce
#CLASSNAME  mhw-menu-horizontal-woocommerce



if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Define Path 
*/
define( 'SHOPCODE_MHW_PLUGIN_URI', plugin_dir_url( __FILE__ ));

/**
 * Adding scripts
 */
 add_action( 'wp_enqueue_scripts', 'shopcode_mhw_adding_scripts' ); 	
 if( !function_exists('shopcode_mhw_adding_scripts') ){
	function shopcode_mhw_adding_scripts() {
		wp_register_style( 'ohw_main_style', SHOPCODE_MHW_PLUGIN_URI.'assets/css/main.css', '', '1.0', false );
		wp_enqueue_style( 'ohw_main_style' );
	}}
	
	
	
class Mhw_Menu_Horizontal_Woocommerce extends WP_Widget {
	
	//	@var string (The plugin version)		
	var $version = '1.0';
	//	@var string $localizationDomain (Domain used for localization)
	var $localizationDomain = 'mhw-menu-horizontal-woocommerce';
	
	
function __construct() {
		$mhw_basename = dirname ( plugin_basename ( __FILE__ ) );
		$widget_ops = array (
		'classname' => $mhw_basename, 
		'description' => __ ( 'ShopCode Show Menu Horizontal WooCommerce', $this->localizationDomain ) 
		);
		parent::__construct( $mhw_basename, __ ( 'ShopCode Show Menu Horizontal WooCommerce', $this->localizationDomain ), $widget_ops );
	}
	

	
	function widget($args, $instance) {
		extract ( $args );
		$items_wrap = !empty( $instance['dropdown'] ) ? '<select id="amw-'.$this->number.'" class="%2$s amw" onchange="onNavChange(this)"><option value="">Select</option>%3$s</select>' : '<ul id="%1$s" class="%2$s">%3$s</ul>';
		$container = isset( $instance['container'] ) ? $instance['container'] : 'div';
		$container_id = isset( $instance['container_id'] ) ? $instance['container_id'] : '';
		$menu_class = isset( $instance['menu_class'] ) ? $instance['menu_class'] : 'menu';
		$container_class ='';
		$link_view_product = apply_filters ( 'link_view_product', isset ( $instance ['link_view_product'] ) ? esc_attr ( $instance ['link_view_product'] ) : '' );
		$category = apply_filters ( 'category', isset ( $instance ['category'] ) ? esc_attr ( $instance ['category'] ) : '' );
		$name_product_categories = apply_filters ( 'name_product_categories', isset ( $instance ['name_product_categories'] ) ? esc_attr ( $instance ['name_product_categories'] ) : '' );
		$check_show_menu = apply_filters ( 'check_show_menu', isset ( $instance ['check_show_menu'] ) ? ( bool ) $instance ['check_show_menu'] : ( bool ) false );
	    $menu = wp_get_nav_menu_object( $instance['nav_menu'] );

		if ( ! $menu || is_wp_error($menu) )
			return;

		$menu_args = array(
			'echo' => false,
			'items_wrap' => '%3$s',
			'menu' => $menu,
			'container' => true,
			'container_id' => $container_id,
			'menu_class' => $menu_class,
			
		);

		$wp_nav_menu = wp_nav_menu( $menu_args );
		
		echo $before_widget;
		
?>

<div class="clear"></div>
<div class="mhw-menu_horizontal_woo_css">
<div class="navigat">
    <h2>
	
	<?php 
if (!empty($category)) { ?>
        <a href="<?php echo get_term_link($category, 'product_cat'); ?>"><?php echo $name_product_categories; ?>
		</a>
		
<?php } ?>
	
	
	</h2>
	
	<?php if($check_show_menu){ ?>
	
    <div class="viewallcat">
	<?php
	if ( $wp_nav_menu ) {

			static $menu_id_slugs = array();

			$nav_menu ='';

			$show_container = true;
			if ( $container ) {
				$allowed_tags = apply_filters( 'wp_nav_menu_container_allowedtags', array( 'div', 'nav' ) );
				if ( in_array( $container, $allowed_tags ) ) {
					$show_container = true;
					$class = $container_class ? ' class="' . esc_attr( $container_class ) . '"' : ' class="menu-'. $menu->slug .'-container"';
					$id = $container_id ? ' id="' . esc_attr( $container_id ) . '"' : '';
					$nav_menu .= '<'. $container . $id . $class . '>';
				}
			}

			$menu_id_slugs[] = $wrap_id;
			$wrap_class = $menu_class ? $menu_class : '';
			$nav_menu .= sprintf( $items_wrap, esc_attr( $wrap_id ), esc_attr( $wrap_class ), $wp_nav_menu );
			$nav_menu .= '</' . $container . '>';
			echo $nav_menu;
		}?>
	
	
<?php 
wp_reset_query();
wp_reset_postdata();

$get_posts_from_product_cat = array(
    'post_type' => 'product',
    'post_status' => 'published',
    'product_cat' => $category,
    'numberposts' => -1
);
$count_posts_all_by_slug = count( get_posts( $get_posts_from_product_cat ) );
 ?>
<a href="<?php echo get_term_link($category, 'product_cat'); ?>" class="accessory">View all <b><?php echo $count_posts_all_by_slug; ?></b> <?php echo $link_view_product; ?></a>
</div>
<?php } ?>
	
</div></div>

<div class="clear"></div>





<?php
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance) {
		return $new_instance;
		$instance['nav_menu'] = (int) $new_instance['nav_menu'];
	}
	
	function form($instance) {
		$link_view_product = isset ( $instance ['link_view_product'] ) ? esc_attr ( $instance ['link_view_product'] ) : 'Product';
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
		$category = isset ( $instance ['category'] ) ? esc_attr ( $instance ['category'] ) : '';
		$name_product_categories = isset ( $instance ['name_product_categories'] ) ? esc_attr ( $instance ['name_product_categories'] ) : 'Name Product categories';
		$check_show_menu = isset ( $instance ['check_show_menu'] ) ? ( bool ) $instance ['check_show_menu'] : false;
		// Get menus
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );

		// If no menus exists, direct the user to go and create some.
		if ( !$menus ) {
			echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
			return;
		}
		
	
?>



    





	 <p><label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category WooCommerce:', $this->localizationDomain); ?></label>

<select
	id="<?php echo $this->get_field_id('category'); ?>"
	name="<?php echo $this->get_field_name('category'); ?>">
	<?php 
	$selected = __($category, $this->localizationDomain);
	echo '<option value="' . $selected . '" ' .( '0' == $category ? 'selected="selected"' : '' ). '>'. __($category, $this->localizationDomain).'</option>';
	
	
	$cats = get_categories(array('hide_empty' => 1, 'taxonomy' => 'product_cat', 'hierarchical' => true));
	
	
	foreach ($cats as $cat) {
		
		echo '<option value="' . $cat->slug . '" ' .( $cat->term_id == $category ? 'selected="selected"' : '' ). '>' . $cat->name . '</option>';
	}
	
	?>
	</select></p>
	
	<p>   
    
<small><?php _e('Title product categories', $this->localizationDomain); ?></small>
<?php _e('.', $this->localizationDomain); ?></small>
<input name="<?php echo $this->get_field_name('name_product_categories'); ?>"
type="text" size="30" value="<?php echo $name_product_categories; ?> " />
    </p>
	
<p><input id="<?php echo $this->get_field_id('check_show_menu'); ?>"
	name="<?php echo $this->get_field_name('check_show_menu'); ?>"
	type="checkbox" <?php checked($check_show_menu); ?> /> <label
	for="<?php echo $this->get_field_id('check_show_menu'); ?>"><?php _e('Display menu and total product', $this->localizationDomain); ?></label><br />
</p>
	
	
	
		<p>
			<label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php _e('Select Menu From nav-menus:'); ?></label>
			<select id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
				<?php
				foreach ( $menus as $menu ) {
					$selected = $nav_menu == $menu->term_id ? ' selected="selected"' : '';
					echo '<option'. $selected .' value="'. $menu->term_id .'">'. $menu->name .'</option>';
				}
				?>
			</select>
		</p>
	
  <p><label for="<?php echo $this->get_field_id('link_view_product'); ?>"><?php _e('Total product \'name\'', $this->localizationDomain); ?> <input
	class="widefat" id="<?php echo $this->get_field_id('link_view_product'); ?>"
	name="<?php echo $this->get_field_name('link_view_product'); ?>" type="text"
	value="<?php echo $link_view_product; ?>" /></label></p>
 

<?php 
    }
	
} // end class Mhw_Menu_Horizontal_Woocommerce

add_action('widgets_init', create_function('', 'return register_widget("Mhw_Menu_Horizontal_Woocommerce");'));
?>