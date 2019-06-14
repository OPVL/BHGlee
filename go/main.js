let eg = `/OpenWindow.cfm?entity=Candidate&id=12345&view=Activity&expandedSection=Interviews`;
// const token = '5fc4b5a4-be0e-4fe6-a7bc-b539630ba7b7';

let token = false;
let restUrl = 'https://rest23.bullhornstaffing.com/rest-services/3rn5us/';
/**
 * Entity	            Call
 * Candidate:	        /OpenWindow.cfm?entity=Candidate&view=AddNote           <<---/                                             \
 * ClientContact:	    /OpenWindow.cfm?entity=ClientContact&view=AddNote       <<--|    These are the views we're interested in.   |
 * ClientCorporation:	/OpenWindow.cfm?entity=ClientCorporation&view=AddNote   <<---\                                             /
 * JobOrder:	        /OpenWindow.cfm?entity=JobOrder&view=AddNote
 * Placement:	        /OpenWindow.cfm?entity=Placement&view=AddNote
 * Opportunity:	        /OpenWindow.cfm?entity=Opportunity&view=AddNote
 * Lead:	            /OpenWindow.cfm?entity=Lead&view=AddNote 
 */

const dropDownOptions = [
    "Active Job Call",
    "Client Visit Attended",
    "Client Visit Booked",
    "Client Gleeview Meeting",
    "BD Call",
    "BD Email",
    "BD Gleeview",
    "1st Interview",
    "Gleeview 1st Intv",
    " Gleeview 2nd Intv",
    "Congrats Card Sent",
    "Email",
    "Feedback",
    "Icetrak SMS",
    "Inbound Call",
    "Keep In Touch Sent",
    "Left Message",
    "Other",
    "Outbound Call",
    "Pre-screen Booked",
    "Prescreen Attended",
    "Prescreen Gleeview Attended",
    "Spec CV sent",
    "2nd Interview",
    "Final Interview",
    "Event: Invite Accepted",
    "Event: Attended",
    "Consent Requested",
    "GDPR Access/Removal Request",
    "Aftercare Call",
    "TOB Sent"
];

const createRow = function (target) {
    return createElem(target, 'row');
}

function createCol(row, size = 6) {
    return createElem(row, `col-md-${size}`);
}

function createElem(parent, className, elemType = 'div') {
    let div = document.createElement(elemType);
    div.setAttribute('class', className);
    parent.appendChild(div);
    return div;
}

function createNotesForm(parent, entityId) {

    for (const child of parent.children) {
        if (child.nodeName == 'FORM') {
            parent.removeChild(child);
            return null;
        }
    }
    let form = createElem(parent, '', 'form');
    form.setAttribute('action', 'submit/');
    form.setAttribute('method', 'POST');

    /**
     * Entity ID hidden input box
     */
    let hBox = createElem(form, 'form-group');
    let hLabel = createElem(hBox, 'sr-only', 'label');
    hLabel.setAttribute('for', 'userID');
    hLabel.innerText = 'UserID';
    let hInput = createElem(hBox, 'form-control', 'input');
    hInput.setAttribute('type', 'number');
    hInput.setAttribute('name', 'userID');
    hInput.setAttribute('value', entityId);
    hInput.setAttribute('id', 'userId')
    hInput.hidden = true;

    /**
     * Action Dropdown uses loop of dropdown options
     */
    let sBox = createElem(form, 'form-group');
    let sLabel = createElem(sBox, '', 'label');
    sLabel.setAttribute('for', 'action');
    sLabel.innerText = 'Action: ';
    let sSelect = createElem(sBox, 'form-control', 'select');
    sSelect.setAttribute('required', 'true');
    sSelect.setAttribute('name', 'action');
    sSelect.setAttribute('onchange', 'console.log(value)');
    for (const action of dropDownOptions) {
        let option = createElem(sSelect, '', 'option');
        option.innerHTML = action;
        option.setAttribute('value', action);
    }

    /**
     * Notes Area (aka. 'Comments')
     */
    let nBox = createElem(form, 'form-group');
    let nLabel = createElem(nBox, '', 'label');
    nLabel.setAttribute('for', 'notesArea');
    nLabel.innerText = 'Notes: ';
    let nField = createElem(nBox, 'form-control', 'textarea');
    nField.setAttribute('name', 'notesArea');
    nField.setAttribute('id', 'notesArea');
    nField.setAttribute('rows', '4');

    /**
     * Submit Button
     */
    let submitButton = createElem(form, 'btn btn-primary btn-block', 'button');
    submitButton.setAttribute('type', 'submit');
    submitButton.innerHTML = 'Submit';

    return form;
}

function createList(parent, collection, count) {
    let card = createElem(parent, 'card');
    createElem(card, 'card-header').innerText = `Returned ${count} Results`;

    console.log(collection);
    let firstLoop = true;
    for (const group of collection) {

        // don't iterate empty returns
        if (group.entities.length == 0) {
            continue;
        }

        // create header
        let catHeader = createElem(card, 'card-header', 'button');
        catHeader.innerHTML = `${group.niceName} <span class="badge badge-primary badge-pill">${group.entities.length}</span>`;
        catHeader.setAttribute('data-toggle', 'collapse');
        catHeader.setAttribute('data-target', `#CardBody${group.name}`);
        catHeader.setAttribute('aria-expanded', firstLoop ? 'true' : 'false');
        catHeader.setAttribute('aria-controls', `CardBody${group.name}`);
        catHeader.style = 'border: none';

        // create body
        let entBody = createElem(card, `collapse`);

        // always make the first result expanded
        if (firstLoop) {
            entBody.setAttribute('class', 'show');
            console.log(group.name);
            firstLoop = false;
        }
        entBody.setAttribute('id', `CardBody${group.name}`);
        let list = createElem(entBody, 'list-group');

        //iterate through group and populate list
        for (const entity of group.entities) {
            createListEntry(list, entity);
        }
    }
}

