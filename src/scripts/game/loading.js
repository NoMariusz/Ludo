import { READY_TEXT, WAITING_TEXT } from "../constants.js";

// loadingGame
let live = true

const startLoading = async () => {
    while (live){
        await sleep(1500);
        await loadGame();
    }
}

const loadGame = async () => {
    // get data from backend
    const data = await getDataFromServer();
    console.log("Data from server: ", data);
    // if data null then make proper actions
    if (data == null) {
        alert("Can not load game, please be sure you are logged");
        return false;
    }

    // if data is, then load it to game
    loadDataToGame(data);
};

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

const getDataFromServer = async () => {
    const res = await fetch("api/game/getGameData.php");
    if (!res.ok) {
        console.error("Game data response not ok");
        return null;
    }

    const resData = await res.json();
    return resData;
};

const loadDataToGame = (data) => {
    // load players
    loadPlayersBlock(data);
    // load ready slider
    loadReadySlider(data);
};

const loadPlayersBlock = (data) => {
    const playersPin = document.querySelectorAll(".playerPin");
    for (
        let playerIndex = 0;
        playerIndex < data.players.length;
        playerIndex++
    ) {
        loadDataToPin(playersPin[playerIndex], data.players[playerIndex]);
    }
};

const loadDataToPin = (pin, playerData) => {
    pin.innerText = playerData.nick;
    pin.classList.remove("gray");
    pin.classList.add(playerData.color);
};

const loadReadySlider = (data) => {
    const isReadySwitch = document.getElementById("isReadySwitch");
    const readyText = document.getElementById("readyText");
    const playerData = getMainPlayerData(data);

    const playerWaiting = playerData.status == 0;
    // for slider
    isReadySwitch.checked = !playerWaiting;
    isReadySwitch.disabled = !playerWaiting;
    // for player text
    readyText.innerText = playerWaiting ? WAITING_TEXT : READY_TEXT;
};

const getMainPlayerData = (data) => {
    return data.players.find((e) => e.id == data.player_id);
};

export default startLoading;
