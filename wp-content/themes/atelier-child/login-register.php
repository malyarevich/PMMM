<?php
if (! session_id())
    session_start ();
    $_SESSION['course_step']=0;
    get_header(); ?>
<?php
    get_template_part( 'template-parts/page', 'reg-login' );
?>
<?php get_footer(); ?>
