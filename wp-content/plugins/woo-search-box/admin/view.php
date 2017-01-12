<?php
if (!defined('ABSPATH')) {
    die;
}
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br></div><h2>Guaven Woo Search Suggestions
  <span style="float:right"><a class="button" href="?page=woo-search-box%2Fadmin%2Fclass-search-analytics.php">Analytics</a></span></h2>
<?php
settings_errors();
?>

<form action="" method="post" name="settings_form">
<?php
wp_nonce_field('guaven_woos_nonce', 'guaven_woos_nonce_f');
?>




<h3>Cache re/builder</h3>

<p>
This button generates the needed cached data based on your products and using parameters below.</p>
<?php if (defined('W3TC')) echo '<p style="color:red">It seems you are using W3 Total Cache which blocks rebuilding process by default (due to its Object Cache feature).
Please go to W3TC settings and <a target="_blank" href="'.admin_url('admin.php?page=w3tc_general&w3tc_note=config_save#object_cache').'">disable Object Cache</a> to let
our rebuilder working. When everything is ok, ignore this message.
</p>';?>
<div style="height:30px">
<input type="button" class="rebuilder button button-primary" value="Rebuild Cache" style="float:left"></div>

<div style="font-weight: bold;font-size:14px;background:yellow;margin-top:10px;display:none;clear:both;padding: 10px" id="result_field"></div>


<h3><br>Enable/disable the features</h3>

<table class="form-table" id="box-table-a">
<tbody>



<tr valign="top">
<th scope="row" class="titledesc">Initial texts</th>
<td scope="row">

<p>

<label>
        <input name="guaven_woos_showinit" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_showinit") != '') {
    echo 'checked="checked"';
}
?>>
        Show initial help message to visitor when he/she focuses on search area.    </label>

</p>
<br>
<p>

<label>
Initial box text
<input name="guaven_woos_showinit_t" type="text" id="guaven_woos_showinit_t"
value='<?php
echo get_option("guaven_woos_showinit_t");
?>' class="small-text" style="width:500px"
placeholder='F.e: Type here any product name you want: f.e. iphone, samsung etc.'>
</label>
</p><br>
<p>
<label>
"No match" text
<input name="guaven_woos_showinit_n" type="text" id="guaven_woos_showinit_n"
value='<?php
echo esc_attr(get_option("guaven_woos_showinit_n"));
?>' class="" style="width:500px"
placeholder='No any products found...'>
</label>
</p>


    </td>
</tr>



<tr valign="top">
<th scope="row" class="titledesc">Featured products</th>
<td scope="row">
<p>
<label>
        <input name="guaven_woos_ispin" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_ispin") != '') {
    echo 'checked="checked"';
}
?>>
        Enable pinned products in search suggestion box (will be shown when user just focused on search box) </label>
</p>

<br>
<p>
<label>
      Pinned product ID numbers:

<input name="guaven_woos_pinneds" type="text" id="guaven_woos_pinneds"
value="<?php
echo get_option("guaven_woos_pinneds");
?>">
</label>
<small>(comma separated: f.e.  123,14233,2323 etc.) </small>
</p>

<br>
<p>
<label>
      Title text for pinned suggestions:

<input name="guaven_woos_pinnedt" type="text" id="guaven_woos_pinnedt"
value="<?php
echo get_option("guaven_woos_pinnedt");
?>">
</label>
<small>(f.e.  Featured products.) </small>
</p>


</td> </tr>





<tr valign="top">
<th scope="row" class="titledesc">Personalizated suggestions for users</th>
<td scope="row">
<p>
<label>
        <input name="guaven_woos_ispers" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_ispers") != '') {
    echo 'checked="checked"';
}
?>>
        Enable cookie based personalizated initial suggestions (will be shown when user just focused on search box)</label>
</p>

<br>
<p>
<label>
      Title text for personalizated initial suggestions:

<input name="guaven_woos_perst" type="text" id="guaven_woos_perst"
value="<?php
echo get_option("guaven_woos_perst");
?>">
</label>
<small>(f.e.  Last watched products.) </small>
</p>


<p>
<label>
      Max number of personal suggestions:

<input name="guaven_woos_persmax" type="number" id="guaven_woos_persmax"
value="<?php
echo get_option("guaven_woos_persmax");
?>">
</label>
<small>(default is 5) </small>
</p>

</td> </tr>



<tr valign="top">
<th scope="row" class="titledesc">The most popular products block after "no match" message</th>
<td scope="row">

