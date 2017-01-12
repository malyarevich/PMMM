<?php

use bis\repf\model\PageRulesEngineModel;

/**
 * Class RulesDynamicContent
 */
class RulesDynamicContent
{


    /**
     * This method is used to get the dynamic content based on the defined rule.
     * @param $append_to_post
     * @param null $title
     * @return string
     */
    public static function get_dynamic_content($append_to_post, $title = null) {

        $dc_body = $append_to_post->gencol1;
        $dc_type = $append_to_post->action;
        $image = null;

        if(RulesEngineUtil::isContains($dc_type, "post")) {
             $image_attr = json_decode($append_to_post->gencol3);
        } else {
             $image_attr = json_decode($append_to_post->gencol4);
        }

        if ($image_attr != null) {

            $post_id = $image_attr->image_id;
            $image_size = $image_attr->image_size;

            $page_model = new PageRulesEngineModel();
            $image = $page_model->get_image_from_media_library($post_id, $image_size);

        }

        if (RulesEngineUtil::isContains($dc_type, "hide") ) {

            $dynamic_content = $dynamic_content = '<div class="alert alert-danger" role="alert">
                    ' . stripslashes($dc_body) . '</div>';

        } elseif (RulesEngineUtil::isContains($dc_type, "replace")) {

              $dynamic_content = stripslashes($dc_body);

        } elseif($dc_type === "append_image_page" || $dc_type === "append_image_post") {

            $dynamic_content = '<img src="' . $image->get_url() . '" />';

        } elseif ($dc_type === "append_image_bg_page" || $dc_type === "append_image_bg_post") {

            $dynamic_content = '<div style="background:url(' . $image->get_url() . ');background-repeat: no-repeat;">
                                        <div class="panel-body">' . stripslashes($dc_body) . '</div>
                                </div>';
        } else {

            if($title != null) {

                $dynamic_content = '<div class="panel panel-default"><div class="panel-heading">'.$title.'</div>
                <div class="panel-body">' . stripslashes($dc_body) . '</div></div>';

            } else {

                $dynamic_content = '<div>' . stripslashes($dc_body) . '</div>';
            }

        }

        return $dynamic_content;

    }
    /**
     * This method generates the dynamic content.
     *
     * @param $title
     * @param $body
     * @return string
     */
    public static function get_content($append_to_post, $content)
    {

        $modal_content = null;



        $dynamic_content = RulesDynamicContent::get_dynamic_content($append_to_post);
        
        $dc_type = $append_to_post->action;
        
        // Replace page content
        if(RulesEngineUtil::isContains($dc_type, "replace")) {
            $content = $dynamic_content;
        }   else if (($dc_type !== "hide_post" && $dc_type !== "hide_page" && $dc_type !== "soft_page_hide")) { // Append content options

            $cposition = json_decode($append_to_post->gencol2)->content_position;
            switch($cposition) {

                case "pos_dialog_page":
                case "pos_dialog_post":
                    $modal_content = RulesDynamicContent::get_model_dialog($append_to_post);
                    $content = $content . $modal_content;
                    break;

                case "pos_bottom_page":
                case "pos_bottom_post":
                    $content = $content . $dynamic_content;
                    break;

                case "pos_top_page":
                case "pos_top_post":
                    $content = $dynamic_content . $content;
                    break;
            }

        }

        return $content;
    }

