<tfoot>
		<?php
			if ( $totals = $order->get_order_item_totals() ) {
				$i = 0;
				foreach ( $totals as $total ) {
					$i++;
					?><tr>
						<th scope="row" width="75%" colspan="2" style="<?php echo $missingstyle;?>text-align:left; border: 1px solid <?php echo $bordercolor;?>; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
						<td width="25%" style="<?php echo $missingstyle;?>text-align:left; border: 1px solid <?php echo $bordercolor;?>; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
					</tr><?php
				}
			}
		?>
</tfoot>