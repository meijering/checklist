<?PHP
session_start();
$uname = "";
$pword = "";
$salt  = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    require 'configure.php';

    $uname = $_POST['username'];
    $pword = $_POST['password'];
    $salt = DB_SALT;

    $db_found = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME );

    if ($db_found) {

        $SQL = $db_found->prepare('SELECT * FROM cl_gebruiker WHERE username = ? AND password = MD5(CONCAT( ? , ? )) AND actief > 0');

        $SQL->bind_param('sss', $uname, $salt, $pword);
        $SQL->execute();
        $result = $SQL->get_result();

        if ($result->num_rows == 1) {
            $db_field = $result->fetch_assoc();
            $_SESSION['login']['user'] = $db_field['gebruiker_id'];
            
            echo json_encode(array('id_token' => session_id(), 'naam' => $db_field['naam'], 'laatst_ingelogd' => $db_field['laatst_ingelogd']));
        }
        else {
            $_SESSION['login'] = '';
            $errorMessage = "username FAILED";
            echo $errorMessage;
        }
    } else {
        echo 'Geen db';
    }
} else {

?>


<html>
<head>
    <title>Basic Login Script</title>
</head>
<body>

<FORM NAME ="form1" METHOD ="POST" ACTION ="login.php">

    Username: <INPUT TYPE = 'TEXT' Name ='username'  value="<?PHP print $uname;?>" >
    Password: <INPUT TYPE = 'TEXT' Name ='password'  value="<?PHP print $pword;?>" maxlength="16">

    <P align = center>
        <INPUT TYPE = "Submit" Name = "Submit1"  VALUE = "Login">
    </P>

</FORM>

<P>
    <?PHP print $errorMessage;?>




</body>
</html>

<?PHP
}
?>
