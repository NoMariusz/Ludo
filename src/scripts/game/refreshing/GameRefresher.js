import Constants from "../../Constants.js";
import Pawn from "./Pawn.js";

export default class GameRefresher {
    constructor() {
        this.pawns = [];
    }

    refresh = async () => {
        // get data from backend
        const data = await this.getDataFromServer();
        console.log("Data from server: ", data);
        // if data null or game ended make proper actions
        if (!this.checkIfDataGood(data)) {
            // redirect to login
            window.location.href += "login.php" 
            return false;
        }

        // if data is, then load it to game
        this.loadDataToGame(data);
    };

    getDataFromServer = async () => {
        const res = await fetch("api/public/getGameData.php");
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
        // load board
        this.loadBoard(data);
        // load cube
        this.loadCube(data);
        // load throwButton displaying
        this.loadThrowButton(data);
    };

    // loading players block

    loadPlayersBlock = (data) => {
        const playersPin = document.querySelectorAll(".playerPin");
        for (
            let playerIndex = 0;
            playerIndex < data.players.length;
            playerIndex++
        ) {
            this.loadDataToPin(
                playersPin[playerIndex],
                data.players[playerIndex],
                data.player_id
            );
        }
    };

    loadDataToPin = (pin, playerData, mainPlaierId) => {
        // MADE p tag
        pin.innerHTML = "";
        const p = document.createElement("p");
        pin.appendChild(p);
        // set text
        p.innerText = playerData.nick;
        // add color
        pin.classList.remove("gray");
        pin.classList.add(Constants.COLORS[playerData.color_index]);
        // add other styling if pin belong to actual user
        if (playerData.id == mainPlaierId) {
            pin.classList.add("mainPlayerPin");
        }
        this.loadPlaceNoticer(pin, playerData);
    };

    loadPlaceNoticer = (pin, playerData) => {
        // if player not have place then not do anything
        if (playerData["place"] == null) {
            return false;
        }
        const noticer = this.makeNoticer("placeNoticer");
        pin.appendChild(noticer);
        // add information about taken place
        noticer.innerText = playerData["place"];
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
        readyText.innerText = playerWaiting
            ? Constants.WAITING_TEXT
            : Constants.READY_TEXT;
    };

    getMainPlayerData = (data) => {
        return data.players.find((e) => e.id == data.player_id);
    };

    // loading time noticer

    loadTimeNoticer = (data) => {
        // prepare data
        const playerWithTurnIdx = data.players.findIndex(
            (p) => p.status > 2 && p.status != 5
        );
        // if nobody have turn not render noticer
        if (playerWithTurnIdx == -1) {
            return false;
        }
        const playersPin = document.querySelectorAll(".playerPin");
        const pin = playersPin[playerWithTurnIdx];
        // make noticer
        const noticer = this.makeNoticer("timeNoticer");
        // add to html and set content
        pin.appendChild(noticer);
        const milisecondsLeft =
            Date.now() - Date.parse(data.game.turn_start_time);
        const secondsLeft = Math.round(milisecondsLeft / 1000);
        noticer.innerText = Math.max(Constants.TURN_TIME - secondsLeft, 0);
    };

    makeNoticer = (type) => {
        const noticer = document.createElement("div");
        noticer.classList.add("baseNoticer");
        noticer.classList.add(type);
        return noticer;
    };

    // loading board

    loadBoard = async (data) => {
        const canvas = document.querySelector("#gameCanvas");
        const ctx = canvas.getContext("2d");
        // load board image
        await this.loadBoardImage(ctx);
        // load pawns
        this.loadPawns(data, ctx);
    };

    loadBoardImage = async (ctx) => {
        return new Promise((resolv, reject) => {
            ctx.clearRect(
                Constants.BOARD_MARGIN,
                0,
                Constants.BOARD_SIZE,
                Constants.BOARD_SIZE
            );
            const img = new Image();
            img.onload = () => {
                ctx.drawImage(
                    img,
                    Constants.BOARD_MARGIN,
                    0,
                    Constants.BOARD_SIZE,
                    Constants.BOARD_SIZE
                );
                resolv(true);
            };
            img.src = "./src/gfx/board.svg";
        });
    };

    loadPawns = (data, ctx) => {
        // modify pawns list
        this.updatePawns(data);
        // render pawns
        this.pawns.forEach((pawn) => {
            pawn.render(ctx);
        });
    };

    updatePawns = (data) => {
        /* update pawns or create new */
        data.pawns.forEach((pawn) => {
            const pawnClass = this.pawns.find((e) => e.id == pawn.id);
            if (pawnClass) {
                // if pawn exist only load new data
                pawnClass.updatePawn(pawn);
            } else {
                // if pawn from data is not in pawns add them
                const pawnClass = new Pawn(pawn, data.player_id);
                this.pawns.push(pawnClass);
            }
        });
    };

    // diplaying cube and points

    loadCube = async (data) => {
        const points = data.game.last_throw_points;
        const canvas = document.querySelector("#gameCanvas");
        const ctx = canvas.getContext("2d");
        const isCubeThrowed = data.game.throwed_cube == 1;
        this.loadDiceImage(ctx, points, isCubeThrowed);
    };

    loadDiceImage = async (ctx, points, show) => {
        return new Promise((resolv, reject) => {
            // clear old dice
            const xy = (Constants.BOARD_MARGIN - Constants.DICE_SIZE) / 2;
            ctx.clearRect(xy, xy, Constants.DICE_SIZE, Constants.DICE_SIZE);
            // end work if should not show
            if (!show) {
                resolv(true);
                return false;
            }
            // draw dice image
            const img = new Image();
            img.onload = () => {
                ctx.drawImage(
                    img,
                    xy,
                    xy,
                    Constants.DICE_SIZE,
                    Constants.DICE_SIZE
                );
                resolv(true);
            };
            img.src = `./src/gfx/cubes/${points}.jpg`;
        });
    };

    // change visibility of throwCube button

    loadThrowButton = (data) => {
        const throwCube = document.getElementById("throwCube");
        if (this.shouldThrowBtnDisplayed(data)) {
            throwCube.classList.remove("hidden");
        } else {
            throwCube.classList.add("hidden");
        }
    };

    shouldThrowBtnDisplayed = (data) => {
        if (data.game.throwed_cube == 1) {
            return false;
        }
        const mainPlayer = this.getPlayerById(data, data.player_id);
        if (mainPlayer.status != 3) {
            return false;
        }
        return true;
    };

    // utils

    getPlayerById = (data, playerId) => {
        return data.players.find((e) => e.id == playerId);
    };

    checkIfDataGood = (data) => {
        return data != null && data.game.length != 0 && data.game.status != 2;
    };
}
