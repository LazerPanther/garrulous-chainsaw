<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version	 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'last';
?>
<li <?php post_class( $classes ); ?>>

	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

		<?php
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
/*			ob_start();
			do_action( 'woocommerce_before_shop_loop_item_title' );
			$data = ob_get_clean();
			$pattern = '|<img.*src="([^"]*)".*>|';
			preg_match($pattern, $data, $matches);
			$img_tag = isset($matches[0]) ? $matches[0] : "";
			$img_url  = isset($matches[1]) ? $matches[1] : "";
			$replacement = "<div class='pic'><div class='links'><a class='fancy fa fa-eye' href='" . $img_url . "'></a></div><div class='hover-effect'></div><img src='" . $img_url . "'></div>";
			$data = preg_replace('|' . $img_tag . '|', $replacement, $data);
			echo $data;*/
			woocommerce_show_product_loop_sale_flash();
			$img = woocommerce_get_product_thumbnail('full');
			preg_match('|<img.*src="([^"]+)".*>|',$img,$matches);
			$img_url = isset($matches[1]) ? $matches[1] : "";
			if (!empty($img_url)){
				$dims = get_option('shop_catalog_image_size');
				$thumb_url = bfi_thumb($img_url, $dims);
				echo "<div class='pic'><img src='$thumb_url' alt /><div class='hover-effect'></div><div class='links'><a class='fancy fa fa-eye' href='$img_url'></a></div></div>";
				//echo "<a class='fancy fa fa-eye' href='$img_url'></a>";
			}
			//woocommerce_template_loop_product_thumbnail();
		?>

		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

		<?php
			/**
			 * woocommerce_after_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_template_loop_rating - 5
			 * @hooked woocommerce_template_loop_price - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item_title' );
		?>


	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>

</li>