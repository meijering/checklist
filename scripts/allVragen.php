<?PHP
session_start();
$errorMessage = "";


if (isset($_SESSION['login']['user']) ){
    require 'configure.php';

    $userId  = $_SESSION['login']['user'];

    $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME );

    if ($db_found) {
        
        $result = $db_found->query('SELECT * FROM cl_groep  WHERE thema = 1 order by groep_id');

        while($row = $result->fetch_assoc()) {
            $groep_id = $row['groep_id'];
            $groep = $row;
            $SQL = $db_found->prepare('SELECT * FROM cl_vraag where groep_id = ? order by rang');
            $SQL->bind_param('i', $groep_id);
            $SQL->execute();
            $vraag_result = $SQL->get_result();
            while ($vraag_row = $vraag_result->fetch_assoc()) {
                 $SQL2 = $db_found->prepare('SELECT * FROM cl_antwoord where vraag_id = ? ' .
                    'AND gebruiker_id= ? ' .
                    'ORDER BY ingevoerd_op DESC limit 1');

                $SQL2->bind_param('ii', $vraag_row['vraag_id'], $userId);
                $SQL2->execute();
                $antw_result = $SQL2->get_result();

                $vraag_with_antwoord = false;
                while ($arow = $antw_result->fetch_assoc()) {
                    if ($arow['antwoord']) {
                      $vraag_with_antwoord = true;
                    }
                }
                if (!$vraag_with_antwoord) {
                  $groep['vragen'][] = $vraag_row;
                }


                $antw_result->close();
            }
            if ($groep['vragen'] && count($groep['vragen']) > 0) {
                $groepen[] = $groep;
            }

            $vraag_result->close();
            
        }
        $result->close();
        echo json_encode($groepen);


    } else {
        echo 'Geen db';
    }
}
?>
