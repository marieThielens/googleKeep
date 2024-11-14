<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search my notes</title>
    <base href="<?= $web_root ?>">
    <!-- Pour bootsrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script></head>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include('menu.html'); ?>
<p class="container-fluid">Search by tag:</p>

<!-- Formulaire de recherche -->
<form id="searchForm" method="POST" class="container-fluid">
    <p>All Labels:</p>
    <div class="container-fluid">
        <ul class="list-inline">
            <?php foreach ($labels as $label): ?>
                <li class="list-inline-item">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="labels[]" value="<?= $label ?>">
                        <?= $label ?>
                    </label>
                </li>
            <?php endforeach; ?>
        </ul>
        <!-- champ caché -->
        <?php foreach($labels as $label) :?>
            <input type="hidden" name="<?= $label ?>" value="<?= $label ?>">
        <?php endforeach; ?>
        <button class="btn btn-primary" type="submit" value="Search">Search</button>
    </div>
</form>

<!-- Affichage des notes filtrées -->
<p class="container-fluid">Your notes:</p>
<div id="sortable" class="d-flex flex-wrap card-deck justify-content-center connectedSortable">
    <?php foreach ($filteredNotes as $note): ?>
        <div class="card overflow-hidden m-3 bg-dark justify-content-between" data-note-id="<?= $note->noteId ?>">
            <div class="card-header">
                <?php if ($note instanceof TextNotes): ?>
                    <a href="Notes/open_text_note/<?= $note->noteId ?>/">
                        <?= $note->title; ?>
                    </a>
                <?php elseif ($note instanceof CheckListNotes): ?>
                    <a href="Notes/open_checklist/<?= $note->noteId ?>">
                        <?= $note->title; ?>
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if ($note instanceof TextNotes): ?>
                    <p><?= $note->content; ?></p>
                <?php elseif ($note instanceof CheckListNotes && $note->checkListItems): ?>
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
                    <?php if (isset($labelsB[$note->noteId])): ?>
                        <!-- Parcourir chaque label associé à la note -->
                        <?php foreach ($labelsB[$note->noteId] as $label): ?>
                            <span class="badge bg-secondary m-1"><?= htmlspecialchars($label->label) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>      
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name="labels[]"]');
    const searchForm = document.getElementById('searchForm');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const formData = new FormData(searchForm);
            
            fetch('<?= $web_root ?>Notes/search', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                const newNotes = doc.querySelector('#sortable').innerHTML;
                document.querySelector('#sortable').innerHTML = newNotes;
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>

</body>
</html>