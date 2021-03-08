import { READY_TEXT, WAITING_TEXT } from "../constants.js";


export default class GameRefresher{
    refresh = async () => {
        // get data from backend
        const data = await this.getDataFromServer();
        console.log("Data from server: ", data);
        // if data null then make proper actions
        if (data == null) {
            // redirect to game
            window.location.href = window.location.href.slice(0, -8);
            return false;
        }
    
        // if data is, then load it to game
        this.loadDataToGame(data);
    };
    
    getDataFromServer = async () => {
        const res = await fetch("api/game/getGameData.php");
        if (!res.ok) {
            console.error("Game data response not ok");
            return null;
        }
    
        const resData = await res.json();
        return resData;
    };
    
    loadDataToGame = (data) => {
        // load players
        this.loadPlayersBlock(data);
        // load ready slider
        this.loadReadySlider(data);
    };

    // loading players block
    
    loadPlayersBlock = (data) => {
        const playersPin = document.querySelectorAll(".playerPin");
        for (
            let playerIndex = 0;
            playerIndex < data.players.length;
            playerIndex++
        ) {
            this.loadDataToPin(playersPin[playerIndex], data.players[playerIndex]);
        }
    };
    
    loadDataToPin = (pin, playerData) => {
        pin.innerText = playerData.nick;
        pin.classList.remove("gray");
        pin.classList.add(playerData.color);
    };

    // loading ready slider
    
    loadReadySlider = (data) => {
        const isReadySwitch = document.getElementById("isReadySwitch");
        const readyText = document.getElementById("readyText");
        const playerData = this.getMainPlayerData(data);
    
        const playerWaiting = playerData.status == 0;
        // for slider
        isReadySwitch.checked = !playerWaiting;
        isReadySwitch.disabled = !playerWaiting;
        // for player text
        readyText.innerText = playerWaiting ? WAITING_TEXT : READY_TEXT;
    };
    
    getMainPlayerData = (data) => {
        return data.players.find((e) => e.id == data.player_id);
    };

    // loading time noticer
}