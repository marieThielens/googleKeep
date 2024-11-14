<?php

require_once "framework/Model.php"; // C'est lui qui se connecte à la db

class Users extends Model {
    public function __construct(public string $mail,public string $hashedPassword, public string $fullName,public ?int $id = null, public string $role = "user" ){}

    // sauvegarder ou mettre à jour l'utilisateur
    public function saveUser() : Users {
        if($this->userId() !== null) { // vérifie si le membre existe
            self::execute("UPDATE users SET mail =:mail,hashed_Password =:hashedPassword , full_name =:fullName WHERE id=:id",
                ["mail"=>$this->mail, "hashedPassword" => $this->hashedPassword, "fullName" => $this->fullName, "id" => $this->userId()] );
        } else {
            // nom col db                                    variable
            self::execute("INSERT into users(mail, hashed_password, full_name, role) VALUES(:mail, :hashedPassword, :fullName, :role )",
                //maVariable , l'objet
                ["mail"=>$this->mail, "hashedPassword"=>$this->hashedPassword, "fullName"=>$this->fullName, "role"=>$this->role ]);
            $this->id = self::lastInsertId();
        }
        return $this;
    }

    // Récupérer un membre à partir de son mail
    public static function get_user_by_email(string $mail) : Users|false {
        $query = self::execute("SELECT * FROM users where mail = :mail", ["mail"=>$mail]);
        $data = $query->fetch();
        if($query->rowCount() == 0) {
            return false;
        } else {
                        // $data["nom colonne db"]
            return new Users($data["mail"], $data["hashed_password"], $data["full_name"],$data["id"], $data["role"] );
        }
    }
    
    // Vérifier si un utilisateur existe déjà
    public static function verifyIsExist(string $mail , Users $currentUser = null) : array {
        $errors = [];
        $user = self::get_user_by_email($mail);
        if($user && (!$currentUser || $currentUser->id !== $user->id)){
            $errors["userExist"] = "A user with this email already exists.";
        }
        return $errors;
    }

    // Valider le format d'un email
    public function validateMail() : array {
        $errors = [];

        // Fonction qui vérifie le format du mail
        if(filter_var($this->mail, FILTER_VALIDATE_EMAIL) === false) {
            $errors["mailInvalidFormat"] = "You have to enter a valid email format";
        } 
        if(empty($this->mail)){
            $errors["emptyMail"] ="You must give an email address";
        }
        return $errors;
    }

    // valider le format du pseudo
    public function validateFullName() : array {
        $errors = [];
        if(empty($this->fullName)) {
            $errors["pseudoRequired"] = "Full name is required.";
        } if(strlen($this->fullName) < Configuration::get("min_full_name")) {
            $errors["pseudoLength"] = "Full name length must be minimum 3";
        }
        return $errors;
    }

    // Vérifier que le mot entré correspond à celui dans la db hashé
    public static function checkPasswordDb(string $mdpClear, string $mdpHash){
        return $mdpHash === Tools::my_hash($mdpClear);
    }

    // Le format du mot de passe
    public static function validatePassword(string $password) : array {
        $errors = [];
        
        if (strlen($password) < Configuration::get("min_hash_password") ) {
            $errors["passwordLength"] = "Password length must to have a length of minimum 8.";
        } if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
            $errors["passwordValid"] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }

    // Vérifier que le mot de passe et sa confirmation sont les mêmes
    public static function validatePasswords(string $password, string $passwordConfirm) : array{
        $errors = Users::validatePassword($password);
        if($password != $passwordConfirm) {
            $errors["passwordSame"] = "You have to enter twice the same password.";
        }
        if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
            $errors["passwordValid"] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        if (strlen($password) < Configuration::get("min_hash_password") ) { // 
            $errors["passwordLength"] = "Password length must to have a length of minimum 8.";
        }
        return $errors;
    } 

    // A l'accueil, vérifier que le login et le mail sont bons
    public static function validateLogin(string $mail, string $password) : array {
        $errors = [];
        if (empty($mail)) {
            $errors["emptyMail"] = "Mail is empty";
        } else {
            $member = Users::get_user_by_email($mail);
            if ($member) {
                if (!self::checkPasswordDb($password, $member->hashedPassword)) {
                    $errors["wrongPassword"] = "Wrong password. Please try again.";
                }
            } else {
                $errors["exist"] = "Can't find a member with the email '$mail'. Please sign up.";
            }
        }
        return $errors;
    }

    // Récupérer un membre par son id
    public static function getUserById(int $id) : Users|false {
        $query = self::execute("SELECT * from users where id = :id", ["id" => $id]);
        $data = $query->fetch();
        if($query->rowCount() == 0) { // si l'utilisateur n'existe pas
            return false;
        } else {
            // On retourne l'utilisateur  $data["nom colonne db"]
            return new Users($data["mail"], $data["hashed_password"], $data["full_name"], $data["id"], $data["role"] );
        }
    }
    // Récupérer les notes depuis le model Note
    public function getNotes() : array {
        return Notes::getNotes($this);
    }
    public function getLabels($notes) : array {
        return Labels::getLabels($notes);
    }

    public function getNotesById() : array {
        return Notes::getNotes($this);
    }

    // Récupérer les notes épinglée
    public function getPinnedNotes() : array {
        return Notes::getPinnedNotes($this);
    }

    // Récupérer les autres notes
    public function getOtherNotes() : array {
        return Notes::getOtherNotes($this);
    }

     public function userId() : ?int {
        return $this->id;
     }


     public function hasAccessToNote(Notes $note) : bool {
        $creator = self::execute("SELECT * FROM notes WHERE notes.id = :idNote and notes.owner =:idUser", ["idNote" => $note->noteId, "idUser"=> $this->id ]);
        return $creator->rowCount() > 0;
     }
     // Vérifier qu'on ne peut pas faire n'importe quoi avec l'url
     public function hasAccessToNoteOrAbort($noteId): void {
        // Vérifier si le paramètre est numérique
        if (!isset($noteId) || !is_numeric($noteId)) {
            Tools::abort("The URL is invalid, parameter must be integer");
        }

        // Vérifier si la note existe dans la base de données
        $maNote = TextNotes::getContentNoteById($noteId);
        $mesCheckList = CheckListNotes::getChecListkNoteId($noteId);

        if (!$maNote && !$mesCheckList ) {
            Tools::abort("Note not found");
        }
        // Vérifier l'accès à la note
        if (($maNote && !$this->hasAccessToNote($maNote)) || ($mesCheckList && !$this->hasAccessToNote($mesCheckList))) {
            Tools::abort("You don't have access to this note");
        }
    }

}

?>