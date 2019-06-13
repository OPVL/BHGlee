//https://rest23.bullhornstaffing.com/rest-services/3rn5us/search/Placement?fields=id,status,dateAdded,jobSubmission,candidate(id,firstName,lastName,source,isAnonymized),jobOrder(id,title,clientCorporation(id,name),clientContact(id,firstName,lastName,email)),salary,payRate,clientBillRate,dateBegin,dateEnd,employmentType,fee&sort=-dateAdded&start=0&count=10&showTotalMatched=true&showEditable=true&query=candidate.id:84628 AND dateAdded:[20190422230000 TO 20190430230000]
//https://rest23.bullhornstaffing.com/rest-services/3rn5us/search/Placement?fields=id,status,dateAdded,jobSubmission,candidate(id,firstName,lastName,source,isAnonymized),jobOrder(id,title,clientCorporation(id,name),clientContact(id,firstName,lastName,email)),salary,payRate,clientBillRate,dateBegin,dateEnd,employmentType,fee&sort=-dateAdded&start=0&count=25&showTotalMatched=true&showEditable=true&query=candidate.id:84628 AND dateAdded:[20190130000000 TO 20190430230000]
// const restUrl = 'https://cls23.bullhornstaffing.com/core';
// const restUrl2 = 'https://rest23.bullhornstaffing.com/rest-services/3rn5us';
const fields = 'dealValue,dateAdded,actualCloseDate,reasonClosed';
const sort = 'dateAdded';
const query = 'status:"Closed-Won" AND actualCloseDate:[20190101000000 TO 20190429225959] AND owner.id:(87030) AND NOT status:Archive';
let token = false;
let restUrl = 'https://rest23.bullhornstaffing.com/rest-services/3rn5us/';

const fields2 = [
    'id', 'dateAdded', 'status', 'employmentType', 'salary', 'dateBegin', 'flatFee', 'owner'
];

function init(args = null) {

    if (!token) {
        //BhRestToken is valid for 10 minutes
        console.log('getting token');
        token = getCookie('token');
        if (token) {
            console.log(`token is ${token}`);
            getInfo();
            return;
        }
        getToken(new Date());
    }

    cookieConsent();
}

const getToken = async (time) => {

    const response = await fetch('http://localhost/TestDump/Gleetest/resources/');
    json = await response.json();
    token = json.BhRestToken;
    restUrl = json.restUrl;

    time.setMinutes(time.getMinutes() + 10);
    time = time.toUTCString();

    document.cookie = `token=${json.BhRestToken}; expires=${time}`;

    console.log(json);

    getInfo();
}

const getInfo = async() => {
    let quarter = getQuarter('cur');
    updateDisplayDates(quarter);
    const query2 = `(status:"Invoice Raised" OR status:"Approved") AND owners.id:87030 AND (employmentType:"Permanent" OR employmentType:"FTC (perm)")  AND NOT status:Archive AND dateAdded:[${dateToBh(quarter[0])} TO ${dateToBh(quarter[1])}]`;

    let quotaCurrent;
    let quotaData;

    fetch(`${restUrl}query/SalesQuota?fields=*&where=id IS NOT NULL AND owner.id=87030 AND period='Q4 2019'&orderBy=-percentAttained&count=500&BhRestToken=${token}`)
        .then(function (response) {
            return response.json();
        })
        .then((json) => quotaData = json)
        .then(function () {
            document.getElementById('quotaAnnual').innerHTML = `Annual Target: £${quotaData.data[0].quota}`;
            document.getElementById('quotaQuarter').innerHTML = `Quarterly Target: £${quotaData.data[0].quota / 4}`;

        })
        .then(fetch(`${restUrl}search/Placement?fields=${fields2.toString()}&sort=-dateAdded&start=0&count=25&query=${query2}&BhRestToken=${token}`)
            .then(function (response) {
                return response.json();
            })
            .then((json) => quotaCurrent = json)
            .then(function () {
                document.getElementById('welcome-msg').innerHTML = `Hello, ${quotaCurrent.data[0].owner.firstName} ${quotaCurrent.data[0].owner.lastName}`;

                document.getElementById('jobCount').innerHTML = `Number of placements: ${quotaCurrent.count}`;
                let val = 0;
                for (let placement of quotaCurrent.data) {
                    val += placement.flatFee;
                }
                document.getElementById('quotaCurrent').innerHTML = `Current Revenue: £${val}`;
                move((val / (quotaData.data[0].quota)) * 100, 'quotaPercentA');
                move((val / (quotaData.data[0].quota / 4)) * 100, 'quotaPercentQ');
            }));
}

