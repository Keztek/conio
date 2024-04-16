const baseUrl = '/../../api/Database/';

const urlParams = new URLSearchParams(window.location.search);
const database = urlParams.get('db');

function createDatabase(tableName) {
    fetch(baseUrl + 'createDatabase.php', {
        method: 'POST',
        body: new URLSearchParams({ 'tableName': tableName }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
        .then(response => response.json())
        .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
}

function deleteDatabase(tableName) {
    fetch(baseUrl + 'deleteDatabase.php', {
        method: 'POST',
        body: new URLSearchParams({ 'tableName': tableName }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
        .then(response => response.json())
        .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
}

function containsObject(tableName, objectName) {
    fetch(baseUrl + 'containsObject.php', {
        method: 'POST',
        body: new URLSearchParams({ 'tableName': tableName, 'objectName': objectName }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
        .then(response => response.json())
        .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
}

function createDatabaseObject(tableName, objectName, objectData) {
    fetch(baseUrl + 'createDatabaseObject.php', {
        method: 'POST',
        body: new URLSearchParams({ 'tableName': tableName, 'objectName': objectName, 'objectData': JSON.stringify(objectData) }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
        .then(response => response.json())
        .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
}

function updateDatabaseObject(tableName, objectName, newData) {
    fetch(baseUrl + 'updateDatabaseObject.php', {
        method: 'POST',
        body: new URLSearchParams({ 'tableName': tableName, 'objectName': objectName, 'newData': JSON.stringify(newData) }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
        .then(response => response.json())
        .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
}

function deleteDatabaseObject(tableName, objectName) {
    fetch(baseUrl + 'deleteDatabaseObject.php', {
        method: 'POST',
        body: new URLSearchParams({ 'tableName': tableName, 'objectName': objectName }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
        .then(response => response.json())
        .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
}

function getDatabaseObject(tableName, objectName) {
    fetch(baseUrl + 'getDatabaseObject.php', {
        method: 'POST',
        body: new URLSearchParams({ 'tableName': tableName, 'objectName': objectName }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
        .then(response => response.json())
        .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
}

function getDatabaseObjects(tableName) {
    let contents = document.getElementById("bigdbsearchresults");
    fetch(baseUrl + 'getDatabaseObjects.php', {
        method: 'POST',
        body: new URLSearchParams({ 'tableName': tableName }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
        .then(response => response.json())
        .then(data => {
            let keys = Object.keys(data[0]);
            let values = Object.values(data[0]);
            for (let i = 0; i < keys.length; i++) {
                let dbo = '<div class="bigdbobject canchange"><div class="objectactions" ><img class="addbigdbproperty" src="img/icon-add.gif" title="Add Property"><img class="deletebigdbobject" src="img/icon-delete.gif" title="Delete Object"></div><b class="objectkey">%key%</b><table><tbody>';
                dbo = dbo.replace('%key%', keys[i]);
                let objKeys = Object.keys(data[0][keys[i]]);
                let objValues = Object.values(data[0][keys[i]]);
                for (let i2 = 0; i2 < objKeys.length; i2++) {
                    dbo += '<tr id="valueRow" class="bigdbrow"><th class="rowname">%objkey%</th><td class="value plainvalue">%objval%</td><td class="clicktoedit" style="width:75px">click to edit</td></tr>';
                    dbo = dbo.replace('%objkey%', objKeys[i2]);
                    dbo = dbo.replace('%objval%', objValues[i2]);
                }
                dbo += '</tbody></table><div class="savebigdbchanges"><a class="button positive savebigdbchanges"><img class="" src="img/tick.png" title="Save Changes">Save Changes</a></div></div>';
                contents.innerHTML += dbo;
            }
        })
        .catch(error => console.error('Error:', error));
}

function getDatabases() {
    let contents = document.getElementById("db-list");
    fetch(baseUrl + 'getDatabases.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
        .then(response => response.json())
        .then(data => {
            for (let i = 0; i < data[0].length; i++) {
                if (database == data[0][i]) {
                    contents.innerHTML += '<li><a href="?db=' + data[0][i] + '" class="active">' + data[0][i] + '</a></li>';
                } else {
                    contents.innerHTML += '<li><a href="?db=' + data[0][i] + '">' + data[0][i] + '</a></li>';
                }
            }
        })
        .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', function () {
    //getDatabaseObject("Users", "70f8cdf1-7916-41fa-9d29-b55872842273");
    getDatabases();
});