<p>
<label>
        <input name="guaven_woos_nomatch_pops" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_nomatch_pops") != '') {
    echo 'checked="checked"';
}
?>>
        Show the most popular products below when "no match" (not found) message appears?</label>
</p>


<br>
<p>
<label>
      Meta key name for product popularity:

<input name="guaven_woos_popsmkey" type="text" id="guaven_woos_popsmkey"
value="<?php
echo get_option("guaven_woos_popsmkey");
?>">
</label>
<small>(f.e. total_sales, view_count, views etc. You should check your products custom fields if you don't know its exact name) </small>
</p>
<br>
<p>
<label>
      Max number of popular products:

<input name="guaven_woos_popsmax" type="number" id="guaven_woos_popsmax"
value="<?php
echo get_option("guaven_woos_popsmax");
?>">
</label>
<small>(default is 5) </small>
</p>
</td></tr>

<tr valign="top">
<th scope="row" class="titledesc">Backend Search (<b><i style="color:red">new!</i></b>)</th>
<td scope="row">
<p>
<label>
        <input name="guaven_woos_backend" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_backend") != '') {
    echo 'checked="checked"';
}
?>>
        Try to show same smart results at Backend side too (replaces theme's default search results and happens after pressing enter)</label>
</p>
<small>This feature works on cookie based algorithm</small>
</td></tr>


<tr valign="top">
<th scope="row" class="titledesc">Search by category names block</th>
<td scope="row">

<p>
<label>
        <input name="guaven_woos_catsearch" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_catsearch") != '') {
    echo 'checked="checked"';
}
?>>
        Show search results by category names block?</label>
</p>
<br>



<p>
<label>
      Max number of shown categories:

<input name="guaven_woos_catsearchmax" type="number" id="guaven_woos_catsearchmax"
value="<?php
echo get_option("guaven_woos_catsearchmax");
?>">
</label>
<small>(default is 5) </small>
</p>


</td></tr>







<tr valign="top">
<th scope="row" class="titledesc">Global settings</th>
<td scope="row">


<?php
$guaven_woos_autorebuild=get_option("guaven_woos_autorebuild");
 ?>
<p>
<label>
Do the Cache Auto-Rebuild after each time when you edit any product / show manual rebuilder button in admin top bar:
<select name="guaven_woos_autorebuild">
<option value="b1a0" <?php echo $guaven_woos_autorebuild=='b1a0'?'selected':''; ?>>Enable top rebuild button / disable auto-rebuild</option>
<option value="b1a1" <?php echo $guaven_woos_autorebuild=='b1a1'?'selected':''; ?>>Enable top rebuild button / enable auto-rebuild</option>
<option value="b0a1" <?php echo $guaven_woos_autorebuild=='b0a1'?'selected':''; ?>>Disable top rebuild button / enable auto-rebuild</option>
<option value="b0a0" <?php echo $guaven_woos_autorebuild=='b0a0'?'selected':''; ?>>Disable top rebuild button / disable auto-rebuild</option>
</select>
</p>
<br>

<p>
<label>
        <input name="guaven_woos_autorebuild_editor" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_autorebuild_editor") != '') {
    echo 'checked="checked"';
}
?>>
        Enable this feature for "shop manager" users </label>

</p>    <small>By default, the feature is available only for administrators.
  <br />So, if to check this, then administrators and shop managers will be able to use rebuild button and auto-rebuild feature. </small>


<p><br>
<label>
        <input name="guaven_woos_async" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_async") != '') {
    echo 'checked="checked"';
}
?>>
        Load cached data asynchronously </label>
</p>
<small>Recommended for stores with >1000 products.</small>
</td></tr>



</tbody> </table>


<hr>
<h3>General parameters</h3>


<table class="form-table" id="box-table-a">
<tbody>


<tr valign="top">
<th scope="row" class="titledesc">Typo resolver and search by description</th>
<td scope="row">

<p>
<label>
        <input name="guaven_woos_corr_act" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_corr_act") != '') {
    echo 'checked="checked"';
}
?>>
        Automatic Correction feature     </label>
</p>
<small>For example if a user types ifone instead of iphone, or kidshoe instead of Kids Shoes this feature will understand him/her and will suggest
corresponding products.</small>


<br>


<p>
<label>
        Show suggestions by autocorrected key if there are
