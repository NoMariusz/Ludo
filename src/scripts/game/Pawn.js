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
        this.ownerId = ownerId;
        this.canBeMoved = this.checkCanMovePawn(data);
    }

    updatePawn(data) {
        this.loadPosition(data);
        this.canBeMoved = this.checkCanMovePawn(data);
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
            ? MAIN_COLOR
            : PREETY_COLORS[this.color_idx];
        ctx.fill();

        this.makeButton();
    }

    makeButton() {
        const block = document.querySelector("#gameBlock");
        let btn = document.createElement("button");
        btn.classList.add("pawnButton");
        const x = BOARD_MARGIN + this.position[0] - PAWN_SIZE;
        const y = this.position[1] - PAWN_SIZE;
        btn.style.left = x + "px";
        btn.style.top = y + "px";
        block.appendChild(btn);
        btn.onclick = () => {
            this.handlePawnClick();
        };
    }

    async handlePawnClick() {
        let path = `api/public/movePawn.php?pawn_id=${this.id}`;

        let res = await fetch(path);

        if (!res.ok) {
            console.error("Can not move pawn");
            alert("Can not move pawn");
        }
    }

    checkCanMovePawn = (data) => {
        if (data.can_be_moved != "1") {
            return false;
        }
        if (this.ownerId != data.player_id) {
            return false;
        }
        return true;
    };
}
