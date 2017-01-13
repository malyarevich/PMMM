<?php
    switch ($_SESSION['course_step']) {
        case 3: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 1</h1>';
            echo '<h4>Herzlich Willkommen zum Seminar „Weinbau in Südafrika“. Dieser Test besteht aus 10 Multiple-Choice Fragen. Bitte beantworten Sie die Fragen. Es gibt keine Zeitbegrenzung. Viel Spaß!</h4>';
            echo '<h4 class="quiz_rezult">Herzlichen Glückwunsch! Sie haben den Test mit einem Ergebnis von '. $_SESSION['course_score'].'/ 10 ('. $_SESSION['course_percent'].'.00%) erfolgreich abgeschlossen. Klicken Sie nun auf WEITER.</h4>';
            echo '<button data-step="1.1" class="back-quiz-step quiz-step">< zurück </button>';
            echo '<button data-step="4" class="next-quiz-step quiz-step">weiter ></button>';
            break;
        }
        case 3.1: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 1</h1>';
            echo '<h4>Herzlich Willkommen zum Seminar „Weinbau in Südafrika“. Dieser Test besteht aus 10 Multiple-Choice Fragen. Bitte beantworten Sie die Fragen. Es gibt keine Zeitbegrenzung. Viel Spaß!</h4>';
            echo '<h4 class="quiz_rezult">Sie haben diesen Test bereits erfolgreich abgeschlossen. Bitte klicken Sie auf WEITER.</h4>';
            echo '<button data-step="1.1" class="back-quiz-step quiz-step">< zurück </button>';
            echo '<button data-step="4" class="next-quiz-step quiz-step">weiter ></button>';
            break;
        }
        case 6: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 2</h1>';
            echo '<h4>Herzlich Willkommen zum Seminar „Weinbau in Südafrika“. Dieser Test besteht aus 10 Multiple-Choice Fragen. Bitte beantworten Sie die Fragen. Es gibt keine Zeitbegrenzung. Viel Spaß! </h4>';
            echo '<h4 class="quiz_rezult">Herzlichen Glückwunsch! Sie haben den Test mit einem Ergebnis von '. $_SESSION['course_score'].'/ 10 ('. $_SESSION['course_percent'].'.00%) erfolgreich abgeschlossen. Klicken Sie nun auf WEITER.</h4>';
            echo '<button data-step="4.1" class="back-quiz-step quiz-step">< zurück </button>';
            echo '<button data-step="7" class="next-quiz-step quiz-step">weiter ></button>';
            break;
        }
        case 6.1: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 2</h1>';
            echo '<h4>Herzlich Willkommen zum Seminar „Weinbau in Südafrika“. Dieser Test besteht aus 10 Multiple-Choice Fragen. Bitte beantworten Sie die Fragen. Es gibt keine Zeitbegrenzung. Viel Spaß! </h4>';
            echo '<h4 class="quiz_rezult">Sie haben diesen Test bereits erfolgreich abgeschlossen. Bitte klicken Sie auf WEITER.</h4>';
            echo '<button data-step="4.1" class="back-quiz-step quiz-step">< zurück </button>';
            echo '<button data-step="7" class="next-quiz-step quiz-step">weiter ></button>';
            break;
        }
        case 9: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 3</h1>';
            echo '<h4>Herzlich Willkommen zum Seminar „Weinbau in Südafrika“. Dieser Test besteht aus 10 Multiple-Choice Fragen. Bitte beantworten Sie die Fragen. Es gibt keine Zeitbegrenzung. Viel Spaß! </h4>';
            echo '<h4 class="quiz_rezult">Herzlichen Glückwunsch! Sie haben den Test mit einem Ergebnis von '. $_SESSION['course_score'].'/ 10 ('. $_SESSION['course_percent'].'.00%) erfolgreich abgeschlossen. Klicken Sie nun auf WEITER.</h4>';
            echo '<button data-step="7.1" class="back-quiz-step quiz-step">< zurück </button>';
            echo '<button data-step="10" class="next-quiz-step quiz-step">weiter ></button>';
            break;
        }
        case 9.1: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 3</h1>';
            echo '<h4>Herzlich Willkommen zum Seminar „Weinbau in Südafrika“. Dieser Test besteht aus 10 Multiple-Choice Fragen. Bitte beantworten Sie die Fragen. Es gibt keine Zeitbegrenzung. Viel Spaß! </h4>';
            echo '<h4 class="quiz_rezult">Sie haben diesen Test bereits erfolgreich abgeschlossen. Bitte klicken Sie auf WEITER.</h4>';
            echo '<button data-step="7.1" class="back-quiz-step quiz-step">< zurück </button>';
            echo '<button data-step="10" class="next-quiz-step quiz-step">weiter ></button>';
            break;
        }


        case 12: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 4</h1>';
            echo '<h4>Herzlich Willkommen zum Seminar „Weinbau in Südafrika“. Dieser Test besteht aus 10 Multiple-Choice Fragen. Bitte beantworten Sie die Fragen. Es gibt keine Zeitbegrenzung. Viel Spaß! </h4>';
            echo '<h4 class="quiz_rezult">Herzlichen Glückwunsch! Sie haben den Test mit einem Ergebnis von '. $_SESSION['course_score'].'/ 10 ('. $_SESSION['course_percent'].'.00%) erfolgreich abgeschlossen. Klicken Sie nun auf WEITER.</h4>';
            echo '<button data-step="10.1" class="back-quiz-step quiz-step">< zurück </button>';
            echo '<button data-step="13" class="next-quiz-step quiz-step">weiter ></button>';
            break;
        }
        case 12.1: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 4</h1>';
            echo '<h4>Herzlich Willkommen zum Seminar „Weinbau in Südafrika“. Dieser Test besteht aus 10 Multiple-Choice Fragen. Bitte beantworten Sie die Fragen. Es gibt keine Zeitbegrenzung. Viel Spaß! </h4>';
            echo '<h4 class="quiz_rezult">Sie haben diesen Test bereits erfolgreich abgeschlossen. Bitte klicken Sie auf WEITER.</h4>';
            echo '<button data-step="10.1" class="back-quiz-step quiz-step">< zurück </button>';
            echo '<button data-step="13" class="next-quiz-step quiz-step">weiter ></button>';
            break;
        }
        case 15: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 5</h1>';
            echo '<h4>Herzlich Willkommen zum Seminar „Weinbau in Südafrika“. Dieser Test besteht aus 10 Multiple-Choice Fragen. Bitte beantworten Sie die Fragen. Es gibt keine Zeitbegrenzung. Viel Spaß! </h4>';
            echo '<h4 class="quiz_rezult">Herzlichen Glückwunsch! Sie haben den Test mit einem Ergebnis von '. $_SESSION['course_score'].'/ 10 ('. $_SESSION['course_percent'].'.00%) erfolgreich abgeschlossen. Klicken Sie nun auf WEITER.</h4>';
            echo '<button data-step="13.1" class="back-quiz-step quiz-step">< zurück </button>';
            echo '<button data-step="16" class="next-quiz-step quiz-step">weiter ></button>';
            break;
        }
        case 15.1: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 5</h1>';
            echo '<h4>Herzlich Willkommen zum Seminar „Weinbau in Südafrika“. Dieser Test besteht aus 10 Multiple-Choice Fragen. Bitte beantworten Sie die Fragen. Es gibt keine Zeitbegrenzung. Viel Spaß! </h4>';
            echo '<h4 class="quiz_rezult">Sie haben diesen Test bereits erfolgreich abgeschlossen. Bitte klicken Sie auf WEITER.</h4>';
            echo '<button data-step="13.1" class="back-quiz-step quiz-step">< zurück </button>';
            echo '<button data-step="16" class="next-quiz-step quiz-step">weiter ></button>';
            break;
        }


        case 18: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 6</h1>';
            echo '<h4>Herzlich Willkommen zum Seminar „Weinbau in Südafrika“. Dieser Test besteht aus 10 Multiple-Choice Fragen. Bitte beantworten Sie die Fragen. Es gibt keine Zeitbegrenzung. Viel Spaß! </h4>';
            echo '<h4 class="quiz_rezult">Herzlichen Glückwunsch! Sie haben den Test mit einem Ergebnis von '. $_SESSION['course_score'].'/ 10 ('. $_SESSION['course_percent'].'.00%) erfolgreich abgeschlossen. Klicken Sie nun auf WEITER.</h4>';
            echo '<button data-step="16.1" class="back-quiz-step quiz-step">< zurück </button>';
            echo '<button data-step="19" class="next-quiz-step quiz-step">weiter ></button>';
            break;
        }
        case 18.1: {
            echo '<h1 class="quiz_header">WINE QUIZ SESSION 6</h1>';
            echo '<h4>Herzlich Willkommen zum Seminar „Weinbau in Südafrika“. Dieser Test besteht aus 10 Multiple-Choice Fragen. Bitte beantworten Sie die Fragen. Es gibt keine Zeitbegrenzung. Viel Spaß! </h4>';
            echo '<h4 class="quiz_rezult">Sie haben diesen Test bereits erfolgreich abgeschlossen. Bitte klicken Sie auf WEITER.</h4>';
            echo '<button data-step="16.1" class="back-quiz-step quiz-step">< zurück </button>';
            echo '<button data-step="19" class="next-quiz-step quiz-step">weiter ></button>';
            break;
        }
    }

?>