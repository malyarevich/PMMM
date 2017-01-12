<?php header('Content-type: text/css'); ?>

/* QV Button */

.jckqvBtn {
	<?php
	$btnDisplay = 'table';
	if($this->settings['position_align'] == 'none') $btnDisplay = 'block';
	?>
	display: <?php echo $btnDisplay; ?>;
	<?php if($this->settings['styling_autohide']){ ?>
		-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
		filter: alpha(opacity=0);
		-moz-opacity: 0;
		-khtml-opacity: 0;
		opacity: 0;
		visibility: hidden;
	<?php } ?>
	float: <?php echo ($this->settings['position_align'] == 'left' || $this->settings['position_align'] == 'right') ? $this->settings['position_align'] : "none"; ?>;
	<?php $margins = array($this->settings['position_margins'][0].'px', $this->settings['position_margins'][1].'px', $this->settings['position_margins'][2].'px', $this->settings['position_margins'][3].'px'); ?>
	margin: <?php echo implode(' ', $margins); ?>;
	<?php $padding = array($this->settings['styling_padding'][0].'px', $this->settings['styling_padding'][1].'px', $this->settings['styling_padding'][2].'px', $this->settings['styling_padding'][3].'px'); ?>
	padding: <?php echo implode(' ', $padding); ?>;
	<?php if($this->settings['position_align'] == 'center') { ?>
	margin-left: auto;
	margin-right: auto;
	<?php } ?>
	<?php if($this->settings['styling_btnstyle'] != 'none') { ?>
		<?php if($this->settings['styling_btnstyle'] == 'flat') { ?>
			background: <?php echo $this->settings['styling_btncolour']; ?>;
		<?php } else { ?>
			border: 1px solid #fff;
			border-color: <?php echo $this->settings['styling_btncolour']; ?>;
		<?php } ?>
		color: <?php echo $this->settings['styling_btntextcolour']; ?>;
	<?php } ?>
	-moz-border-radius-topleft: <?php echo $this->settings['styling_borderradius'][0]; ?>px;
	-webkit-border-top-left-radius: <?php echo $this->settings['styling_borderradius'][0]; ?>px;
	 border-top-left-radius: <?php echo $this->settings['styling_borderradius'][0]; ?>px;
	-moz-border-radius-topright: <?php echo $this->settings['styling_borderradius'][1]; ?>px;
	-webkit-border-top-right-radius: <?php echo $this->settings['styling_borderradius'][1]; ?>px;
	border-top-right-radius: <?php echo $this->settings['styling_borderradius'][1]; ?>px;
	-moz-border-radius-bottomright: <?php echo $this->settings['styling_borderradius'][2]; ?>px;
	-webkit-border-bottom-right-radius: <?php echo $this->settings['styling_borderradius'][2]; ?>px;
	border-bottom-right-radius: <?php echo $this->settings['styling_borderradius'][2]; ?>px;
	-moz-border-radius-bottomleft: <?php echo $this->settings['styling_borderradius'][3]; ?>px;
	-webkit-border-bottom-left-radius: <?php echo $this->settings['styling_borderradius'][3]; ?>px;
	border-bottom-left-radius: <?php echo $this->settings['styling_borderradius'][3]; ?>px;
}

.jckqvBtn:hover {
	<?php if($this->settings['styling_btnstyle'] != 'none') { ?>
		<?php if($this->settings['styling_btnstyle'] == 'flat') { ?>
			background: <?php echo $this->settings['styling_btnhovcolour']; ?>;
		<?php } else { ?>
			border-color: <?php echo $this->settings['styling_btnhovcolour']; ?>;
		<?php } ?>
		color: <?php echo $this->settings['styling_btntexthovcolour']; ?>;
	<?php } ?>
}

/* Magnific Specific */

.mfp-bg {
	background: <?php echo $this->settings['general_overlaycolour']; ?>;
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=<?php echo $this->settings['general_overlayopacity']*10; ?>)";
	filter: alpha(opacity=<?php echo $this->settings['general_overlayopacity']*10; ?>);
	-moz-opacity: <?php echo $this->settings['general_overlayopacity']; ?>;
	-khtml-opacity: <?php echo $this->settings['general_overlayopacity']; ?>;
	opacity: <?php echo $this->settings['general_overlayopacity']; ?>;
}

/* Images */

		#jckqv .rsMinW .rsThumbsHor {
			height: <?php echo $imgsizes['thumbnail']['height']; ?>px; /* thumbnail Height */
		}
			#jckqv .rsMinW,
			#jckqv .rsMinW .rsOverflow,
			#jckqv .rsMinW .rsSlide,
			#jckqv .rsMinW .rsVideoFrameHolder,
			#jckqv .rsMinW .rsThumbs {
				background: <?php echo $this->settings['imagery_bgcolour']; ?>; /* Slide BG Colour */
			}
			#jckqv .rsMinW .rsThumb {
				width: <?php echo $imgsizes['thumbnail']['width']; ?>px; /* thumbnail Width */
				height: <?php echo $imgsizes['thumbnail']['height']; ?>px; /* thumbnail Height */
			}

/* Add to Cart */

	#jckqv .quantity {
		display: <?php echo ($this->settings['content_showqty'] == 1) ? 'inline' : 'none !important'; ?>;
	}

	<?php if($this->settings['content_themebtn'] != 1){ ?>

		#jckqv .button {
			background: <?php echo $this->settings['content_btncolour']; ?>;
			color: <?php echo $this->settings['content_btntextcolour']; ?>;
		}

			#jckqv .button:hover {
				background: <?php echo $this->settings['content_btnhovcolour']; ?>;
				color: <?php echo $this->settings['content_btntexthovcolour']; ?>;
			}

	<?php } ?>