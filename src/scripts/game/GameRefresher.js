import { READY_TEXT, WAITING_TEXT, TURN_TIME } from "../constants.js";


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
        // load displaying time
        this.loadTimeNoticer(data);
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
        // add p tag
        pin.innerHTML = "";
        const p = document.createElement("p")
        p.innerText = playerData.nick;
        pin.appendChild(p);
        // add kolor
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

    loadTimeNoticer = (data) => {
        // prepare data
        const playerWithTurnIdx = data.players.findIndex(p => p.status > 2);
        // if nobody have turn not render noticer
        if (playerWithTurnIdx == -1){
            return false;
        }
        const playersPin = document.querySelectorAll(".playerPin");
        console.log(playersPin, playerWithTurnIdx);
        const pin = playersPin[playerWithTurnIdx];
        // make noticer
        const noticer = this.makeNoticer();
        // add to html and set content
        pin.appendChild(noticer);
        const milisecondsLeft = Date.now() - Date.parse(data.game.turn_start_time)
        const secondsLeft = Math.round(milisecondsLeft / 1000)
        noticer.innerText = TURN_TIME - secondsLeft;
    }

    makeNoticer = () => {
        const noticer = document.createElement("div");
        noticer.classList.add("baseNoticer");
        noticer.classList.add("timeNoticer");
        return noticer;
    }
}