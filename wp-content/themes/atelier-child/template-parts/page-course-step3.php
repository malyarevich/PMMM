<?php
    switch ($_SESSION['course_step']) {
        case 3: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 1</h1>';
            echo '<h4>Welcome to the Wines of South Africa Wine Course Quiz. This quiz consists of 10 multiple choice questions. Please answer the questions carefully. There is no time limit.</h4>';
            echo '<h4 class="quiz_rezult">CONGRATULATIONS! YOU HAVE SUCCESSFULLY COMPLETED THE QUIZ WITH A SCORE OF:'. $_SESSION['course_score'].'/ 10 ('. $_SESSION['course_percent'].'.00%). CLICK NEXT TO ADVANCE.</h4>';
            echo '<button data-step="1.1" class="back-quiz-step quiz-step">< Back </button>';
            echo '<button data-step="4" class="next-quiz-step quiz-step">Next ></button>';
            break;
        }
        case 3.1: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 1</h1>';
            echo '<h4>Welcome to the Wines of South Africa Wine Course Quiz. This quiz consists of 10 multiple choice questions. Please answer the questions carefully. There is no time limit.</h4>';
            echo '<h4 class="quiz_rezult">YOU HAVE ALREADY SUCCESSFULLY COMPLETED THIS TEST. CLICK NEXT TO ADVANCE.</h4>';
            echo '<button data-step="1.1" class="back-quiz-step quiz-step">< Back </button>';
            echo '<button data-step="4" class="next-quiz-step quiz-step">Next ></button>';
            break;
        }
        case 6: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 2</h1>';
            echo '<h4>Welcome to the Wines of South Africa Wine Course Quiz. This quiz consists of 10 multiple choice questions. Please answer the questions carefully. There is no time limit.</h4>';
            echo '<h4 class="quiz_rezult">CONGRATULATIONS! YOU HAVE SUCCESSFULLY COMPLETED THE QUIZ WITH A SCORE OF:'. $_SESSION['course_score'].'/ 10 ('. $_SESSION['course_percent'].'.00%). CLICK NEXT TO ADVANCE.</h4>';
            echo '<button data-step="4.1" class="back-quiz-step quiz-step">< Back </button>';
            echo '<button data-step="7" class="next-quiz-step quiz-step">Next ></button>';
            break;
        }
        case 6.1: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 2</h1>';
            echo '<h4>Welcome to the Wines of South Africa Wine Course Quiz. This quiz consists of 10 multiple choice questions. Please answer the questions carefully. There is no time limit.</h4>';
            echo '<h4 class="quiz_rezult">YOU HAVE ALREADY SUCCESSFULLY COMPLETED THIS TEST. CLICK NEXT TO ADVANCE.</h4>';
            echo '<button data-step="4.1" class="back-quiz-step quiz-step">< Back </button>';
            echo '<button data-step="7" class="next-quiz-step quiz-step">Next ></button>';
            break;
        }
        case 9: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 3</h1>';
            echo '<h4>Welcome to the Wines of South Africa Wine Course Quiz. This quiz consists of 10 multiple choice questions. Please answer the questions carefully. There is no time limit.</h4>';
            echo '<h4 class="quiz_rezult">CONGRATULATIONS! YOU HAVE SUCCESSFULLY COMPLETED THE QUIZ WITH A SCORE OF:'. $_SESSION['course_score'].'/ 10 ('. $_SESSION['course_percent'].'.00%). CLICK NEXT TO ADVANCE.</h4>';
            echo '<button data-step="7.1" class="back-quiz-step quiz-step">< Back </button>';
            echo '<button data-step="10" class="next-quiz-step quiz-step">Next ></button>';
            break;
        }
        case 9.1: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 3</h1>';
            echo '<h4>Welcome to the Wines of South Africa Wine Course Quiz. This quiz consists of 10 multiple choice questions. Please answer the questions carefully. There is no time limit.</h4>';
            echo '<h4 class="quiz_rezult">YOU HAVE ALREADY SUCCESSFULLY COMPLETED THIS TEST. CLICK NEXT TO ADVANCE.</h4>';
            echo '<button data-step="7.1" class="back-quiz-step quiz-step">< Back </button>';
            echo '<button data-step="10" class="next-quiz-step quiz-step">Next ></button>';
            break;
        }


        case 12: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 4</h1>';
            echo '<h4>Welcome to the Wines of South Africa Wine Course Quiz. This quiz consists of 10 multiple choice questions. Please answer the questions carefully. There is no time limit.</h4>';
            echo '<h4 class="quiz_rezult">CONGRATULATIONS! YOU HAVE SUCCESSFULLY COMPLETED THE QUIZ WITH A SCORE OF:'. $_SESSION['course_score'].'/ 10 ('. $_SESSION['course_percent'].'.00%). CLICK NEXT TO ADVANCE.</h4>';
            echo '<button data-step="10.1" class="back-quiz-step quiz-step">< Back </button>';
            echo '<button data-step="13" class="next-quiz-step quiz-step">Next ></button>';
            break;
        }
        case 12.1: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 4</h1>';
            echo '<h4>Welcome to the Wines of South Africa Wine Course Quiz. This quiz consists of 10 multiple choice questions. Please answer the questions carefully. There is no time limit.</h4>';
            echo '<h4 class="quiz_rezult">YOU HAVE ALREADY SUCCESSFULLY COMPLETED THIS TEST. CLICK NEXT TO ADVANCE.</h4>';
            echo '<button data-step="10.1" class="back-quiz-step quiz-step">< Back </button>';
            echo '<button data-step="13" class="next-quiz-step quiz-step">Next ></button>';
            break;
        }
        case 15: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 5</h1>';
            echo '<h4>Welcome to the Wines of South Africa Wine Course Quiz. This quiz consists of 10 multiple choice questions. Please answer the questions carefully. There is no time limit.</h4>';
            echo '<h4 class="quiz_rezult">CONGRATULATIONS! YOU HAVE SUCCESSFULLY COMPLETED THE QUIZ WITH A SCORE OF:'. $_SESSION['course_score'].'/ 10 ('. $_SESSION['course_percent'].'.00%). CLICK NEXT TO ADVANCE.</h4>';
            echo '<button data-step="13.1" class="back-quiz-step quiz-step">< Back </button>';
            echo '<button data-step="16" class="next-quiz-step quiz-step">Next ></button>';
            break;
        }
        case 15.1: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 5</h1>';
            echo '<h4>Welcome to the Wines of South Africa Wine Course Quiz. This quiz consists of 10 multiple choice questions. Please answer the questions carefully. There is no time limit.</h4>';
            echo '<h4 class="quiz_rezult">YOU HAVE ALREADY SUCCESSFULLY COMPLETED THIS TEST. CLICK NEXT TO ADVANCE.</h4>';
            echo '<button data-step="13.1" class="back-quiz-step quiz-step">< Back </button>';
            echo '<button data-step="16" class="next-quiz-step quiz-step">Next ></button>';
            break;
        }


        case 18: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 6</h1>';
            echo '<h4>Welcome to the Wines of South Africa Wine Course Quiz. This quiz consists of 10 multiple choice questions. Please answer the questions carefully. There is no time limit.</h4>';
            echo '<h4 class="quiz_rezult">CONGRATULATIONS! YOU HAVE SUCCESSFULLY COMPLETED THE QUIZ WITH A SCORE OF:'. $_SESSION['course_score'].'/ 10 ('. $_SESSION['course_percent'].'.00%). CLICK NEXT TO ADVANCE.</h4>';
            echo '<button data-step="16.1" class="back-quiz-step quiz-step">< Back </button>';
            echo '<button data-step="19" class="next-quiz-step quiz-step">Next ></button>';
            break;
        }
        case 18.1: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 6</h1>';
            echo '<h4>Welcome to the Wines of South Africa Wine Course Quiz. This quiz consists of 10 multiple choice questions. Please answer the questions carefully. There is no time limit.</h4>';
            echo '<h4 class="quiz_rezult">YOU HAVE ALREADY SUCCESSFULLY COMPLETED THIS TEST. CLICK NEXT TO ADVANCE.</h4>';
            echo '<button data-step="16.1" class="back-quiz-step quiz-step">< Back </button>';
            echo '<button data-step="19" class="next-quiz-step quiz-step">Next ></button>';
            break;
        }
    }

?>