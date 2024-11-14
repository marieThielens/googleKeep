<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change password</title>
    <base href="<?= $web_root ?>">
    <!-- Pour bootsrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/styles.css">

</head>
<body>
<header class="container-fluid d-flex justify-content-md-between">
    <a href="user/profile">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
        </svg>
    </a>
        <p>Change password</p>
    </header>

    <div class="container">
        <form action="user/changePassword" method="post">
            <!-- Ancien mot de passe -->
            <div class="input-group mb-2"> <!--mb-2 car c'est 2 colonnes-->
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="#D9E2E6" class="bi bi-key" viewBox="0 0 16 16">
                        <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5"/>
                        <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                    </svg>
                </div>
            </div>
            <input type="password" class="form-control texteBlanc" placeholder="Old password" name="oldPassword">
        </div>
            <?= (isset($errors["mdpIncorrect"])) ? "<p style='color: red'>".$errors["mdpIncorrect"]."</p>" : ""?>
            
        
        <!-- Nouveau mot de passe -->
            <div class="input-group mb-2"> <!--mb-2 car c'est 2 colonnes-->
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="#D9E2E6" class="bi bi-key" viewBox="0 0 16 16">
                        <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5"/>
                        <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                    </svg>
                </div>
            </div>
            <input type="password" class="form-control texteBlanc" placeholder="New password" value="" name="newPassword">
        </div> 
        
        <?= (isset($errors["mdpDifferent"])) ? "<p style='color: red'>". $errors["mdpDifferent"]."</p>" : ""?>
        <?= (isset($errors["passwordLength"])) ? "<p style='color: red'>".$errors["passwordLength"]."</p>" : ""?>
        <?= (isset($errors["passwordValid"])) ? "<p style='color: red'>".$errors["passwordValid"]."</p>" : ""?>

    
        <!-- Confirm new password -->
            <div class="input-group mb-2"> <!--mb-2 car c'est 2 colonnes-->
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="24" fill="#D9E2E6" class="bi bi-key" viewBox="0 0 16 16">
                        <path d="M0 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 10h-.535A4 4 0 0 1 0 8m4-3a3 3 0 1 0 2.712 4.285A.5.5 0 0 1 7.163 9h.63l.853-.854a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.793-.793-1-1h-6.63a.5.5 0 0 1-.451-.285A3 3 0 0 0 4 5"/>
                        <path d="M4 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                    </svg>
                </div>
            </div>
            <input type="password" class="form-control texteBlanc" placeholder="Confirm new password" value="" name="confirmNewPassword">
        </div>
        <button type="submit" value="Change password" class="btn btn-primary col-12 mt-2">Change password</button>
        <a href="user/viewSettings" role="button" class="btn btn-outline-danger col-12 mt-2 mb-4">Cancel</a>
        </form>
            <?php if (isset($success) != 0): ?>
                <div class="container-fluid d-flex justify-content-md-between">
                    <p><span class='success' style='color: green'><?= $success ?></span></p>
                    <a href="user/profile">Back to profile</a>
                </div>
            <?php endif; ?>
    </div>
</body>
</html>