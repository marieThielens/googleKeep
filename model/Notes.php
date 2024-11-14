<?php
require_once "framework/Model.php"; // C'est lui qui se connecte à la db
require_once "Users.php";

abstract class Notes extends Model {

   public function __construct(public string $title, public Users $owner, public string $createdAt, public ?string $editedAt, public bool $pinned, public bool $archived, public int $wheiht, public ?int $noteId = null ) {} 


   // Méthode commune ------------------
    abstract protected function saveNote();
    abstract public function openNote() : Notes;

    // Récupère les id des notes de l'user
    public static function getNotes(Users $user) : array {
        $notesId = self::getNoteById($user);
        $myTextNotes = [];
        $myCheckListNotes = [];
    
        foreach ($notesId as $n) {
            $oneNote = TextNotes::getContentNoteById($n["id"]);

            if (!$oneNote) { // Si ce n'est pas un textNote
                $checkListNote = CheckListNotes::getChecListkNoteId($n["id"]);

                if ($checkListNote !== false) {
                    $myCheckListNotes[] = $checkListNote;
                }
            } else {
                $myTextNotes[] = $oneNote;
            }
        }
    
        // Fusionner les deux tableaux
        $myNotes = array_merge($myTextNotes, $myCheckListNotes);
        return $myNotes;
    }

    private static function getNoteById(Users $user) : array {
        $query = self::execute("SELECT * FROM notes where owner =:userId and archived = 0 order by weight", ["userId"=> $user->id]); 
        $reponse = $query->fetchAll();
        return $reponse;
    }
    
    public static function noteArchivee(int $noteId) : bool {
        $query = self::execute("SELECT COUNT(*) FROM notes WHERE id = :noteId and archived = 1" , ["noteId" => $noteId]);
        $result = $query->fetchColumn();
        return $result > 0 ;
    }

    // Les archives
    public static function getArchives(Users $user) : array{
        $notesArchivees = self::getArchivesNoteById($user);
        $archives = [];

        foreach ($notesArchivees as $n) {
            $archive = TextNotes::getContentNoteById($n["id"]);
           // var_dump($archive);
            if (!$archive) {
                $archive = CheckListNotes::getChecListkNoteId($n["id"]);
            }
            if ($archive) {
                $archives[] = $archive;
            }
        }
        return $archives;
    }
    private static function getArchivesNoteById(Users $user) : array {
        $query = self::execute("SELECT * FROM notes where owner =:userId and archived = 1 order by weight asc", ["userId"=> $user->id]); 
        $reponse = $query->fetchAll();
        return $reponse;
    } 

  //  Récupérer toutes les notes épinglées d'un utilisateur (non archivées et non partagées)
    public static function getPinnedNotes(Users $user): array {
        $query = self::execute("SELECT * FROM notes WHERE owner = :userId AND pinned = 1 AND archived = 0 order by weight asc", ["userId" => $user->id]);
        $reponse = $query->fetchAll();

        $pinnedNotes = [];
        foreach ($reponse as $row) {
            $pinnedNote = TextNotes::getContentNoteById($row["id"]);
            $pinnedNote = $pinnedNote ? $pinnedNote : CheckListNotes::getChecListkNoteId($row["id"]);

            if ($pinnedNote) {
                array_push($pinnedNotes, $pinnedNote);
            }
        }
        return $pinnedNotes;
    }