function move(max, id) {
    var elem = document.getElementById(id);
    var width = 0;
    var id = setInterval(frame, 9);

    function frame() {
        if (width >= 100) {
            clearInterval(id);
        } else {
            if (width < max) {
                width++;
                elem.style.width = width + '%';
                elem.innerHTML = width * 1 + '%';
            }
        }
    }
}

function testFunction() {
    
}

function dateToBh(timestamp, endTime = '000000') {
    let time = new Date();
    let m = timestamp.getMonth() + 1;
    m = m > 10 ? m : '0' + m;
    let d = timestamp.getDate();
    d = d > 10 ? d : '0' + d;

    console.log(timestamp);
    return `${timestamp.getFullYear()}${m}${d}${endTime}`;
}

function getQuarter(when) {
    let d = new Date();
    let quarter = Math.floor((d.getMonth() / 3));


    switch (when) {
        case 'cur':
            let curDate = new Date(d.getFullYear(), quarter * 3, 1);
            return [
                curDate,
                new Date(curDate.getFullYear(), curDate.getMonth() + 3, 0)
            ];
        case 'prev':
            let prevDate = new Date(d.getFullYear(), quarter * 3 - 3, 1);
            return [
                prevDate,
                new Date(prevDate.getFullYear(), prevDate.getMonth() + 3, 0)
            ];
    }

    return range;
}

function setInner(id, content) {
    let elem = document.getElementById(id);
    elem.innerHTML = content;
}

function dateSuffix(number) {
    let suffix = 'th';

    switch (number) {
        case 1:
            suffix = 'st';
            break;
        case 2:
            suffix = 'nd';
            break;
        case 3:
            suffix = 'rd';
            break;
        case 21:
            suffix = 'st';
            break;
        case 22:
            suffix = 'nd';
            break;
        case 23:
            suffix = 'rd';
            break;
        case 31:
            suffix = 'st';
            break;
    }

    return `${number}${suffix}`;
}

function updateDisplayDates(dates) {

    month = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];
    let now = new Date();
    let qDisplay = 4;
    switch (now.getMonth()) {
        case 0:
        case 1:
        case 2:
            qDisplay = 1;
            break;
        case 3:
        case 4:
        case 5:
            qDisplay = 2;
            break;
        case 6:
        case 7:
        case 8:
            qDisplay = 3;
            break;
    }

    setInner('sub-welcome', `Q${qDisplay} - ${now.getFullYear()}`);

    console.log(`Q${qDisplay} ${now.getFullYear()}`);

    let start = `${dateSuffix(dates[0].getDate())} ${month[dates[0].getMonth()]}`;
    let end = `${dateSuffix(dates[1].getDate())} ${month[dates[1].getMonth()]}`;
    setInner('quarter-title', `${start} - ${end}`);
}

/**
 * Cookie Consent js
 * Simple popup dialog that checks presence of consent cookie.
 */

function cookieConsent() {
    if (checkConsent()) {
        console.info('cookie consent found.')
        document.getElementById('cookie-container').style.display = 'none';
        return;
    }

    console.info('consent cookie not found, showing consent.')

    document.getElementById('cookie-container').style.display = 'fixed';
}

function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}

function checkConsent() {
    return getCookie('consent') && new Date(getCookie('expires')) > new Date();
}

function closeCookie() {
    let expires = new Date();
    expires.setDate(expires.getDate() + 365);

    document.cookie = `consent=${'TRUE'};`;
    document.cookie = `expires=${expires.toUTCString()};`;

    console.log(`${getCookie('consent') == 'TRUE'}: consent set. expires success: ${getCookie('expires')}`);

    cookieConsent();
    return;
}

//https://cls23.bullhornstaffing.com/core/search/Opportunity?fields=id,title,clientContact(id,firstName,lastName),type,status,dealValue,dateAdded,owner(id,firstName,lastName),weightedDealValue,lead(id,firstName,lastName),expectedCloseDate,winProbabilityPercent,actualCloseDate,reasonClosed&sort=-dateAdded&start=0&count=25&query=isDeleted:0  AND status:%22Closed-Won%22 AND owner.name:(stephen AND brandsma) AND NOT status:Archive&showTotalMatched=true&showLabels=true&BhRestToken=d118f032-4fe9-43ec-aa5d-43a97eec7ad7&privateLabelId=22425