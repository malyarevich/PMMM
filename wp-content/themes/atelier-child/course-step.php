<?php
    if (! session_id())
    session_start ();
//echo $_SESSION['course_step'];
    get_header();?>
    <div class="container">
        <div class="row">
            <?php if ( post_password_required() ){?>
                <div class="col-md-12">
                    <div class="page-password">
                        <?php echo get_the_password_form();?>
                    </div>
                </div>
            <?php }else{ ?>
                <div class="col-md-12">
                <div class="progress-bar">
                    <div class="progress-box">
                        <div class="<?php if($_SESSION['course_step']>=1.1) echo 'session-complete'; else echo 'session-incomplete'; ?>"></div>
                        <p>Kurs 1 <br> &nbsp;</p></div>
                    <div class="progress-box-quiz">
                        <div class="<?php if($_SESSION['course_step']>3 || $_SESSION['course_step']==1.1) echo 'quiz-complete'; else echo 'quiz-incomplete'; ?>"></div>
                        <p>Quiz 1 <br> &nbsp;</p></div>
                    <div class="progress-box">
                        <div class="<?php if($_SESSION['course_step']>=4.1) echo 'session-complete'; else echo 'session-incomplete'; ?>"></div>
                        <p>Kurs 2 <br> &nbsp;</p></div>
                    <div class="progress-box-quiz">
                        <div class="<?php if($_SESSION['course_step']>6 || $_SESSION['course_step']==4.1) echo 'quiz-complete'; else echo 'quiz-incomplete'; ?>"></div>
                        <p>Quiz 2 <br> &nbsp;</p></div>
                    <div class="progress-box">
                        <div class="<?php if($_SESSION['course_step']>=7.1) echo 'session-complete'; else echo 'session-incomplete'; ?>"></div>
                        <p>Kurs 3 <br> &nbsp;</p></div>
                    <div class="progress-box-quiz">
                        <div class="<?php if($_SESSION['course_step']>9 || $_SESSION['course_step']==7.1) echo 'quiz-complete'; else echo 'quiz-incomplete'; ?>"></div>
                        <p>Quiz 3 <br> &nbsp;</p></div>
                    <div class="progress-box">
                        <div class="<?php if($_SESSION['course_step']>=10.1) echo 'session-complete'; else echo 'session-incomplete'; ?>"></div>
                        <p>Kurs 4 <br> &nbsp;</p></div>
                    <div class="progress-box-quiz">
                        <div class="<?php if($_SESSION['course_step']>12 || $_SESSION['course_step']==10.1) echo 'quiz-complete'; else echo 'quiz-incomplete'; ?>"></div>
                        <p>Quiz 4 <br> &nbsp;</p></div>
                    <div class="progress-box">
                        <div class="<?php if($_SESSION['course_step']>=13.1) echo 'session-complete'; else echo 'session-incomplete'; ?>"></div>
                        <p>Kurs 5 <br> &nbsp;</p></div>
                    <div class="progress-box-quiz">
                        <div class="<?php if($_SESSION['course_step']>15 || $_SESSION['course_step']==13.1) echo 'quiz-complete'; else echo 'quiz-incomplete'; ?>"></div>
                        <p>Quiz 5 <br> &nbsp;</p></div>
                    <div class="progress-box">
                        <div class="<?php if($_SESSION['course_step']>=16.1) echo 'session-complete'; else echo 'session-incomplete'; ?>"></div>
                        <p>Kurs 6 <br> &nbsp;</p></div>
                    <div class="progress-box-quiz">
                        <div class="<?php if($_SESSION['course_step']>18 || $_SESSION['course_step']==16.1) echo 'quiz-complete'; else echo 'quiz-incomplete'; ?>"></div>
                        <p>Quiz 6 <br> &nbsp;</p></div>
                </div>
                <?php
//                if(isset($_POST['sendStep']))
//                    $_SESSION['course_step']=$_POST['sendStep'];
//                    $user  = get_user_by('email', $_SESSION['user_course_mail']);
//                    update_user_meta($user->id, "course_step", $_SESSION['course_step']);
                    switch ($_SESSION['course_step']) {
                        case 1:
                            get_template_part( 'template-parts/page', 'course-step1' );
                            break;
                        case 1.1:
                            get_template_part( 'template-parts/page', 'course-step1' );
                            break;
                        case 2:
                            get_template_part( 'template-parts/page', 'course-step2' );
                            break;
                        case 3:
                            get_template_part( 'template-parts/page', 'course-step3' );
                            break;
                        case 3.1:
                            get_template_part( 'template-parts/page', 'course-step3' );
                            break;
                        case 4:
                            get_template_part( 'template-parts/page', 'course-step1' );
                            break;
                        case 4.1:
                            get_template_part( 'template-parts/page', 'course-step1' );
                            break;
                        case 5:
                            get_template_part( 'template-parts/page', 'course-step2' );
                            break;
                        case 6:
                            get_template_part( 'template-parts/page', 'course-step3' );
                            break;
                        case 6.1:
                            get_template_part( 'template-parts/page', 'course-step3' );
                            break;
                        case 7:
                            get_template_part( 'template-parts/page', 'course-step1' );
                            break;
                        case 7.1:
                            get_template_part( 'template-parts/page', 'course-step1' );
                            break;
                        case 8:
                            get_template_part( 'template-parts/page', 'course-step2' );
                            break;
                        case 9:
                            get_template_part( 'template-parts/page', 'course-step3' );
                            break;
                        case 9.1:
                            get_template_part( 'template-parts/page', 'course-step3' );
                            break;
                        case 10:
                            get_template_part( 'template-parts/page', 'course-step1' );
                            break;
                        case 10.1:
                            get_template_part( 'template-parts/page', 'course-step1' );
                            break;
                        case 11:
                            get_template_part( 'template-parts/page', 'course-step2' );
                            break;
                        case 12:
                            get_template_part( 'template-parts/page', 'course-step3' );
                            break;
                        case 12.1:
                            get_template_part( 'template-parts/page', 'course-step3' );
                            break;
                        case 13:
                            get_template_part( 'template-parts/page', 'course-step1' );
                            break;
                        case 13.1:
                            get_template_part( 'template-parts/page', 'course-step1' );
                            break;
                        case 14:
                            get_template_part( 'template-parts/page', 'course-step2' );
                            break;
                        case 15:
                            get_template_part( 'template-parts/page', 'course-step3' );
                            break;
                        case 15.1:
                            get_template_part( 'template-parts/page', 'course-step3' );
                            break;
                        case 16:
                            get_template_part( 'template-parts/page', 'course-step1' );
                            break;
                        case 16.1:
                            get_template_part( 'template-parts/page', 'course-step1' );
                            break;
                        case 17:
                            get_template_part( 'template-parts/page', 'course-step2' );
                            break;
                        case 18:
                            get_template_part( 'template-parts/page', 'course-step3' );
                            break;
                        case 18.1:
                            get_template_part( 'template-parts/page', 'course-step3' );
                        break;
                        case 19:
                            get_template_part( 'template-parts/page', 'course-document' );
                            break;
                    }
                ?>
        </div>
            <?php }?>
    </div>
</div>
<?php get_footer(); ?>