<input name="guaven_woos_whentypo" type="number" step="1"  id="guaven_woos_whentypo"
value="<?php
echo get_option("guaven_woos_whentypo")>0?(int) get_option("guaven_woos_whentypo"):10;
?>" class="small-text"> or less suggestions for original input.
</label>
</p>

<br>
<p>
<label>
        <input name="guaven_woos_add_description_too" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_add_description_too") != '') {
    echo 'checked="checked"';
}
?>>
        Search by product description   </label>
</p>
<small>Although the description will be hidden in search suggestions, the plugin will give the results based on description.<br>
Check this only if it is very important for your store.
</small>


</td> </tr>




<tr valign="top">
<th scope="row" class="titledesc">Which products will appear</th>
<td scope="row">




<p>
<label>
        Maximum numbers of products in cached data.
<input name="guaven_woos_maxprod" type="number" step="1000" min="1000" id="guaven_woos_maxprod"
value="<?php
echo (int) get_option("guaven_woos_maxprod");
?>" class="small-text"> (defaul is 5000).
</label>
</p>
<br>

<p>
<label>
        Show suggestion after
<input name="guaven_woos_min_symb_sugg" type="number" step="1" min="1" id="guaven_woos_min_symb_sugg"
value="<?php
echo (int) get_option("guaven_woos_min_symb_sugg");
?>" class="small-text"> symbols entered by a visitor.
</label>
</p>

<br>
<p>
<label>
        <input name="guaven_woos_nostock" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_nostock") != '') {
    echo 'checked="checked"';
}
?>>
        Include out of stock products     </label>
</p>

<br>
<p>
<label>
        <input name="guaven_woos_removehiddens" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_removehiddens") != '') {
    echo 'checked="checked"';
}
?>>
        Hide "Catalog visibility = hidden" products at live search box</label>
</p>

<br>
<p>
<label>
        <input name="guaven_woos_variation_skus" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_variation_skus") != '') {
    echo 'checked="checked"';
}
?>>
        Show all variations when there is a keyword match </label>
</p>

<br>
<p>
<label>
        Order by products by
<input name="guaven_woos_customorder" type="text"  id="guaven_woos_customorder"
value="<?php
echo  get_option("guaven_woos_customorder");
?>">
</label>
<small>Use format: <i>date</i>, <i>title</i>, <i>ID</i> or <i>meta:total_sales</i>, <i>meta:view_count</i> etc...</small>
</p>
  </td>
</tr>

<tr valign="top">
<th scope="row" class="titledesc">Suggestion box sizes</th>
<td scope="row">


<p>
<label>
        Suggestion bar width
<input name="guaven_woos_sugbarwidth" type="number" step="1" min="1" id="guaven_woos_sugbarwidth"
value="<?php
echo (int) get_option("guaven_woos_sugbarwidth");
?>" class="small-text">% of search bar.
</label>
</p>

<p>
<label>
        Maximal number of suggestion results
<input name="guaven_woos_maxres" type="number" step="1" min="1" id="guaven_woos_maxres" value="<?php
echo get_option("guaven_woos_maxres");
?>" class="small-text">
        results.
</label>
</p>



  </td>
</tr>

</tbody></table>





<hr>
<h3>Advanced Settings and Customizations</h3>


<table class="form-table" id="box-table-a">
<tbody>

<tr valign="top">
<th scope="row" class="titledesc">Cache rebuilder command for cron jobs</th>

<td scope="row">


<code>
php <?php echo ABSPATH;?>index.php  <?php echo $cron_token; ?>
</code>

</td> </tr>


<tr valign="top">
<th scope="row" class="titledesc">Live result template</th>
<td scope="row">


