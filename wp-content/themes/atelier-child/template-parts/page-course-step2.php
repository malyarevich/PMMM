<?php
switch ((int)$_SESSION['course_step']) {
    case 2: {
        echo do_shortcode('[spb_gravityforms grav_form="13" show_title="true" show_desc="true" ajax="false" width="1/1" el_position="first last"]');
        echo '<button data-step="1" class="back-quiz-step back-quiz-step-custom quiz-step">< Back </button>';
        break;
    }
    case 5: {
        echo do_shortcode('[spb_gravityforms grav_form="14" show_title="true" show_desc="true" ajax="false" width="1/1" el_position="first last"]');
        echo '<button data-step="4" class="back-quiz-step back-quiz-step-custom quiz-step">< Back </button>';
        break;
    }
    case 8: {
        echo do_shortcode('[spb_gravityforms grav_form="15" show_title="true" show_desc="true" ajax="false" width="1/1" el_position="first last"]');
        echo '<button data-step="7" class="back-quiz-step back-quiz-step-custom quiz-step">< Back </button>';
        break;
    }
    case 11: {
        echo do_shortcode('[spb_gravityforms grav_form="16" show_title="true" show_desc="true" ajax="false" width="1/1" el_position="first last"]');
        echo '<button data-step="10" class="back-quiz-step back-quiz-step-custom quiz-step">< Back </button>';
        break;
    }
    case 14: {
        echo do_shortcode('[spb_gravityforms grav_form="17" show_title="true" show_desc="true" ajax="false" width="1/1" el_position="first last"]');
        echo '<button data-step="13" class="back-quiz-step back-quiz-step-custom quiz-step">< Back </button>';
        break;
    }
    case 17: {
        echo do_shortcode('[spb_gravityforms grav_form="18" show_title="true" show_desc="true" ajax="false" width="1/1" el_position="first last"]');
        echo '<button data-step="16" class="back-quiz-step back-quiz-step-custom quiz-step">< Back </button>';
        break;
    }
}
?>