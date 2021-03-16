import {
    READY_TEXT,
    WAITING_TEXT,
    TURN_TIME,
    COLORS,
    BOARD_MARGIN,
    BOARD_SIZE,
    PAWNS_COUNT,
    DICE_SIZE,
} from "../constants.js";
import Pawn from "./Pawn.js";

export default class GameRefresher {
    constructor() {
        this.pawns = [];
        this.pawnsLoaded = false;
    }

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
        // load board
        this.loadBoard(data);
        // load cube
        this.loadCube(data);
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
                data.players[playerIndex]
            );
        }
    };

    loadDataToPin = (pin, playerData) => {
        // add p tag
        pin.innerHTML = "";
        const p = document.createElement("p");
        p.innerText = playerData.nick;
        pin.appendChild(p);
        // add kolor
        pin.classList.remove("gray");
        pin.classList.add(COLORS[playerData.color_index]);
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
        const playerWithTurnIdx = data.players.findIndex((p) => p.status > 2);
        // if nobody have turn not render noticer
        if (playerWithTurnIdx == -1) {
            return false;
        }
        const playersPin = document.querySelectorAll(".playerPin");
        const pin = playersPin[playerWithTurnIdx];
        // make noticer
        const noticer = this.makeNoticer();
        // add to html and set content
        pin.appendChild(noticer);
        const milisecondsLeft =
            Date.now() - Date.parse(data.game.turn_start_time);
        const secondsLeft = Math.round(milisecondsLeft / 1000);
        noticer.innerText = TURN_TIME - secondsLeft;
    };

    makeNoticer = () => {
        const noticer = document.createElement("div");
        noticer.classList.add("baseNoticer");
        noticer.classList.add("timeNoticer");
        return noticer;
    };

    // loading board

    loadBoard = async (data) => {
        const canvas = document.querySelector("#gameCanvas");
        const ctx = canvas.getContext("2d");
        // load board image
        await this.loadBoardImage(ctx);
        // load pawns
        this.loadPawns(data.pawns, ctx);
    };

    loadBoardImage = async (ctx) => {
        return new Promise((resolv, reject) => {
            ctx.clearRect(BOARD_MARGIN, 0, BOARD_SIZE, BOARD_SIZE);
            const img = new Image();
            img.onload = () => {
                ctx.drawImage(img, BOARD_MARGIN, 0, BOARD_SIZE, BOARD_SIZE);
                resolv(true);
            };
            img.src = "./src/gfx/board.svg";
        })
    };

    loadPawns = (pawns, ctx) => {
        // modify pawns list
        if (this.pawnsLoaded) {
            this.updatePawns(pawns);
        } else {
            this.makePawns(pawns);
            this.pawnsLoaded = true;
        }
        // render pawns
        this.pawns.forEach(pawn => {
            pawn.render(ctx);
        });
    };

    makePawns = (pawns) => {
        this.pawns = [];
        pawns.forEach((pawn) => {
            const pawnClass = new Pawn(
                pawn.id,
                pawn.position,
                pawn.color_index,
                false,
                pawn.out_of_board,
                pawn.in_home,
            );
            this.pawns.push(pawnClass);
        });
    };

    updatePawns = (pawns) => {
        pawns.forEach((pawn) => {
            const pawnClass = this.pawns.find((e) => e.id == pawn.id);
            if (pawnClass) {
                pawnClass.loadPosition(
                    pawn.position,
                    pawn.out_of_board,
                    pawn.in_home,
                );
            }
        });
    };

    // diplaying cuba and points

    loadCube = async (data) => {
        const points = data.game.last_throw_points;
        const canvas = document.querySelector("#gameCanvas");
        const ctx = canvas.getContext("2d");
        const isCubeThrowed = data.game.throwed_cube == 1;
        this.loadDiceImage(ctx, points, isCubeThrowed);
    }

    loadDiceImage = async (ctx, points, show) => {
        return new Promise((resolv, reject) => {
            const xy = (BOARD_MARGIN - DICE_SIZE)/2
            ctx.clearRect(xy, xy, DICE_SIZE, DICE_SIZE);
            if (!show){
                resolv(true);
                return false;
            }
            const img = new Image();
            img.onload = () => {
                ctx.drawImage(img, xy, xy, DICE_SIZE, DICE_SIZE);
                resolv(true);
            };
            img.src = `./src/gfx/cubes/${points}.jpg`;
        })
    };
}
