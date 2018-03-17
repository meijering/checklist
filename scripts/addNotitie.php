<?PHP
session_start();

 if (isset($_SESSION['login']['user']) ){
    require 'configure.php';

    $userId  = $_SESSION['login']['user'];

    $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME );

    if ($db_found) {

        $vraag = $_POST['vraag'];
        $notitie = $_POST['notitie'];

        $SQL = $db_found->prepare('insert into cl_opmerking (vraag_id, gebruiker_id, opmerking, ingevoerd_op ) '.
            'values(?, ?, ?, NOW())');

        $SQL->bind_param('iis', $vraag, $userId, $notitie);
        if ($SQL->execute() ) {
            $SQL4 = $db_found->prepare('SELECT * FROM cl_opmerking '.
                'WHERE vraag_id = ? AND gebruiker_id in (? , 0 ) '.
                'ORDER BY ingevoerd_op DESC');
            $SQL4->bind_param('ii', $vraag,$userId);
            $SQL4->execute();
            $remark_result = $SQL4->get_result();
            while($trow = $remark_result->fetch_assoc()) {
                $trow['opmerking'] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $trow['opmerking']);
                $opmerkingen[] = $trow;
            }
            $remark_result->close();
            echo json_encode($opmerkingen);
        } else {
            echo 'NOK';
        }
    } else {
        echo 'Geen db';
    }
 }
?>