    /**
     * This method is used for creating modal dialogs
     *
     * @param $append_to_post
     * @return string
     */
    private static function get_model_dialog($append_to_post) {
        $model_title = null;
        $model_body = $append_to_post->gencol1;
        $dc_type = $append_to_post->action;
        $model_span = '<span class="img-span"></span>';

        if ($dc_type === "append_image_page" || $dc_type === "append_image_post" ||
            $dc_type === "append_image_bg_page" || $dc_type === "append_image_bg_post") {

            // Gencol 3 contains image attr details for post and Gencol4 for page.
            if(RulesEngineUtil::isContains($dc_type, "post")) {
                $image_attr = json_decode($append_to_post->gencol3);
            } else {
                $image_attr = json_decode($append_to_post->gencol4);
            }

            $image_id = $image_attr->image_id;
            $image_size = $image_attr->image_size;

            $page_model = new PageRulesEngineModel();
            $image = $page_model->get_image_from_media_library($image_id, $image_size);
            if ($dc_type === "append_image_page" || $dc_type === "append_image_post") {

                $model_dialog = '<script>
                    jQuery(function() {
                        bis_re_showImageModalDialog("'.$image->get_url().'");
                    });
                    </script>';

            } else { // Content and image
                $model_body = '<div style="display:none">
			                <div id="bis_inline_content" style="padding:10px;background:url(' . $image->get_url() . ');">'
                    .stripslashes($model_body).'
                            </div>
                           </div>';
                $model_dialog = '<script>
                    jQuery(function() {
                        bis_re_showModalDialog("bis_inline_content", "30%");
                    });
                    </script>';
                $model_dialog =  $model_body.$model_dialog;
            }

        } else { // only content

            $model_body = '<div style="display:none">
			                <div id="bis_inline_content" style="padding:10px; background:#fff;">'
                                .stripslashes($model_body).'
                            </div>
                           </div>';
            $model_dialog = '<script>
                    jQuery(function() {
                        bis_re_showModalDialog("bis_inline_content", "30%");
                    });
                    </script>';
            $model_dialog =  $model_body.$model_dialog;
        }

        return $model_span.$model_dialog;
    }

    /**
     * This method return constucted bootstrap model dialog.
     *
     * @param $model_title
     * @param $model_body
     * @return mixed
     */
    private static function get_model_dialog1($append_to_post)
    {

        $model_title = null;
        $model_body = $append_to_post->gencol1;
        $dc_type = $append_to_post->action;

        if ($dc_type === "append_image_page" || $dc_type === "append_image_post" ||
            $dc_type === "append_image_bg_page" || $dc_type === "append_image_bg_post") {

            // Gencol 3 contains image attr details for post and Gencol4 for page.
            if(RulesEngineUtil::isContains($dc_type, "post")) {
                $image_attr = json_decode($append_to_post->gencol3);
            } else {
                $image_attr = json_decode($append_to_post->gencol4);
            }

            $image_id = $image_attr->image_id;
            $image_size = $image_attr->image_size;

            $page_model = new PageRulesEngineModel();
            $image = $page_model->get_image_from_media_library($image_id, $image_size);

            if ($dc_type === "append_image_page" || $dc_type === "append_image_post") {
                $model_dialog =
                    '<div id="bis_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
                            <div class="modal-dialog" >
                                <div class="modal-content" id="bis_modal_bg_image">
                                        <img src="' . $image->get_url() . '" />
                                </div>
                                 <span title="Close" style="cursor: pointer"
                                        class="bis-icon-modal-close glyphicon glyphicon-remove-circle pull-right" data-dismiss="modal">
                                 </span>
                            </div>
                        </div><!-- /.modal -->';

            } else { // Content and image

                $model_dialog =
                    '<div id="bis_modal" class="modal fade" tabindex="-1" role="dialog"
                            aria-labelledby="viewModalLabel" aria-hidden="true">
                            <div class="modal-dialog" >
                                 <div class="modal-content" id="bis_modal_bg_image"
                                 style="background:url(' . $image->get_url() . ');">
                                    <div class="modal-body">
                                        ' . stripslashes($model_body) . '
                                    </div>
                                </div>
                                 <span title="Close" style="cursor: pointer"
                                        class="bis-icon-modal-close glyphicon glyphicon-remove-circle pull-right" data-dismiss="modal">
                                 </span>
                            </div>
                        </div><!-- /.modal -->';
            }

        } else { // only content
            $model_dialog =
                '<div id="bis_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content" id="bis_modal_bg_image">
                                    <div class="modal-body">
                                        ' . stripslashes($model_body) . '
                                    </div>
                                    <span title="Close" style="cursor: pointer"
                                        class="bis-icon-modal-close glyphicon glyphicon-remove-circle pull-right" data-dismiss="modal">
                                    </span>
                                </div>
                            </div>
                        </div><!-- /.modal -->';

        }

        return $model_dialog;
    }


}

?>