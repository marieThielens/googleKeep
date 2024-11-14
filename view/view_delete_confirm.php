<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm delete</title>
    <base href="<?= $web_root ?>">
    <!-- Pour bootsrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="d-flex align-items-center m-1">
    <div class="container border text-center mr-1 ml-1 mt-5 rounded">
        <div class="container text-center mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-trash  text-danger ms-auto" viewBox="0 0 16 16">
                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
            </svg>
            <h2 class="text-center text-danger h2 mb-1">Are you sure ? </h2>
        </div>
        <hr>
        <div>
            <p class="text-danger h3">Do you really want to delete this note ? <span class="font-weight-bold">
                <?php if($maNote) : $maNote->title;  endif?>
                <?php if($maCheck) : $maCheck->title;  endif?>
            </span> and all of its dependencies ? </p>
        </div>
        <div>
            <p class="h2">This process cannot be undone</p>
        </div>
        <div class="container-fluid d-flex justify-content-center mb-4">
            <a href="Notes/index" type="button" class="btn btn-secondary">Cancel</a>
            <?php if($maNote) : ?>
            <form action="Notes/delete_note/<?= $noteId ?>" method="post">
                <input type="submit" class="btn btn-danger" name="delete" value="Delete">
            </form>
            <?php endif ?>
            <?php if($maCheck) : ?>
                <form action="Notes/delete_note/<?= $noteId ?>" method="post">
                    <input type="submit" class="btn btn-danger" name="delete" value="Delete">
                </form>
            <?php endif ?>
        </div>
    </div>
    
</body>
</html>