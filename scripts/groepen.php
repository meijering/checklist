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

            $SQL = $db_found->prepare('SELECT * FROM cl_vraag where groep_id = ?');
            $SQL->bind_param('i', $row['groep_id']);
            $SQL->execute();
            $vraag_result = $SQL->get_result();
            $row['total'] = 0;
            $row['percentage'] = 0;
            $count = 0;
            while($vragen_row = $vraag_result->fetch_array())
            {
                if ($vragen_row['type'] == "checkbox") {
                    $row['total']++;
                    $SQL2 = $db_found->prepare('SELECT * FROM cl_antwoord where vraag_id = ? ' .
                        'AND gebruiker_id = ? ' .
                        'ORDER BY ingevoerd_op DESC limit 1');
                    $SQL2->bind_param('ii', $vragen_row['vraag_id'], $userId);
                    $SQL2->execute();
                    $antwoord_result = $SQL2->get_result();
                    while ($antwrow = $antwoord_result->fetch_array()) {
                        if ($antwrow['antwoord']) {
                            $count++;
                        }
                    }
                    $antwoord_result->close();
                }
            }
            $vraag_result->close();
            if ($count > 0) {
                $row['percentage'] = intval(1000*($count/$row['total'])/10);
            }


            $groepen[] = $row;

        }
        $result->close();


        echo json_encode($groepen);



    } else {
        echo 'Geen db';
    }
 }
?>
