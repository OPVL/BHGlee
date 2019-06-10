const fetch = require("node-fetch");

const token_url = 'https://auth.bullhornstaffing.com/oauth/authorize';
const authCode = '';
const client_id = '3b2ab272-af50-4098-a3b0-f8fe712c01e1';
const client_secret = 'bne5ncAwEMNVwbCfJ0EGq2HU';

//construct the login data to get our token
// &code=${authCode}

let loginUrl = (`${token_url}?grant_type=authorization_code&client_id=${client_id}&client_secret=${client_secret}`)

let egg = fetch(token_url)
.then(function(response){
    console.log(response.data);
});