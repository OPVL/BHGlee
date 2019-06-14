<?php
if (!isset($_COOKIE['refresh_token'])){
    header('Location: /gleesons/login?origin=dashboard');
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Gleesons Sales Dashboard</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>

<body onload="init()">
    <nav class="navbar navbar-expand-sm navbar-light text-dark" style="background: #c9dfee">
        <a class="navbar-brand" href="/gleesons/">
            <img src="../resources/img/logo-white.png" width="300" height="57" alt="">
        </a>
        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId"
            aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation"></button>
        <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Dashboard<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/gleesons/go/">GoIntegrator</a>
                </li>
                <!-- <div class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="handleResponse('<?=$searchTerm?>')">Trigger
                        response</a>
                </div> -->
            </ul>
        </div>
    </nav>
    <div class="container-fluid">
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="jumbotron">
                    <h1 class="display-4" id="welcome-msg">Hello. . .</h1>
                    <p class="lead" id="sub-welcome">Q1 - 2019</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card text-dark bg-light">
                    <div class="card-header">Current Year Progress</div>
                    <div class="card-body">
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">January 2019 - December 2019</h4>

                            </div>
                            <div class="col-md-6">
                                <h4 id="quotaAnnual">Annual: £150,000</h4>
                            </div>
                        </div>
                        <p class="card-text" id="jobCount">Placements:</p>
                        <div class="progress">
                            <div class="progress-bar bg-info" id="quotaPercentA" aria-valuemin="0" aria-valuemax="100">
                                0%</div>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <hr class="my-2">
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="card text-dark bg-light">
                    <div class="card-header">Current Quarter Progress</div>
                    <div class="card-body">
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title" id="quarter-title">January 1st - March 31st</h4>
                            </div>
                            <div class="col-md-6">
                                <h4 id="quotaQuarter">Quarterly: £37,500</h4>
                            </div>
                        </div>
                        <p class="card-text" id="jobCount">Placements:</p>
                        <div class="progress">
                            <div class="progress-bar bg-info" id="quotaPercentQ" aria-valuemin="0" aria-valuemax="100">
                                0%
                            </div>
                        </div>
                        <br>
                        <p id="jobCount"></p> <!-- Current successful placements in range -->
                        <p id="quotaCurrent"></p> <!-- Current progress in range -->
                        <p id="quotaPercent"></p> <!-- Current progress (%) in range -->
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>
    <script src="main.js"></script>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
</body>

</html>