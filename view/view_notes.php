<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes</title>
    <base href="<?= $web_root ?>">
    <!-- Pour bootsrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script></head>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include('menu.html'); ?>

<?php if($pinnedNotes) : ?> <p class="container-fluid">pinned</p> <?php endif ?>
<div id="sortable1" class="d-flex flex-wrap card-deck justify-content-center connectedSortable">
    <?php foreach ($pinnedNotes as $index => $note): ?>
    <div class="card overflow-hidden m-3 bg-dark justify-content-between" data-note-id="<?= $note->noteId ?>" draggable="true">
        <!-- Cliquer vers un textNote -->
        <?php if ($note instanceof TextNotes): ?>
            <a href="Notes/open_text_note/<?=$note->noteId ?>">
        <?php endif; ?>
                <!-- Cliquer vers une note checklist -->
        <?php if ($note instanceof CheckListNotes): ?>
                <a href="Notes/open_checklist/<?=$note->noteId ?>">
            <?php endif; ?>
            <div class="card-header">
            <?= $note->title; ?>
        </div>
    </a>
        <div class="card-body">
            <!-- Quand c'est du texte -->
            <?php if ($note instanceof TextNotes): ?>
                    <p><?= $note->content; ?></p>  
            <?php endif; ?>
    
            <!-- Quand c'est des checkbox -->
            <?php if ($note instanceof CheckListNotes && $note->checkListItems): ?>
                <?php foreach ($note->checkListItems as $checkListItem): ?>
                    <div>
                            <label>
                                <input type="checkbox" disabled <?= $checkListItem->checked ? 'checked' : '' ?>>
                                <?= $checkListItem->content ?>
                            </label>
                        </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="card-footer d-flex py-3">
        <?php if(current($pinnedNotes) !== $note) : ?>
            <a href="Notes/move_note_left/<?=$note->noteId ?>" class="position-absolute bottom-0 start-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                    <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                </svg>
            </a>
            <?php endif; ?>
            
            <!-- labels -->
            <?php if (isset($labels[$note->noteId])): ?>
                <!-- Parcourir chaque label associé à la note -->
                <?php foreach ($labels[$note->noteId] as $label): ?>
                    <span class="badge bg-secondary m-1"><?= htmlspecialchars($label->label) ?></span>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($index !== array_key_last($pinnedNotes)): ?>

            <a href="Notes/move_note_right/<?=$note->noteId ?>" class="position-absolute bottom-0 end-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708"/>
                    <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
        
    </div>
    <?php endforeach; ?>
</div>

<?php if($others) : ?> <p class="container-fluid">Others</p> <?php endif ?>

<div id="sortable2" class="d-flex flex-wrap card-deck justify-content-center connectedSortable">
    <?php foreach ($others as $index => $note): ?>
    <div class="card overflow-hidden m-3 bg-dark justify-content-between" data-note-id="<?= $note->noteId ?>"  draggable="true">
        <div class="card-header">
            <?php if ($note instanceof TextNotes): ?>
            <!-- rediriger avec l'id de la note -->
                <a href="Notes/open_text_note/<?=$note->noteId ?>">
                    <?= $note->title; ?>
                </a>
            <?php endif; ?>
            <?php if ($note instanceof CheckListNotes): ?>
            <!-- rediriger avec l'id de la note -->
                <a href="Notes/open_checklist/<?=$note->noteId ?>">
                    <?= $note->title; ?>
                </a>
            <?php endif; ?>

        </div>
        <div class="card-body">
            <!-- Quand c'est du texte -->
            <?php if ($note instanceof TextNotes): ?>
                    <p><?= $note->content; ?></p>  
            <?php endif; ?>
            <!-- Quand c'est des checkbox -->
            <?php if ($note instanceof CheckListNotes && $note->checkListItems): ?>
                <?php foreach ($note->checkListItems as $checkListItem): ?>
                    <div>
                            <label>
                                <input type="checkbox" disabled <?= $checkListItem->checked ? 'checked' : '' ?>>
                                <?= $checkListItem->content ?>
                            </label>
                        </div>
                <?php endforeach; ?>
                
            <?php endif; ?>
        </div>
        <div class="card-footer d-flex py-3">
        <!-- labels -->
        <?php if (isset($labels[$note->noteId])): ?>
                <!-- Parcourir chaque label associé à la note -->
                <?php foreach ($labels[$note->noteId] as $label): ?>
                    <span class="badge bg-secondary m-1"><?= htmlspecialchars($label->label) ?></span>
                <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if(current($others) !== $note) : ?>
            <a href="Notes/move_note_left/<?=$note->noteId ?>" class="position-absolute bottom-0 start-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                    <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                </svg>
            </a>
            <?php endif; ?>

            
            <!-- Si ce n'est pas la derniere on enlève le chevron -->
            <?php if ($index !== array_key_last($others)): ?>
            <a href="Notes/move_note_right/<?=$note->noteId ?>" class="position-absolute bottom-0 end-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708"/>
                    <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if(!$pinnedNotes && !$others) : ?><p class="italique">Your notes are empty</p> <?php endif; ?> 


    <footer id="footerMesNotes">

        <a href="notes/add_text_note/<?= $userId ?>" >
            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" fill="#FFC107" class="bi bi-sticky" viewBox="0 0 16 16">
                <path d="M2.5 1A1.5 1.5 0 0 0 1 2.5v11A1.5 1.5 0 0 0 2.5 15h6.086a1.5 1.5 0 0 0 1.06-.44l4.915-4.914A1.5 1.5 0 0 0 15 8.586V2.5A1.5 1.5 0 0 0 13.5 1zM2 2.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 .5.5V8H9.5A1.5 1.5 0 0 0 8 9.5V14H2.5a.5.5 0 0 1-.5-.5zm7 11.293V9.5a.5.5 0 0 1 .5-.5h4.293z"/>
            </svg>
        </a>
        <a href="notes/addChecklistgNote" class="marginLeft">
            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" fill="#FFC107" class="bi bi-list-check" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0"/>
            </svg>
        </a>
    </footer>

    <script>

    $( function() {
    let startPos, startParent;

    $( "#sortable1, #sortable2" ).sortable({
        connectWith: ".connectedSortable",
        update: async function(event, ui) {
            // Récupérer l'ID de la note et son index initial
            const noteId = ui.item.data('note-id');
            const currentIndex = ui.item.index();
            
            // Vérifier si l'index actuel est supérieur à l'index initial
            if (currentIndex > startPos) {
                    // Effectuer une requête AJAX si l'élément est déplacé vers la droite
                    const data = await $.post("Notes/move_right_service", { param1: noteId }, null, "json");
            } else if(currentIndex < startPos) {
                const data = await $.post("Notes/move_left_service", { param1: noteId }, null, "json");

            }


            // Mettre à jour la position de départ et le parent
            startPos = currentIndex;
            startParent = ui.item.parent().data('note-id');
        },
        start: function(event, ui) {
            // Stocker la position de départ et le parent lors du début du glisser-déposer
            startPos = ui.item.index();
            startParent = ui.item.parent().data('note-id');
        }
    }).disableSelection();
});

    </script>
</body>
</html>