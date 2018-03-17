<?PHP
session_start();

 if (isset($_SESSION['login']['user']) ){
    require 'configure.php';

    $userId  = $_SESSION['login']['user'];

    $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME );

    if ($db_found) {

        $vraag = $_POST['vraag'];
        $antwoord = $_POST['antwoord'];

        $SQL = $db_found->prepare('insert into cl_antwoord (vraag_id, gebruiker_id, antwoord ) '.
            'values(?, ?, ?)');

        $SQL->bind_param('iis', $vraag, $userId, $antwoord);
        echo $SQL->execute();
    } else {
        echo 'Geen db';
    }
 }
?>
