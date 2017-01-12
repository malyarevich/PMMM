<div class='wrap'>
    <div id='real3dflipbook-admin'>
        <a href="admin.php?page=real3d_flipbook_admin" class="back-to-list-link">&larr; 
            <?php _e('Back to flipbooks list', 'flipbook'); ?>
        </a>
        <h1 id="edit-flipbook-text"></h1>
        <form method="post" enctype="multipart/form-data" action="admin.php?page=real3d_flipbook_admin&action=save_settings&bookId=<?php echo($current_id);?>">
            <p class="submit">
                <!-- <span class="unsaved" sytle="display:none;">Unsaved  </span> -->
                <input type="submit" name="submit" id="submit" class="button save-button button-primary" value="Save Changes">
                <a href="#TB_inline?width=600&height=550&inlineId=flipbook-preview-container" class="thickbox flipbook-preview button save-button button-secondary">Preview Flipbook</a>
            </p>
            <div class="tabs">
                <ul>
                    <li>
                        <a href="#tab-pages">
                            <h4>Pages</h4>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-general">
                            <h4>General</h4>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-lightbox">
                            <h4>Lightbox</h4>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-menu">
                            <h4>Menu</h4>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-webgl">
                            <h4>WebGL</h4>
                        </a>
                    </li>
                    <li>
                        <a href="#tab-mobile">
                            <h4>Mobile</h4>
                        </a>
                    </li>
                    <li>
                </ul>
                <div id="tab-pages">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable" style="">

                        <h2>Select flipbook type</h2>
                        <p>
                            <label>
                                <input id="flipbook-type-pdf" name="type" type="radio" value="pdf"> PDF     
                            </label>
                        </p>
                        <p>
                            <label>
                                <input id="flipbook-type-jpg" name="type" type="radio" value="jpg"> JPG     
                            </label>
                        </p>
                        <div class="clear" />
                        <div id="select-pdf">
                            <h2></h2>
                            <table class="form-table" id="flipbook-pdf-options">
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="add-jpg-pages" style="display:none;">
                            <h2><p><a class="page-title-action add-pages-button" href="#">Add pages</a></p><!-- <p>
                            <a class="page-title-action select-pdf-button" href="#">Convert PDF to JPG-s</a></p> --></h2>
                            <p class="creating-page"></p>
                            <div class="ui-sortable sortable-pages-body">
                                <div id="pages-container" class="ui-sortable sortable-pages-container"></div>
                            </div>
                        </div>
                    </div>
                    <div id="convert-pdf" style="display:none;">PDF
                        <a href="#" class="add-new-h2 select-pdf-button">Select</a>
                    </div>
                </div>
                </div>
                <div id="tab-general">
                    <table class="form-table" id="flipbook-general-options">
                        <tbody/>
                    </table>
                </div>
                <div id="tab-normal">
                    <table class="form-table" id="flipbook-normal-options">
                        <tbody/>
                    </table>
                </div>
                <div id="tab-mobile">
                    <table class="form-table" id="flipbook-mobile-options">
                        <tbody/>
                    </table>
                </div>
                <div id="tab-lightbox">
                    <table class="form-table" id="flipbook-lightbox-options">
                        <tbody/>
                    </table>
                </div>
                <div id="tab-webgl">
                    <table class="form-table" id="flipbook-webgl-options">
                        <tbody/>
                    </table>
                </div>
                <div id="tab-menu">
                    <div class="metabox-holder">
                        <div class="meta-box-sortables">
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Current page</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-currentPage-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button next page</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnNext-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button last page</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnLast-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button previous page</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnPrev-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button first page</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnFirst-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button zoom in</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnZoomIn-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button zoom out</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnZoomOut-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button table of content</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnToc-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button thumbnails</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnThumbs-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button share</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnShare-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button download pages</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnDownloadPages-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button download PDF</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnDownloadPdf-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button sound</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnSound-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button expand</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnExpand-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button expand lightbox</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnExpandLightbox-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Button print</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-btnPrint-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Share on Google plus</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-google_plus-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Share on Twitter</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-twitter-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Share on Facebook</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-facebook-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Share on pinterest</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-pinterest-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                            <div class="postbox closed">
                                <div class="handlediv" title="Click to toggle"></div>
                                <h3 class="hndle">Share by email</h3>
                                <div class="inside">
                                    <table class="form-table" id="flipbook-email-options">
                                        <tbody/>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="flipbook-preview-container" style="display:none">
                <div id="flipbook-preview-container-inner" style="position:relative;height:100%"></div>
            </div>
            <p class="submit">
                <!-- <span class="unsaved" sytle="display:none;">Unsaved  </span> -->
                <input type="submit" name="submit" id="submit" class="button save-button button-primary" value="Save Changes">
                <a href="#TB_inline?width=600&height=550&inlineId=flipbook-preview-container" class="thickbox flipbook-preview button save-button button-secondary">Preview Flipbook</a>
            </p>
        </form>
    </div>
</div>
<?php 
wp_enqueue_media();
add_thickbox(); 
wp_enqueue_script( "real3d_flipbook", plugins_url(). "/real3d-flipbook/js/flipbook.min.js", array( 'jquery'),REAL3D_FLIPBOOK_VERSION); 
wp_enqueue_style( 'flipbook_style', plugins_url(). "/real3d-flipbook/css/flipbook.style.css" , array(),REAL3D_FLIPBOOK_VERSION); 
wp_enqueue_style( 'font_awesome', plugins_url(). "/real3d-flipbook/css/font-awesome.css" , array(),REAL3D_FLIPBOOK_VERSION); 
//wp_enqueue_script( "compatibilityjs", plugins_url(). "/real3d-flipbook/js/compatibility.min.js", array(),REAL3D_FLIPBOOK_VERSION); 
wp_enqueue_script( "pdfjs", plugins_url(). "/real3d-flipbook/js/pdf.min.js", array(),REAL3D_FLIPBOOK_VERSION); 
wp_enqueue_script( "pdfworkerjs", plugins_url(). "/real3d-flipbook/js/pdf.worker.min.js", array(),REAL3D_FLIPBOOK_VERSION); 
wp_enqueue_style( 'wp-color-picker' ); 
wp_enqueue_script( "real3d_flipbook_admin", plugins_url(). "/real3d-flipbook/js/edit_flipbook.js", array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-resizable', 'jquery-ui-selectable', 'jquery-ui-tabs', 'pdfjs', 'wp-color-picker' ),REAL3D_FLIPBOOK_VERSION); 
wp_enqueue_style( 'real3d_flipbook_admin_css', plugins_url(). "/real3d-flipbook/css/flipbook-admin.css",array(), REAL3D_FLIPBOOK_VERSION ); 
wp_enqueue_style( 'jquery-ui-style', plugins_url(). "/real3d-flipbook/css/jquery-ui.css",array(), REAL3D_FLIPBOOK_VERSION ); 
wp_localize_script( 'real3d_flipbook_admin', 'flipbooks', json_encode($flipbooks) );