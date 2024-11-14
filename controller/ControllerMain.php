<?php
require_once 'framework/Controller.php';
require_once "model/Users.php";

class ControllerMain extends Controller {

    //si l'utilisateur est connecté, redirige vers son profil.
    //sinon, produit la vue d'accueil.  
    public function index() : void {
        if ($this->user_logged()) {
            // controller , action
            $this->redirect("user", "profile");
        } else {
            $errors = [];
           (new View("index"))->show(['errors'=> $errors]);
        }
    }
    public function login() : void {
            $mail = '';
            $password = '';
            $errors = [];
            if (isset($_POST['mail']) && isset($_POST['password'])) { 
                $mail = $_POST['mail'];
                $password = $_POST['password'];
                $errors = Users::validateLogin($mail, $password);
                if (empty($errors)) {
                    $this->log_user(Users::get_user_by_email($mail));  ///
                }
            }
            (new View("index"))->show(["mail" => $mail, "password" => $password, "errors" => $errors]);
    }

    public function signup() : void {
        $errors = [];
        $mail = "";
        $fullName = "";
        $password = "";
        $passwordConfirm = "";

        if(isset($_POST["mail"]) && isset($_POST["fullName"]) && isset($_POST["password"]) && isset($_POST["passwordConfirm"])){
            $mail = $_POST["mail"];
            $fullName = trim($_POST["fullName"]);
            $password = $_POST["password"];
            $passwordConfirm = $_POST["passwordConfirm"];

            // Créer dans la db un nouvel user grace au constructeur qu'il y a dans le modèle // , null
            $user = new Users($mail, Tools::my_hash($password), $fullName,null, "user" ); // Hacher le mot de passe
            
            // gérer les erreurs empechent d'envoyer dans la db et affiche l'erreur dans la vue
            $errors = Users::verifyIsExist($mail, null); // générer une erreur si le membre existe déjà
            $errors = array_merge($errors, $user->validateFullName()); // erreur si le format du nom est pas bon
            $errors = array_merge($errors, $user->validateMail());
            $errors = array_merge($errors, Users::validatePasswords($password, $passwordConfirm));

            if(count($errors) == 0) {
                $user->saveUser(); // sauvegarder l'user dans la db
                $this->log_user($user); // sauvegrader l'user dans la session
            }
        }
        (new View("signup"))-> show(["errors" => $errors, "mail"=>$mail, "fullName" =>$fullName]  );
    }
}
?>