    // // Récupérer toutes les autres notes d'un utilisateur (non épinglées, non archivées et non partagées)
    public static function getOtherNotes(Users $user): array {
        $query = self::execute("SELECT * FROM notes WHERE owner = :userId AND pinned = 0 AND archived = 0 order by weight asc", ["userId" => $user->id]);
        $reponse = $query->fetchAll();
        $otherNotes = [];
        foreach ($reponse as $row) {
            $otherNote = TextNotes::getContentNoteById($row["id"]);
            $otherNote = $otherNote ? $otherNote : CheckListNotes::getChecListkNoteId($row["id"]);
            if($otherNote) {
                array_push($otherNotes, $otherNote);
            }
        }
        return $otherNotes;
    }





// sauvegarder la note partagée par ses enfants
public function save() {
    $this->saveNote();
}


// Archiver une note
public function archiveNote(): void {
    Self::execute("UPDATE notes SET archived = 1 WHERE id = :noteId", ["noteId" => $this->noteId]);
}
// Desarchiver une note
public function unArchiveNote() : void {
    Self::execute("UPDATE notes SET archived = 0 WHERE id = :noteId", ["noteId" => $this->noteId]);
}

// delete
public function delete() : void {
    Self::execute("DELETE FROM note_labels WHERE note = :noteId;" , ["noteId" => $this->noteId]);
    Self::execute("DELETE FROM text_notes WHERE id = :noteId;" , ["noteId" => $this->noteId]);
    //DELETE FROM note_labels WHERE note = 22;
    
    Self::execute("DELETE FROM notes WHERE id = :noteId;" , ["noteId" => $this->noteId]);
}

public function delete_checkList() : void {
    Self::execute("DELETE FROM note_shares WHERE note = :noteId;" , ["noteId" => $this->noteId]);
    Self::execute("DELETE FROM checklist_note_items WHERE checklist_note = :noteId;" , ["noteId" => $this->noteId]);
    Self::execute("DELETE FROM note_labels WHERE note = :noteId;" , ["noteId" => $this->noteId]);
    
    Self::execute("DELETE FROM checklist_notes WHERE id = :noteId;" , ["noteId" => $this->noteId]);
    //DELETE FROM note_labels WHERE note = 22;
    
    Self::execute("DELETE FROM notes WHERE id = :noteId;" , ["noteId" => $this->noteId]);
}

public static function newWeightAdd(int $noteId) {
   $query =  self::execute("SELECT owner, weight from notes WHERE id = :noteId" , ["noteId" => $noteId]);
   $result = $query->fetch();
   if($result) {
        $currentWeight = $result["weight"]; // Le poids actuel dans la db
        $owner = $result["owner"]; 
        $addWeight = $currentWeight + 1;

        // Vérifier si le poids n'existe pas déjà
        $unique = self::execute("SELECT COUNT(*) as count FROM notes WHERE owner = :owner AND weight = :addWeight", ["owner" => $owner, "addWeight" => $addWeight]);
        $resultInique = $unique->fetch();
        // Parcourir les poids jusqu'à trouver la "place disponible"
        while($resultInique["count"] > 0) {
            $addWeight++;
            // revérifier que le nouveau poid est unique
            $unique = self::execute("SELECT COUNT(*) as count FROM notes WHERE owner = :owner AND weight = :addWeight", ["owner" => $owner, "addWeight" => $addWeight]);
            $resultInique = $unique->fetch();
            // if($resultInique["count"] === 0) {
            //     self::execute("UPDATE notes SET weight = $addWeight WHERE id = :noteId", ["noteId" => $noteId]);
            // }

        }
         self::execute("UPDATE notes SET weight = $addWeight WHERE id = :noteId", ["noteId" => $noteId]);
   }
}
public static function newWeightSub(int $noteId) {
    $query =  self::execute("SELECT owner, weight from notes WHERE id = :noteId" , ["noteId" => $noteId]);
    $result = $query->fetch();
    if($result) {
         $currentWeight = $result["weight"]; // Le poids actuel dans la db
         $owner = $result["owner"]; 
         $addWeight = $currentWeight - 1;
 
         // Vérifier si le poids n'existe pas déjà
         $unique = self::execute("SELECT COUNT(*) as count FROM notes WHERE owner = :owner AND weight = :addWeight", ["owner" => $owner, "addWeight" => $addWeight]);
         $resultInique = $unique->fetch();
         // Parcourir les poids jusqu'à trouver la "place disponible"
         while($resultInique["count"] > 0) {
             $addWeight--;
             // revérifier que le nouveau poid est unique
             $unique = self::execute("SELECT COUNT(*) as count FROM notes WHERE owner = :owner AND weight = :addWeight", ["owner" => $owner, "addWeight" => $addWeight]);
             $resultInique = $unique->fetch();
 
         }
         self::execute("UPDATE notes SET weight = $addWeight WHERE id = :noteId", ["noteId" => $noteId]);
    }
 }
// public static function showByWeight(int $noteId){
//     $query = self::execute("SELECT * FROM notes ORDER BY weight ASC", ["noteId" => $noteId]);
//     $result = $query->fetchAll();

// }
     // Pour changer le header 
     public static function notePartage(int $noteId) : bool {
        $query = self::execute("SELECT COUNT(*) FROM note_shares WHERE note = :noteId", ["noteId" => $noteId]);
        $result = $query->fetchColumn();
        return $result > 0 ;
    }


public function calculDateCreated() : string {
    // Convertir la date de création en objet DateTime
    $createdAt = new DateTime($this->createdAt);

    // Obtenir la date actuelle
    $currentDate = new DateTime();

    // Calculer la différence en jours
    $interval = $createdAt->diff($currentDate);
    $years = $interval->y;
    $months = $interval->m;
    $days = $interval->days;

    // Construire la phrase en fonction de la différence
    if ($years > 0) {
        return $years . ' ' . ($years === 1 ? 'year' : 'years') . ' ago';
    } elseif ($months > 0) {
        return $months . ' ' . ($months === 1 ? 'month' : 'months') . ' ago';
    } elseif ($days > 0) {
        return $days . ' ' . ($days === 1 ? 'day' : 'days') . ' ago';
    } else {
        return 'Today';
    }
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

public static function titleExist(string $title, Users $user) : bool {
    $query = self::execute("SELECT * FROM notes where title = :title and owner = :owner", 
    ["title" => $title, "owner" =>  $user->id ]);
    return $query->rowCount() > 0;
}

}

?>