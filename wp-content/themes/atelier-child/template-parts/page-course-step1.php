<?php
if(is_float($_SESSION['course_step']+0))
    $step = $_SESSION['course_step']+2;
else
    $step = $_SESSION['course_step']+1;

switch ((int)$_SESSION['course_step']) {
    case 1:{
        echo '<h1 class="quiz_header">Session 1 – Introduction to South African Wine</h1>';
        echo '<h4>Download Powerpoint session or view session slides below. If ready, please click link on the right to begin Quiz.</h4>';
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Begin Session1 Quiz > </button>';
        echo do_shortcode('[real3dflipbook id="1"]');
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Begin Session1 Quiz > </button>';
        break;
    }
    case 4:{
        echo '<h1 class="quiz_header">Session 2 – Introduction to South African Wine</h1>';
        echo '<h4>Download Powerpoint session or view session slides below. If ready, please click link on the right to begin Quiz.</h4>';
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Begin Session2 Quiz > </button>';
        echo do_shortcode('[real3dflipbook id="2"]');
        echo '<button data-step="'.$step.'"class="next-quiz-step quiz-step">Begin Session2 Quiz > </button>';
        break;
    }
    case 7:{
        echo '<h1 class="quiz_header">Session 3 – Red Wine Styles</h1>';
        echo '<h4>Download Powerpoint session or view session slides below. If ready, please click link on the right to begin Quiz.</h4>';
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Begin Session3 Quiz > </button>';
        echo do_shortcode('[real3dflipbook id="3"]');
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Begin Session3 Quiz > </button>';
        break;
    }
    case 10:{
        echo '<h1 class="quiz_header">Session 4 – South African Wine Regions Influenced by the Coast</h1>';
        echo '<h4>Download Powerpoint session or view session slides below. If ready, please click link on the right to begin Quiz.</h4>';
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Begin Session4 Quiz > </button>';
        echo do_shortcode('[real3dflipbook id="4"]');
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Begin Session4 Quiz > </button>';
        break;
    }
    case 13:{
        echo '<h1 class="quiz_header">Session 5 – South African Inland Wine Regions</h1>';
        echo '<h4>Download Powerpoint session or view session slides below. If ready, please click link on the right to begin Quiz.</h4>';
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Begin Session5 Quiz > </button>';
        echo do_shortcode('[real3dflipbook id="5"]');
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Begin Session5 Quiz > </button>';
        break;
    }
    case 16:{
        echo '<h1 class="quiz_header">Session 6 – Other Factors Affecting Production and Wines in South Africa</h1>';
        echo '<h4>Download Powerpoint session or view session slides below. If ready, please click link on the right to begin Quiz.</h4>';
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Begin Session6 Quiz > </button>';
        echo do_shortcode('[real3dflipbook id="6"]');
        echo '<button data-step="'.$step.'" class="next-quiz-step quiz-step">Begin Session6 Quiz > </button>';
        break;
    }
}
?>