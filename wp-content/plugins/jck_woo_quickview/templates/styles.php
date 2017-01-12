<style>

/* Add to Cart */

	#jckqv .quantity {
		display: <?php echo ($this->settings['popup_content_showqty'] == 1) ? 'inline' : 'none !important'; ?>;
	}

	<?php if($this->settings['popup_content_themebtn'] != 1){ ?>

		#jckqv .button {
			background: <?php echo $this->settings['popup_content_btncolour']; ?>;
			color: <?php echo $this->settings['popup_content_btntextcolour']; ?>;
		}

			#jckqv .button:hover {
				background: <?php echo $this->settings['popup_content_btnhovcolour']; ?>;
				color: <?php echo $this->settings['popup_content_btntexthovcolour']; ?>;
			}

	<?php } ?>

</style>