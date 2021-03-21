import {
    PAWN_SIZE,
    BOARD_MARGIN,
    PREETY_COLORS,
    MAIN_COLOR,
} from "../constants.js";
import PAWNS_POSITIONS from "../pawnPositions.js";

export default class Pawn {
    constructor(data, ownerId) {
        this.position = [0, 0];
        this.color_idx = data.color_index;
        this.loadPosition(data);
        this.id = data.id;

        this.button = null;

        this.ownerId = ownerId;
        this.canBeMoved = this.checkCanMovePawn(data);
        this.canMoveColor = MAIN_COLOR;
        this.live = true;
        // store optional instance of pawn what is hint where pawn be after move
        this.moveHint = null;
    }

    updatePawn(data) {
        this.loadPosition(data);
        this.canBeMoved = this.checkCanMovePawn(data);
        this.reMakeButton();
    }

    loadPosition(data) {
        if (data.in_home == 1) {
            this.position =
                PAWNS_POSITIONS.inHome[this.color_idx][data.position];
        } else if (data.out_of_board == 1) {
            this.position =
                PAWNS_POSITIONS.outOfBoard[this.color_idx][data.position];
        } else {
            this.position = PAWNS_POSITIONS.normal[data.position];
        }
    }

    render(ctx) {
        this.drawPawn(ctx);
        this.drawPawnHint(ctx);
        if (this.button == null){
            this.makeButton();
        }
    }

    drawPawn(ctx) {
        ctx.beginPath();
        if (this.position == undefined) {
            return false;
        }
        const x = BOARD_MARGIN + this.position[0];
        const y = this.position[1];
        ctx.arc(x, y, PAWN_SIZE, 0, 2 * Math.PI);
        ctx.strokeStyle = "#000";
        ctx.stroke();
        ctx.fillStyle = this.canBeMoved
            ? this.canMoveColor
            : PREETY_COLORS[this.color_idx];
        ctx.fill();
    }

    drawPawnHint(ctx){
        // draw pawn move int if can
        if (this.moveHint!= null){
            this.moveHint.drawPawn(ctx);
        }
    }

    // button stuff

    makeButton() {
        const block = document.querySelector("#gameBlock");
        this.button = document.createElement("button");
        this.button.classList.add("pawnButton");
        const x = BOARD_MARGIN + this.position[0] - PAWN_SIZE;
        const y = this.position[1] - PAWN_SIZE;
        this.button.style.left = x + "px";
        this.button.style.top = y + "px";
        block.appendChild(this.button);
        // add event handlers
        this.button.onclick = () => {
            this.handlePawnClick();
        };
        this.button.onmouseenter = () => {
            this.pawnMouseEnter();
        };
        this.button.onmouseleave = () => {
            this.pawnMouseLeave();
        };
    }

    reMakeButton = () => {
        this.button.remove();
        this.makeButton();
    }

    async handlePawnClick() {
        let path = `api/public/movePawn.php?pawn_id=${this.id}`;

        let res = await fetch(path);

        if (!res.ok) {
            console.error("Can not move pawn");
            alert("Can not move pawn");
        }
    }

    pawnMouseEnter() {
        this.madePawnHint();
    }

    pawnMouseLeave() {
        this.moveHint = null;
    }

    // pawn move/can move hints

    checkCanMovePawn = (data) => {
        if (data.can_be_moved != "1") {
            return false;
        }
        if (this.ownerId != data.player_id) {
            return false;
        }
        return true;
    };

    madePawnHint = async () => {
        // if is hint then not load twice
        if(this.moveHint != null){
            return false;
        }
        // if not can be moved, hint is not necessary
        if(!this.canBeMoved){
            return false;
        }
        // made hint by data from server
        const res = await fetch(`api/public/pawnAfterMove.php?pawn_id=${this.id}`);
        if (!res.ok){
            return false;
        }
        const data = await res.json();
        this.moveHint = new Pawn(data, this.ownerId);
        // force draw hint
        const canvas = document.querySelector("#gameCanvas");
        const ctx = canvas.getContext("2d");
        this.drawPawnHint(ctx);
    }
}
