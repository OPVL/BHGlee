<?php

if (!isset($_COOKIE['refresh_token'])) {
    header('Location: /gleesons/login?origin=go&term=' . $_GET['term']);
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

if (isset($_GET['term'])) {
    $searchTerm = $_GET['term'];
}

?>
<!doctype html>
<html lang="en">

<head>
    <title>GoIntegrator</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>

<body onload="handleResponse('<?= $searchTerm ?>')">
    <nav class="navbar navbar-expand-sm navbar-light text-dark" style="background: #c9dfee">
        <a class="navbar-brand" href="/gleesons/">
            <img src="../resources/img/logo-white.png" width="300" height="57" alt="">
        </a>
        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation"></button>
        <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/gleesons/dashboard">Dashboard</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#">GoIntegrator <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a title="Logout" class="nav-link" href="/gleesons/logout?BhRestToken=<?= $_COOKIE['BhRestToken'] ?>" role="button"><i class="fas fa-sign-out-alt"></i></i></a>
                </li>
                <!-- <div class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="handleResponse('<?= $searchTerm ?>')">Trigger
                        response</a>
                </div> -->
            </ul>
            <form class="form-inline my-2 my-lg-0" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                <input required class="form-control mr-md-10" type="text" placeholder="Search" name="term">
            </form>
        </div>
    </nav>
    <div id="mainPage" style="margin-bottom: 60px;">
    </div>
    <div class="card-footer text-muted text-center bottom bg-light" style="position:fixed; bottom: 0; width: 100%; height: 60px; z-index: 100;">
        Not what you wanted? Try <a data-toggle="collapse" href="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false">Searching Again</a>.
        <div class="credit">
            <p style="color: #ccc; font-size: 10px;">powered by <a href="https://evaporate.tech"> Evaporate</a><a href="https://evaporate.tech">
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
    <script src="../resources/util.js"></script>
    <script src="main.js"></script>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>