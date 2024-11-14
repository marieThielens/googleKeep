<?php
require_once "model/Users.php";
require_once 'framework/Controller.php';

class ControllerUser extends Controller {
    public function index() : void {
        $this->profile();
    }

    // profil de l'utilisateur connecté ou donné
    public function profile() : void {
        $user = $this->get_user_or_redirect();
        
        // récupérer l'id qui est dans l'url
        if(isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $userId = $_GET["param1"];
            $user = Users::getUserById($userId);
        }
        (new View("settings"))->show(["user" => $user, "fullName" => $user->fullName]);
    }

    // Gérer le changement de mot de passe
    public function changePassword() : void{
        $errors = [];
        $user = $this->get_user_or_redirect();
        $oldPassword = "";
        $newPassword = "";
        $confirmNewPassword = "";
        $success = "";

        if(isset($_POST["oldPassword"]) && isset($_POST["newPassword"]) && isset($_POST["confirmNewPassword"])
        && !empty($_POST["oldPassword"])) {
            $oldPassword = $_POST["oldPassword"];
            $newPassword = $_POST["newPassword"];
            $confirmNewPassword = $_POST["confirmNewPassword"];
            
            // Vérifier que les deux nouveaux mots de passe sont les même 
            $errors = Users::validatePasswords($newPassword, $confirmNewPassword);
            
            // Vérifier que le mot de passe entré = le mot de passé haché dans la db
            if(!Users::checkPasswordDb($oldPassword, $user->hashedPassword)){
                $errors["mdpIncorrect"] = "Wrong password. Please try again";
            };

            // Hacher le nouveau mot de passe
            $newPassword = Tools::my_hash($newPassword);

            // Vérifier que le nouveau mot de passe est différent de l'ancien
            if($newPassword == $oldPassword || $oldPassword == $confirmNewPassword){
                $errors["mdpDifferent"] = "The new password must be different from the old one.";
            }
            if(empty($errors)) { // Si la validation est bonne
                $newUser = new Users($user->mail, $newPassword, $user->fullName,$user->id, $user->role);
                $newUser->saveUser(); // sauvegarder l'user dans la db
                //$this->log_user($newUser, "user"); // sauvegarder l'user dans la session
            }

        }
        if(count($_POST) == 3 && count($errors) == 0) {
            $this->redirect("user", "changePassword", "ok");
        }
        // si param 'ok' dans l'url, on affiche le message de succès
        if (isset($_GET['param1']) && $_GET['param1'] === "ok") {
            $success = "Your profile has been successfully updated.";
        }
        (new View("change_password"))->show(["errors"=>$errors, "success"=>$success]);
    }

    // Quand on clique sur le bouton cancel, rediriger vers settings
    public function viewSettings() : void {
        $user = $this->get_user_or_redirect();
        (new View("settings"))->show(["user" => $user, "fullName"=>$user->fullName]);
    }

    public function logout(): void {
        parent::logout();
        
    }

    public function editProfile() {
        $user = $this->get_user_or_redirect(); // Récuperer l'user connecté
        $errors = [];
        $success = "";
        $originalMail = $user->mail; // Conserver l'email original de l'utilisateur
    
        if (isset($_POST['fullName']) && isset($_POST['mail'])) {
            $user->fullName = $_POST['fullName'];
            $user->mail = $_POST['mail'];
            
            // Vérifier si quelqu'un d'autre utilise déjà cet email
            $errors = Users::verifyIsExist($_POST['mail'], $user);
            $errors = array_merge($errors, $user->validateMail());
            $errors = array_merge($errors, $user->validateFullName());
    
            if (empty($errors)) {
                $user->saveUser();
            } else {
                // Si des erreurs sont trouvées, restaurer l'email original
                $user->mail = $originalMail;
            }
        } else {
            $user->mail = $originalMail;
        }
    
        // Si on est en POST et sans erreurs, on redirige avec un paramètre 'ok'
        if (count($_POST) == 2 && count($errors) == 0) {
            $this->redirect("user", "editProfile", "ok");
        }
    
        // Si param 'ok' dans l'url, on affiche le message de succès
        if (isset($_GET['param1']) && $_GET['param1'] === "ok") {
            $success = "Your profile has been successfully updated.";
        }
    
        (new View("edit_profile"))->show(["user" => $user, "errors" => $errors, "success" => $success]);
    }
    
    
}
?>