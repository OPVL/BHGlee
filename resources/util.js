function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

const getToken = async (time) => {

    let refresh = getCookie('refresh_token') || null;
    refresh = `?refresh=${refresh}`;
    
    const response = await fetch(`http://localhost/TestDump/Gleetest/resources/${refresh}`);
    let json = await response.json();

    time.setMinutes(time.getMinutes() + 10);
    time = time.toUTCString();

    document.cookie = `BhRestToken=${json.BhRestToken}; expires=${time}; path=/`;
    document.cookie = `restUrl=${json.restUrl}; max-age=${60*60*24*30}; path=/`;
    document.cookie = `refresh_token=${json.refresh_token}; max-age=${60*60*24*30}; path=/`;

    console.log(json);

    return json.BhRestToken;
}