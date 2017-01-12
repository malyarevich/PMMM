<?php
$Popups = snp_get_popups();
$ABTesting = snp_get_ab();
$Popups=(array)$Popups + (array)$ABTesting;	
$Popups['global'] = 'Use global settings';
$Popups['disabled'] = 'Disabled';
if($mode=='edit') 
{
    ?>
    <tr class="form-field">
	<th scope="row" valign="top"><label><?php _e('Ninja Popups - Welcome', 'nhp-opts'); ?></label></th>
	<td>
    <?php
}
else
{
    ?>	
    <div class="form-field">
        <label><?php _e('Ninja Popups - Welcome', 'nhp-opts'); ?></label>
    <?php
}
?>
        <select name="snp_term_meta[welcome]">
	    <?php
		foreach($Popups as $k => $v)
		{
		    echo '<option '.((!isset($snp_term_meta['welcome']) && $k=='global') || $snp_term_meta['welcome']==$k ? 'selected' : '').' value="'.$k.'">'.$v.'</option>';
		}
	    ?>
	</select>
<?php
if($mode=='edit') 
{
    ?>
	</td>
    </tr>
    <tr class="form-field">
	<th scope="row" valign="top"><label><?php _e('Ninja Popups - Exit', 'nhp-opts'); ?></label></th>
	<td>
    <?php
}
else
{
?>
    </div>
    <div class="form-field">
	<label><?php _e('Ninja Popups - Exit', 'nhp-opts'); ?></label>
    <?php
}
?>
    <select name="snp_term_meta[exit]">
	    <?php
		foreach($Popups as $k => $v)
		{
		    echo '<option '.((!isset($snp_term_meta['exit']) && $k=='global') || $snp_term_meta['exit']==$k ? 'selected' : '').' value="'.$k.'">'.$v.'</option>';
		}
	    ?>
    </select>
<?php
if($mode=='edit') 
{
    ?>
    </td>
    </tr>
    <?php
}
else
{
    ?>
    </div>  
    <?php
}
?>