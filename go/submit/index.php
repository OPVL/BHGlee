<?php
    // no need to sanitise as we're not handling any database connections and BH will do that anyway
    $person     = $_POST['userID'];
    $action     = $_POST['action'];
    $comment    = $_POST['notesArea'];

    // Base 'OpenWindow' url
    $url = 'https://www.bullhornstaffing.com/BullhornStaffing/OpenWindow.cfm?entity=';

    // construct 'OpenWindow' url with params from form
    $newURL = $url . 'Note' . '&view=Add' . '&action=' . $action . '&comments=' . $comment . '&personReferenceID=' . $person;


    echo ($newURL);
    // redirect to BH OpenWindow with params
    header('Location: ' . $newURL);

    $post_data = array(
        'item' => array(
          'item_type_id' => $item_type,
          'string_key' => $string_key,
          'string_value' => $string_value,
          'string_extra' => $string_extra,
          'is_public' => $public,
         'is_public_for_contacts' => $public_contacts
        )
      );

      // PUT -> https://rest9.bullhornstaffing.com/rest-services/13n5s0/entity/Note?BhRestToken=96dc2cad-8bbd-4826-80d5-f958a56fdad3
// BODY: \/ (JSON)
 $noteFormat = array(
    'commentingPerson' => array(
        'id' => "2"
    ),
    'candidates' => array(
        'id' => "4"
    ),
    'comments' => "This is note",
    'personReference' => array(
        'id' => "2"
    )
);
