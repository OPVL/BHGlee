<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->
<?php
$message = null;
$status = $_GET['status'];

$messages = [
    "Error",
    "Double check your credentials & try again.",
    "Logged out Successfully."
];

if (isset($status)){
    $message = $messages[$_GET['message'] ?? 0];
}
?>
<head>
    <title>GoIntegrator</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="#">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <div class="container">
        <div class="d-flex justify-content-center h-100">
            <div class="card">
                <div class="card-header text-light">
                    <h3 class="text-center">Sign in to Bullhorn</h3>
                </div>
                <div class="card-body">
                    <form action='/resources/index.php' method='POST'>
                        <?= isset($status) ? "<div class='text-center alert alert-$status' role='alert'><strong style='font-size: 13px'>$message</strong></div>" : null ?>
                        <div class="form-group">
                            <label class="sr-only" for="origin">origin</label>
                            <input type="text" class="form-control" name="origin" id="origin" placeholder="origin" value="<?= $_GET['origin'] ?? 'dashboard' ?>" hidden>
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="term">term</label>
                            <input type="text" class="form-control" name="term" id="term" placeholder="term" value="?term=<?=$_GET['term']?>" hidden>
                        </div>
                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input id="username" name="username" type="text" class="form-control" placeholder="username" required>

                        </div>
                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input id="password" name="password" type="password" class="form-control" placeholder="password" required>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Login" class="btn btn-block login_btn">
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <div class="text-center">
                        <a class="text-light" href="mailto:support@gleecall.com">Forgot your password?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card-footer text-muted text-center bottom bg-light" style="position:fixed; bottom: 0; width: 100%; height: 40px; z-index: 100;">
        <div class="credit">
            <p style="color: #ccc; font-size: 10px;">powered by <a href="https://evaporate.tech">
                    Evaporate</a><a href="https://evaporate.tech">
                    <svg width="25px" height="15px" viewBox="0 0 50 30" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <!-- Generator: Sketch 48.2 (47327) - http://www.bohemiancoding.com/sketch -->
                        <title>Cloud Light</title>
                        <desc>Created with Sketch.</desc>
                        <defs></defs>
                        <g id="Logo-/-Icon-/-Light" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g id="Cloud-Light" fill="#ccc">
                                <path d="M34.8760435,23.1181739 L21.573,23.1181739 L27.5364783,11.3007826 C28.9412609,8.44295652 31.7534348,6.66773913 34.8760435,6.66773913 C39.4112609,6.66773913 43.1012609,10.3577391 43.1012609,14.8916522 C43.1012609,19.4281739 39.4112609,23.1181739 34.8760435,23.1181739 Z M11.0677826,23.1181739 C8.61169565,23.1181739 6.61473913,21.119913 6.61473913,18.6638261 C6.61473913,16.2090435 8.61169565,14.212087 11.0677826,14.212087 C13.0777826,14.212087 14.4343043,15.5112174 15.0643043,16.7894783 L16.2095217,19.0629565 L14.163,23.1181739 L11.0677826,23.1181739 Z M34.8760435,0.0520869565 C29.0273478,0.0520869565 24.0186522,3.46686957 21.6003913,8.38295652 L19.7938696,11.9607826 C17.7799565,9.32469565 14.6443043,7.59773913 11.0677826,7.59773913 C4.9556087,7.59773913 0.000391304348,12.5516522 0.000391304348,18.6638261 C0.000391304348,24.6990435 4.83430435,29.5838261 10.836913,29.7090435 L10.8264783,29.7325217 L11.0677826,29.7325217 L29.0038696,29.7325217 L34.8760435,29.7325217 C43.0699565,29.7325217 49.7156087,23.0881739 49.7156087,14.8916522 C49.7156087,6.69643478 43.0699565,0.0520869565 34.8760435,0.0520869565 Z" id="Cloud"></path>
                            </g>
                        </g>
                    </svg>
                </a>
            </p>
        </div>
    </div>
    <script src="" async defer></script>
</body>

</html>