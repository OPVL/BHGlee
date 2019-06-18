<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_COOKIE['refresh_token']))
    header("Location: /login");
require "../resources/service/functions.php";

$conn = DatabaseMisc::connect();
$limit = 100;
?>

<!doctype html>
<html lang="en">

<head>
    <title>Gleesons Sales Dashboard</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>

<body onload="">
    <nav class="navbar navbar-expand-sm navbar-light text-dark" style="background: #c9dfee">
        <a class="navbar-brand" href="/">
            <img src="../resources/img/logo-white.png" width="300" height="57" alt="">
        </a>
        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation"></button>
        <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/gleesons/dashboard">Dashboard<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/gleesons/go/">GoIntegrator</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#">Admin<span class="sr-only">(current)</span></a>
                </li>
                <!-- <div class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="handleResponse('<?= $searchTerm ?>')">Trigger
                        response</a>
                </div> -->
            </ul>
        </div>
    </nav>
    <div class="container-fluid">
        <?php
        $sql = "SELECT * FROM event_log ORDER BY id DESC LIMIT $limit";
        $res = $conn->query($sql);

        if (!$res) {
            die("Query Failed: $conn->error");
        }

        echo '<table class="table table-hover"><thead class="thead-light"><tr><th scope="col">#</th><th scope="col">Message</th><th scope="col">Category</th><th scope="col">Time</th><th scope="col">Date</th><th scope="col">IP</th></tr></thead><tbody>';

        while ($row = $res->fetch_assoc()) {
            $colour = '#eee';
            
            switch ($row['category']) {
                case 'value':
                    # code...
                    break;
                
                default:
                    # code...
                    break;
            }
            echo("<tr>");
            echo("<th scope='row'>".$row['id']."</th>");
            echo("<td>".$row['message']."</td>");
            echo("<td>".$row['category']."</td>");
            echo("<td class='bg-warn'>".date('H:i:s',$row['time'])."</td>");
            echo("<td>".date('d/m/y',$row['time'])."</td>");
            echo("<td>".$row['ip']."</td>");
            echo("</tr>");
        }

        echo("</tbody></table>");
        ?>
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
</body>

</html>