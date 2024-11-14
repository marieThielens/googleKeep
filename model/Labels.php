<?php
require_once "framework/Model.php"; // C'est lui qui se connecte à la db

class Labels extends Model {
    public function __construct(public ?int $noteId, public string $label) {}

    public function saveLabel(string $label, int $noteId): void {
        // Vérifier si le label existe déjà pour cette note
        if (!$this->labelExists($label, $noteId)) {
            // Insérer le label seulement s'il n'existe pas déjà
            $query = self::execute("INSERT INTO note_labels (note, label) VALUES (:noteId, :label)", ["noteId" => $noteId, "label" => $label]);
            // Gérer les erreurs ou les succès de l'insertion
            // ...
        } else {
            // Gérer le cas où le label existe déjà
            // Par exemple, afficher un message d'erreur ou ignorer l'insertion
            // ...
        }
    }
    
    // Méthode pour vérifier si le label existe déjà pour la note donnée
    public static function labelExists(string $label, int $noteId): bool {
        $query = self::execute("SELECT COUNT(*) FROM note_labels WHERE note = :noteId AND label = :label", ["noteId" => $noteId, "label" => $label]);
        $count = $query->fetchColumn();
        return $count > 0;
    }



     // Récupérer le (1) label d'un note
     public static function getLabelsById(int $noteId) : Labels|false {
        $query = self::execute("SELECT * FROM note_labels WHERE note = :noteId", ["noteId" => $noteId]);
        $row =  $query->fetch();
        if($query->rowCount() > 0) {
            $label = $row["label"];
            $note = $row["note"];
            if($label !== null) {
                return new Labels($note, $label);
            }
        }
        return false;
     }
     public static function getLabelByName(string $name) {
        $query = self::execute("SELECT * FROM note_labels WHERE label = :label",["label"=> $name]);
        return $query->fetchAll(); // Récupérer toutes les lignes
     }
     public static function getCheckLabelsByNoteId($labels, $noteId) {
        $totalCount = 0; 
    
        foreach ($labels as $label) {
            $query = self::execute("SELECT * FROM note_labels WHERE note = :noteId AND label = :label", ["noteId" => $noteId, "label" => $label]);
            $totalCount += $query->rowCount();
        }
    
        if ($totalCount === count($labels)) {
            return true; 
        } else {
            return false; 
        }
    }

     public static function getLabels($notes) : array{
        $labels = [];

        // Iterate over each element in the $notes array
        foreach ($notes as $note) {
            // Assuming each element in $notes is an associative array with a 'noteId' key
            $noteId = $note->noteId;

            // Get labels corresponding to the noteId
            $noteLabels = self::getLabelsByNote($noteId);

            // Add unique labels to the $labels array
            foreach ($noteLabels as $label) {
                // Assuming each label is an instance of Labels class with a 'label' property
                $labelName = $label->label;
                // Add the label to the $labels array only if it doesn't already exist
                if (!isset($labels[$labelName])) {
                    $labels[$labelName] = $labelName;
                }
            }
        }
        
        // Return the array containing all the unique labels
        return array_values($labels); // Re-index the array to remove associative keys
     } 

     // 
     public static function getLabelsByNote(int $noteId) : array {
        $query = self::execute("SELECT note,label  FROM note_labels WHERE note = :noteId", ["noteId" => $noteId]);
        $reponse = $query->fetchAll();
        $tab = [];
        foreach($reponse as $row) {
            $tab[] = new Labels(
                $row["note"],
                $row["label"]
            );
        }
        return $tab;
     }

     public static function validate(string $label, int $noteId) : array {
        $errors = [];
        if(strlen($label) < Configuration::get("label_min_size") || strlen($label) > Configuration::get("label_max_size")) {
            $errors["labelSize"] = "Label length must be between 2 and 10";
        }
        if (strpos($label, ' ') !== false) {
            $errors["labelSpaces"] = "Label cannot contain spaces.";
        }
        if(self::labelExists($label,$noteId)) {
            $errors["sameLabel"] = "A note cannot contain the same label twice.";
        }
        return $errors;
     }

    public static function deleteLabel(int $note, string $label) : void {
        self::execute("DELETE FROM note_labels WHERE note = :note and label = :label", ["note" => $note, "label" => $label]);
    }
}