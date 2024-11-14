<?php
require_once "framework/Model.php"; // C'est lui qui se connecte à la db
require_once "Users.php";
require_once "Notes.php";

class TextNotes extends Notes {

    public function __construct(
        public string $title,
        public Users $owner,
        public string $createdAt,
        public ?string $editedAt,
        public bool $pinned,
        public bool $archived,
        public int $weight,
        public string $content,
        public ?int $noteId = null,
        public ?int $id = null
    ) {
        parent::__construct($title, $owner, $createdAt, $editedAt, $pinned, $archived, $weight, $noteId);
        $this->content = $content;
        $this->id = $id;
    }



    // Méthode des parents, méthode communes ------------------
    public function saveNote() {
        // Vérifier si la note est déjà enregistrée dans la base de données
        if ($this->noteId) {
            self::execute("UPDATE notes SET title = :title, edited_at  = :editedAt WHERE id = :noteId", ["title" => $this->title, "noteId" => $this->noteId, ":editedAt"=>$this->editedAt]);
            // La note existe déjà, effectuer une mise à jour
            self::execute("UPDATE text_notes SET content = :content WHERE id = :noteId", ["content" => $this->content, "noteId" => $this->noteId]);
        } else {
            // La note n'existe pas encore, effectuer une insertion dans la table 'notes'
            if($this->weight==0){
                

                $addWeight = $this->weight + 1;

                // Vérifier si le poids n'existe pas déjà
                $unique = self::execute("SELECT COUNT(*) as count FROM notes WHERE  weight = :addWeight", [ "addWeight" => $addWeight]);
                $resultUnique = $unique->fetch();
            
                // Parcourir les poids jusqu'à trouver la "place disponible"
                while($resultUnique["count"] > 0) {
                    $addWeight++;
                    // Revérifier que le nouveau poids est unique
                    $unique = self::execute("SELECT COUNT(*) as count FROM notes WHERE weight = :addWeight", ["addWeight" => $addWeight]);
                    $resultUnique = $unique->fetch();
                }
                $this->weight = $addWeight;
                
            }
            self::execute("INSERT INTO notes(owner, title, weight) VALUES(:owner, :title , :weight)", ["owner" => $this->owner->id, "title" => $this->title, "weight" => $this->weight]);
    
            // Récupérer l'identifiant généré lors de l'insertion dans la table 'notes'
            $this->noteId = self::lastInsertId();
    
            // Insertion dans la table 'text_notes' avec la noteId générée
            self::execute("INSERT INTO text_notes(id, content) VALUES(:noteId, :content)", ["noteId" => $this->noteId, "content" => $this->content]);
        }
    }
    public function openNote() : Notes{
        return $this; // Retourne l'instance actuelle de TextNotes
    }
    
// -------------------------------------------------

    // Récupérer le texte d'une note
    public static function getContentNoteById(int $noteId) : TextNotes|false {
        $query = self::execute("SELECT text_notes.content, notes.* FROM text_notes JOIN notes ON text_notes.id = notes.id WHERE text_notes.id = :noteId", ["noteId" => $noteId]);
        $row =  $query->fetch();
        if ($query->rowCount() > 0) {
            $content = $row['content'] ?? '';
            $id = $row['id'];

            // Une vérification pour éviter l'erreur si le contenu est null
            if ($content !== null) {
                return new TextNotes(
                    $row['title'],
                    Users::getUserById($row['owner']),
                    $row['created_at'],
                    $row['edited_at'],
                    (bool)$row['pinned'],
                    (bool)$row['archived'],
                    $row['weight'],
                    $content,
                    $id
                );
            }
        }
        return false;
    }
    public static function validateTitle(string $title) : array {
            $errors = [];
            if(strlen($title) < Configuration::get("title_min_size") && strlen($title) > 0) {
                $errors['titleEmpty'] = "The title must be bigger than 2  ";
            }
            if(empty($title) ) {
                $errors['titleSize'] = "Title cannot be empty";
            }
            if(strlen($title) >= Configuration::get("title_max_size") ) {
                $errors['titleBigSize'] = "Title is too big. Max 25";
            }
            return $errors;
        }


        public function calculDateEdited() : string {
    
            if($this->editedAt !== null) {
                // Convertir la date de création en objet DateTime
                $editedAt = new DateTime($this->editedAt);
                // Obtenir la date actuelle
                $currentDate = new DateTime();
                // Calculer la différence en jours
                $interval = $editedAt->diff($currentDate);
                $years = $interval->y;
                $months = $interval->m;
                $days = $interval->days;
              
                  // Construire la phrase en fonction de la différence
                  if ($years > 0) {
                      return ' Edited ' . $years . ' ' . ($years === 1 ? 'year' : 'years') . ' ago';
                  } elseif ($months > 0) {
                      return ' Edited ' . $months . ' ' . ($months === 1 ? 'month' : 'months') . ' ago';
                  } elseif ($days > 0) {
                      return ' Edited ' . $days . ' ' . ($days === 1 ? 'day' : 'days') . ' ago';
                  } 
                  elseif ($days == 0 && !$currentDate) {
                      return ' Not edited';
                  } 
                  else {
                      return ' Edited Today';
                  }
            } else {
                return "";
            }
        }
    public static function updatePinnedStatus(int $noteId, bool $pinned) : void {
        self::execute("UPDATE notes SET pinned = :pinned WHERE id = :noteId", ["noteId" => $noteId, "pinned" => $pinned ? 1 : 0]);
    }
    
    public function isPinned(): bool {
        return $this->pinned;
    }
}


?>
