<?php
// // no need to sanitise as we're not handling any database connections and BH will do that anyway
// $person     = $_POST['userID'];
// $action     = $_POST['action'];
// $comment    = $_POST['notesArea'];

// // Base 'OpenWindow' url
// $url = 'https://www.bullhornstaffing.com/BullhornStaffing/OpenWindow.cfm?entity=';

// // construct 'OpenWindow' url with params from form
// $newURL = $url . 'Note' . '&view=Add' . '&action=' . $action . '&comments=' . $comment . '&personReferenceID=' . $person;


// echo ($newURL);
// // redirect to BH OpenWindow with params
// header('Location: ' . $newURL);

// $post_data = array(
//     'item' => array(
//         'item_type_id' => $item_type,
//         'string_key' => $string_key,
//         'string_value' => $string_value,
//         'string_extra' => $string_extra,
//         'is_public' => $public,
//         'is_public_for_contacts' => $public_contacts
//     )
// );

// // PUT -> https://rest9.bullhornstaffing.com/rest-services/13n5s0/entity/Note?BhRestToken=96dc2cad-8bbd-4826-80d5-f958a56fdad3
// // BODY: \/ (JSON)
// $noteFormat = array(
//     'commentingPerson' => array(
//         'id' => "2"
//     ),
//     'candidates' => array(
//         'id' => "4"
//     ),
//     'comments' => "This is note",
//     'personReference' => array(
//         'id' => "2"
//     )
// );

// $note = [
//     'commentingPerson' => ['id' => 2], // who owns this notes
//     'candidates' => [['id' => 4]], // who is the note attached to
//     'comments' => 'this is a comment', // comment 
//     'personReference' => ['id' => 2] // the fuck is this?
// ];
    // Build the note from the mishmash of bullhorn junk
    $type       = $_POST['entityType'] ?? die("entityType failed");
    $id         = $_POST['entityId'];
    $action     = $_POST['action'];
    $comment    = $_POST['notesArea'];

    $note = [
        'action' => $action,
        'commentingPerson' => ['id' => $_COOKIE['userId']], // who owns this notes
        $type => [['id' => $id]], // who is the note attached to
        'comments' => $comment, // comment 
        //'personReference' => ['id' => 2] // the fuck is this?
    ];

    sendNote($note);

function sendNote($note)
{
    $token = $_COOKIE['BhRestToken'];
    $url = $_COOKIE['restUrl'] ?? "https://rest23.bullhornstaffing.com/rest-services/3rn5us/";
    $url = $url . "Note&BhRestToken=$token";

    $ch = curl_init($url);

    //Use the CURLOPT_PUT option to tell cURL that
    //this is a PUT request.
    curl_setopt($ch, CURLOPT_PUT, true);

    //We want the result / output returned.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Our fields.
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($note));

    print_r($note);

    curl_setopt($ch, CURLOPT_HEADER, 1);

    //Execute the request.
    $response = curl_exec($ch);

    echo $response;
}