<p>Search suggestions layout (Don't use line-breaks) </p>

 <textarea name="guaven_woos_layout" id="guaven_woos_layout" class="large-text code" rows="3"><?php
echo stripslashes(get_option("guaven_woos_layout"));
?></textarea>

<code>
To restore default layout just empty the area and save settings.
</code>

</td></tr>

<tr valign="top">
<th scope="row" class="titledesc">Custom css</th>
<td scope="row">

<p>Custom css for plugin elements (don't use style tag, just directly put custom css code) </p>

 <textarea name="guaven_woos_custom_css" id="guaven_woos_custom_css" class="large-text code" rows="5"><?php
echo get_option("guaven_woos_custom_css");
?></textarea>

<br>
Main css classes: <br>
<code>.guaven_woos_suggestion -  Main container class of suggestion area<br>
  .guaven_woos_selected - Selected suggestion item class.<br>
  .guaven_woos_div - Suggestion item container class<br>
  .guaven_woos_img - Suggestion item image class<br>
  .guaven_wooclass small - Suggestion item price class<br>
  .guaven_woos_thereissale - Suggestion item sale price class<br>
  .guaven_woos_suggestion_list - Suggestion list li element  <br>
  .guaven_woos_suggestion_unlisted - Suggestion list ul element  <br>
  .guaven_woos_pinnedtitle - Featured products block title <br>
  .guaven_woos_hidden_description - Product description div class, which is hidden by default <br>
  .guaven_woos_suggestion_populars - Popular products block ul element
</code>



</td> </tr>







<tr valign="top">
<th scope="row" class="titledesc">Custom parameter for search</th>
<td scope="row">
<p>
<label>
Comma separated names of meta_keys you want to include to search
<input name="guaven_woos_customfields" type="text" id="guaven_woos_customfields"
value='<?php
echo get_option("guaven_woos_customfields");
?>' class="small-text" style="width:500px"
placeholder='F.e: _wc_average_rating,_stock_status etc.'>
</label>
</p>
<small>If you enter here some meta key fields, the search suggestion algorith will include their data to search metada.
(f.e. you have book store, you add _book_author field here. And then when visitor types the name of author in search box, his/her
books will be suggested with normal title. )</small>

</td></tr>

<tr valign="top">
<th scope="row" class="titledesc">Exclude categories</th>
<td scope="row">
<p>
<label>
Comma separated IDs of product categories which should be expluded from the search
<input name="guaven_woos_excluded_cats" type="text" id="guaven_woos_excluded_cats"
value='<?php
echo get_option("guaven_woos_excluded_cats");
?>' class="small-text" style="width:300px"
placeholder=''>
</label>
</p>

</td></tr>





<tr valign="top">
<th scope="row" class="titledesc">Search by tags and attributes</th>
<td scope="row">

<p>

<label>
Type comma separated names of tags and attributes

<input name="guaven_woos_customtags" type="text" id="guaven_woos_customtags"
value='<?php
echo get_option("guaven_woos_customtags");
?>' class="small-text" style="width:500px"
placeholder='F.e: product_tag,product_vendor etc.'>
</label>
</p>
<small>For example default product tag name is product_tag. To get exact names of any attribute just hover cursor on their name,
you will see taxonomy=pa_color like string there. pa_color is exact name of that attribute. If any questions, just write to our support</small>

</td></tr>




<tr valign="top">
<th scope="row" class="titledesc">Synonym list </th>
<td scope="row">
<p>Put your product related synonyms there. Our search algorythm will take it into account.  </p>

 <textarea name="guaven_woos_synonyms" id="guaven_woos_synonyms" class="large-text code" rows="2"><?php
echo stripslashes(get_option("guaven_woos_synonyms"));
?></textarea>
<br /><code>Each pair should be in A-B format, comma separated. For example: car-auto, lbs-pound, footgear-shoes</code>
</td> </tr>




<tr valign="top">
<th scope="row" class="titledesc">Narrowed Search  </th>
<td scope="row">

<p>
<label>
<input name="guaven_woos_exactmatch" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_exactmatch") != '') {
echo 'checked="checked"';
}
?>>Exact match search
</label>
</p>
<small>If you enable this feature, then the algortyhm will search exact match among title,tags,attrbutes etc.. F.e. If the visitor types
  phone, it will only display the products which have indepentent "phone" string in their content.
</small>



<p><br>
<label>
<input name="guaven_woos_large_data" type="checkbox" value="1" class="tog" <?php
if (get_option("guaven_woos_large_data") != '') {
echo 'checked="checked"';
}
?>>Enable "first letter rule"
</label>
</p>
<small>If you enable this feature, then it will work so: when user types f.e. Galaxy, it will  search in products which names' start with "G",
  so it will find only the products which starts with Galaxy,
  the products which start with "Samsung Galaxy"
will not be displayed.
</small>

</td></tr>


</tbody> </table>

<p>
<input type="submit" class="button button-primary" value="Save settings">
</p>

</form>



<form action="" method="post" name="reset_form">


<?php
wp_nonce_field('guaven_woos_reset_nonce', 'guaven_woos_reset_nonce_f');
?>


<p>
<br>
<input type="submit" onclick="return confirm('Are you sure to reset all settings to default?')" class="button button-default" value="Reset all settings to default">
</p>
</form>


</div>