function createListEntry(parent, entity) {
    // create accordian wrapper div
    let wrapper = createElem(parent, 'list-group-item');


    let line = createElem(wrapper, 'card-text flex-row', 'a');
    line.style = 'min-width: 450px; display: flex!important; justify-content: space-between;';

    let entry = createElem(line, 'text-dark', 'a');
    entry.setAttribute('data-toggle', 'collapse');
    entry.setAttribute('href', `#collapse${entity.entityId}`);
    entry.setAttribute('aria-expanded', 'false');
    entry.setAttribute('aria-controls', `collapse${entity.entityId}`);
    entry.innerText = entity.title;
    entry.style = 'margin: 1rem';

    let infoButton = createElem(line, 'btn btn-warning text-dark', 'a');
    infoButton.setAttribute('href', `https://www.bullhornstaffing.com/BullhornStaffing/OpenWindow.cfm?entity=${entity.entityType}&id=${entity.entityId}&view=Activity`);
    infoButton.innerHTML = '<i class="fas fa-info"></i>';
    infoButton.style = 'height: 30px; width: 69px; padding: inherit; margin: auto 0';

    // create collapsible div wrapper
    let collapse = createElem(wrapper, 'collapse');
    collapse.setAttribute('id', `collapse${entity.entityId}`);
    createElem(collapse, 'my-2', 'hr');
    createNotesForm(collapse, entity.entityId);
}

// function getCookie(cname) {
//     var name = cname + "=";
//     var decodedCookie = decodeURIComponent(document.cookie);
//     var ca = decodedCookie.split(';');
//     for (var i = 0; i < ca.length; i++) {
//         var c = ca[i];
//         while (c.charAt(0) == ' ') {
//             c = c.substring(1);
//         }
//         if (c.indexOf(name) == 0) {
//             return c.substring(name.length, c.length);
//         }
//     }
//     return "";
// }

function noResults(page){
    let card = createElem(page, 'card');
    createElem(card, 'card-header').innerText = `No Results`;
}

function handleResponse(term) {
    let sorted = [{
            "name": "ClientCorporation",
            "entities": [],
            "niceName": "Companies"
        },
        {
            "name": "Candidate",
            "entities": [],
            "niceName": "Candidates"
        },
        {
            "name": "ClientContact",
            "entities": [],
            "niceName": "Contacts"
        },
        {
            "name": "JobOrder",
            "entities": [],
            "niceName": "Job Orders"
        },
        {
            "name": "Placement",
            "entities": [],
            "niceName": "Placements"
        },
        {
            "name": "Opportunity",
            "entities": [],
            "niceName": "Opportunities"
        },
        {
            "name": "Lead",
            "entities": [],
            "niceName": "Leads"
        },
    ];
    let count = 0;

    console.log(term);

    const getInfo = async () => {
        fetch(`${restUrl}find?query=${term}&BhRestToken=${token}`)
            .then(function (response) {
                return response.json();
            })
            .then(function (json) {

                console.log(json);

                if (!json.errorMessage){
                    for (const entity of json.data) {
                        // see if entityType already exists in array
                        for (const group of sorted) {
                            if (group.name == entity.entityType) {
                                group.entities.push(entity);
                                count++;
                                break;
                            }
                        }
                    }
                    createList(document.getElementById('mainPage'), sorted, count);
                    return;
                }

                noResults(document.getElementById('mainPage'));
                return;
            });
    }

    // const getToken = async (time, next) => {

    //     let refresh = getCookie('refresh_token') || null;
    //     refresh = `?refresh=${refresh}`;
        
    //     const response = await fetch(`http://localhost/TestDump/Gleetest/resources/${refresh}`);
    //     json = await response.json();
    //     token = json.BhRestToken;
    //     restUrl = json.restUrl;

    //     time.setMinutes(time.getMinutes() + 10);
    //     time = time.toUTCString();

    //     document.cookie = `BhRestToken=${json.BhRestToken}; expires=${time}; path=/`;
    //     document.cookie = `restUrl=${json.restUrl}; max-age=${60*60*24*30}; path=/`;
    //     document.cookie = `refresh_token=${json.refresh_token}; max-age=${60*60*24*30}; path=/`;

    //     console.log(json);

    //     getInfo();
    // }

    token = getCookie('BhRestToken');
    if (!token) {
        //BhRestToken is valid for 10 minutes
        console.log('getting token');
        token = getToken(new Date());
    }
    getInfo();
}

/**
 * TODO:
 *  Create PUT logic for placing comments directly on to entites from within the go integrator dashboard-y thing.
 *  Once this have been achieved I can sign it off as the MVP and work on the logic/authorization flow 
 * 
 *  Authorization flow uses REST OAuth2 method, use secret to get token (scope permissions if necessary) -> use token to execute queries 
 *  Can use scumbag method of BhRestToken however this is not ideal as this has a limited expiry and grabbing cookies from other tabs is technically an exploit
 */
// PUT -> https://rest9.bullhornstaffing.com/rest-services/3rn5us/entity/Note?BhRestToken=${token}
// BODY: \/ (JSON)
let noteFormat = {
    "commentingPerson": {
        "id": "2"
    },
    "candidates": [{
        "id": "4"
    }],
    "comments": "This is note",
    "personReference": {
        "id": "2"
    }
}