<?php
/**
 * Invoice Content
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanizedPro/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$newY = $pdf->getY();

?>

<?php if ( $invoice->get_option( 'address_margin_top' ) ) : ?>
	<?php $pdf->setTmpY( $pdf->getY() + $invoice->get_option( 'address_margin_top' ) ); ?>
<?php endif; ?>

<?php if ( $invoice->get_option( 'show_sender_address' ) == 'yes' ) : ?>
	<?php $pdf->writeCustomHTML( implode( ' - ', $invoice->get_sender_address() ), array( 'classes' => array( 'address-header' ) ) ); ?>
<?php endif; ?>

<?php if ( $invoice->get_address() ) : ?>
	<?php $pdf->writeCustomHTML( wpautop( $invoice->get_address() ), array( 'classes' => array( 'address' ) ) ); ?>
<?php endif; ?>

<?php 
	
	$newY = $pdf->getY();
	$pdf->resetTmpY(); 

?>

<?php if ( $invoice->get_option( 'show_sender_address_detail' ) == 'yes' ) : ?>
	<?php $pdf->writeCustomHTML( $invoice->get_template_content( $invoice->locate_template( 'info-right.php' ), $pdf ), array( 'classes' => array( 'address-header-detail' ), 'align' => 'R' ) ); ?>
<?php endif; ?>

<?php if ( $invoice->get_option( 'date_margin_top' ) ) : ?>
	<?php $pdf->setTmpY( $pdf->getY() + $invoice->get_option( 'date_margin_top' ) ); ?>
<?php endif; ?>

<?php $pdf->writeCustomHTML( wpautop( $invoice->get_date( $invoice->get_option( 'date_format' ) ) ), array( 'classes' => array( 'date' ), 'align' => 'R' ) ); ?>

<?php 

	$pdf->resetTmpY(); 

	if ( $newY > $pdf->getY() )
		$pdf->setXY( $pdf->getX(), $newY ); 

?>

<?php if ( $invoice->get_option( 'title_margin_top' ) ) : ?>
	<?php $pdf->setY( $pdf->getY() + $invoice->get_option( 'title_margin_top' ) ); ?>
<?php endif; ?>

<?php $pdf->writeCustomHTML( wpautop( $invoice->get_title_pdf() ), array( 'classes' => array( 'number' ) ) ); ?>

<?php if ( $invoice->get_option( 'table_margin_top' ) ) : ?>
	<?php $pdf->setY( $pdf->getY() + $invoice->get_option( 'table_margin_top' ) ); ?>
<?php endif; ?>

<?php $pdf->writeCustomHTML( $invoice->get_template_content( $invoice->locate_template( 'table.php' ), $pdf ), array( 'classes' => array( 'table' ) ) ); ?>