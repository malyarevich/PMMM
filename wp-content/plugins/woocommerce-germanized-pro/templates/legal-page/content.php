<?php
/**
 * Legal Page Content
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanizedPro/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$newY = $pdf->getY();

?>

<?php if ( $legal_page->get_option( 'title_margin_top' ) ) : ?>
	<?php $pdf->setY( $pdf->getY() + $legal_page->get_option( 'title_margin_top' ) ); ?>
<?php endif; ?>

<?php if ( $legal_page->get_option( 'show_title' ) === 'yes' ) : ?>
	<?php $pdf->writeCustomHTML( '<h1>' . $legal_page->get_title_pdf() . '</h1>', array( 'classes' => array( 'title' ) ) ); ?>
<?php endif; ?>

<?php if ( $legal_page->get_option( 'content_margin_top' ) ) : ?>
	<?php $pdf->setY( $pdf->getY() + $legal_page->get_option( 'content_margin_top' ) ); ?>
<?php endif; ?>

<?php if ( $legal_page->get_static_pdf_text( 'before_content' ) ) : ?>
	<?php $pdf->writeCustomHTML( $legal_page->get_static_pdf_text( 'before_content' ), array( 'classes' => array( 'static' ) ) ); ?>
<?php endif; ?>

<?php $pdf->writeCustomHTML( $legal_page->get_template_content( $legal_page->locate_template( 'text.php' ), $pdf ), array( 'classes' => array( 'content' ) ) ); ?>

<?php if ( $legal_page->get_static_pdf_text( 'after_content' ) ) : ?>
	<?php $pdf->writeCustomHTML( $legal_page->get_static_pdf_text( 'after_content' ), array( 'classes' => array( 'static' ) ) ); ?>
<?php endif; ?>