<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives</title>
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

<div class="container">
    <h2>Archives</h2>
    <?php if (empty($archives)): ?>
        <p class="italique">No archives available.</p>
    <?php else: ?>
        <div class="d-flex flex-wrap card-deck justify-content-between">
            <?php foreach ($archives as $note): ?>
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
                    <?php if ($note instanceof TextNotes): ?>
                        <p><?= $note->content; ?></p>
                        <?php elseif ($note instanceof CheckListNotes): ?>
                            <?php foreach ($note->checkListItems as $item): ?>
                                <div>
                                    <label>
                                        <input type="checkbox" disabled <?= $item->checked ? 'checked' : '' ?>>
                                        <?= $item->content ?>
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
                    </div>
                </div> 
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
    
</body>
</html>