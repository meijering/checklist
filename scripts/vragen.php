<?PHP
session_start();
$errorMessage = "";


 if (isset($_SESSION['login']['user']) ){
    require 'configure.php';

    $userId  = $_SESSION['login']['user'];

    $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME );

    if ($db_found) {

        $groep = $_POST['groep'];

        $SQL = $db_found->prepare('SELECT * FROM cl_vraag where groep_id = ? order by rang');
        $SQL->bind_param('i', $groep);
        $SQL->execute();
        $result = $SQL->get_result();
        
        while($row = $result->fetch_assoc()) {

            $SQL2 = $db_found->prepare('SELECT * FROM cl_antwoord where vraag_id = ? '.
            'AND gebruiker_id= ? '.
            'ORDER BY ingevoerd_op DESC limit 1');

            $SQL2->bind_param('ii', $row['vraag_id'], $userId);
            $SQL2->execute();
            $antw_result = $SQL2->get_result();
            $row['checked'] = false;
            while($arow = $antw_result->fetch_assoc()) {
                if ($arow['antwoord']) {
                    $row['checked'] = true;
                }
                $row['antwoord'] = $arow;
            }
            $antw_result->close();

            $SQL3 = $db_found->prepare('SELECT * FROM cl_tip '.
                'WHERE vraag_id = ? AND gebruiker_id in (? , 0 ) '.
                'ORDER BY ingevoerd_op DESC');
            $SQL3->bind_param('ii', $row['vraag_id'],$userId);

            if ($SQL3->execute() ) {
               $tip_result = $SQL3->get_result();
                while($trow = $tip_result->fetch_assoc()) {
                    $trow['tip'] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $trow['tip']);
                    $row['tips'][] = $trow;
                }
                $tip_result->close();
            } else {
                $row['tips'] = array();
            }
            $SQL4 = $db_found->prepare('SELECT * FROM cl_opmerking '.
                'WHERE vraag_id = ? AND gebruiker_id in (? , 0 ) '.
                'ORDER BY ingevoerd_op DESC');
            $SQL4->bind_param('ii', $row['vraag_id'],$userId);
            $SQL4->execute();
            $remark_result = $SQL4->get_result();
            if ($remark_result) {
                while($trow = $remark_result->fetch_assoc()) {
                    $trow['opmerking'] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $trow['opmerking']);
                    $row['opmerkingen'][] = $trow;
                }
                $remark_result->close();
            } else {
                $row['opmerkingen'] = array();
            }

            $vragen[] = $row;

        }
        $result->close();
        echo json_encode($vragen);



    } else {
        echo 'Geen db';
    }
 }
?>
