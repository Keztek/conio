function getMultiplayerData() {
    let contents = document.getElementById("offsetbox");

    fetch('data/Multiplayer.json', {
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
        .then(response => response.json())
        .then(data => {
            //console.log(data);
            for (let i = 0; i < data.Lobby.length; i++) {
                let roomId = Object.keys(data.Lobby[i])
                let roomData = Object.values(data.Lobby[i])[0];
                //console.log(roomData);
                contents.innerHTML += '<tr class="colrow"><td>' + roomId + '</td><td>' + roomData.Type + '</td><td></td><td>' + roomData.Players.length + '/' + roomData.MaxPlayers + '</td><td align="right"><a rel="popbox" href="?close=' + roomId + '">Close</a></td></tr>';
            }
            for (let i = 0; i < data.Game.length; i++) {
                let roomId = Object.keys(data.Game[i])
                let roomData = Object.values(data.Game[i])[0];
                //console.log(roomData);
                contents.innerHTML += '<tr class="colrow"><td>' + roomId + '</td><td>' + roomData.Type + '</td><td></td><td>' + roomData.Players.length + '/' + roomData.MaxPlayers + '</td><td align="right"><a rel="popbox" href="?close=' + roomId + '">Close</a></td></tr>';
            }
        })
        .catch(error => console.error('Error:', error));
}

document.addEventListener('DOMContentLoaded', function () {
    getMultiplayerData();
});