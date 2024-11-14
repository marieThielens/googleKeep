<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archive</title>
    <base href="<?= $web_root ?>">
    <!-- Pour bootsrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/styles.css">
    <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
</head>
<body>

    <header class="container-fluid d-flex justify-content-between mt-3 mb-5">
            <a href="Notes/index/">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg>
            </a>
            <!-- Delete-->
             
            <div>
            <?php if(!$estPartagee) : ?>
             <?php if($maNote instanceof TextNotes) : ?>
                <!-- <a  href="Notes/delete/<?= $maNote->noteId ?>"> -->
            <?php endif; ?>
            <?php if($maChecklistNote instanceof CheckListNotes) : ?>
                <!-- <a href="Notes/delete/<?= $maChecklistNote->noteId ?>"  class="mr-3"> -->
            <?php endif; ?>
                <svg id="cancelLink" data-toggle="modal" data-target="#exampleModal" class="bi bi-trash marginRight" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"  viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                    <path  d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                </svg>
            </a>
            <?php endif; ?>
            <!-- unarchive -->
            <?php if($maNote) : ?>
                <a id="cancelLink" data-toggle="modal" data-target="#exampleModal"href="Notes/un_archive_text_note/<?= $maNote->noteId; ?>">
            <?php endif; ?>
            <?php if($maChecklistNote) : ?>
                <a id="cancelLink" data-toggle="modal" data-target="#exampleModal" href="Notes/un_archive_text_note/<?= $maChecklistNote->noteId; ?>">
            <?php endif; ?>        
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-up-square" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm8.5 9.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707z"/>
                    </svg>
                </a>
            </div>
        </div>
    </header> 


    <div class="container-fluid d-flex justify-content-center">
    <div class="card overflow-hidden mb-5 bg-dark ">
        <div class="card-header">
            <?php if($maNote) : ?>
            <p><?= $maNote->title; ?></p>
            <?php endif; ?> 
            <?php if($maChecklistNote) : ?>
                <?= $maChecklistNote->title ?>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if($maNote) : ?>

            <p><?= $maNote->content; ?></p>
            <?php endif; ?>  
            <?php if($maChecklistNote) : ?>
                <?php foreach ($maChecklistNote->checkListItems as $checkListItem): ?>
                    <div>
                        <label>
                            <input type="checkbox" disabled <?= $checkListItem->checked ? 'checked' : '' ?>>
                            <?= $checkListItem->content ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?> 
        </div>
    </div>
    </div>
    <!-- ...Modale ... -->
    <div class="modal fade bg-dark" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Are you sure ? </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <?php if($maNote) : ?>
                <p>Do you really want to delete note "<?= $maNote->title; ?>" and all the dependencies</p>
            <?php endif; ?>
            <?php if($maChecklistNote) : ?>
             <p>Do you really want to delete note " <?= $maChecklistNote->title ?>" and all the dependencies</p>  
            <?php endif; ?>
               <p>This process cannot be undone</p>
            </div>
            <div class="modal-footer">
                <button id="btnCancelBack" type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button id="btnDeleteModal"  type="button" class="btn btn-danger">Yes, delete it ! </button>
            </div>
            </div>
        </div>
    </div>  


    <!-- Modale 2 -->
    <div id="secondModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Deleted</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>This note has been deleted.</p>
      </div>
      <div class="modal-footer">
        <button id="btnClose" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
    let btnCancelLink, letCancelBack;
    btnCancelLink = $("#cancelLink");
    btnCancelBack = $('#btnCancelBack');
    btnDelete = $('#btnDeleteModal');
    const idNote = <?= $maNote->noteId; ?>

    btnCancelLink.click(function(event) {
        event.preventDefault();
        $('#exampleModal').modal('show');
    });
    btnCancelBack.click(function(even) {
        $('#exampleModal').modal('hide');
    });
    $("#btnClose").on("click", function() {
        window.location.href = "Notes/index/";           
    });
    btnDelete.click(function(even) {
        
        async function deleteTextNote(int) {
            try {
                const reponse = await $.post("Notes/delete_note", {noteId: idNote}, null, json);
                if(reponse) {

                }
            } catch (error) {
                console.error("Error:", error);
            }
        }
        $('#exampleModal').modal('hide');
        $("#secondModal").modal('show');
    });



</script>
    
</body>
</html>