import Constants from "../../Constants.js";

export default class Pawn {
    constructor(data, ownerId) {
        this.position = [0, 0];
        this.color_idx = data.color_index;
        this.loadPosition(data);
        this.id = data.id;

        this.button = null;

        this.ownerId = ownerId;
        this.canBeMoved = this.checkCanMovePawn(data);
        this.canMoveColor = Constants.MAIN_COLOR;
        this.live = true;
        // store optional instance of pawn what is hint where pawn be after move
        this.moveHint = null;
        // to check if pawn is hovering
        this.hovering = false;
    }

    updatePawn(data) {
        let changedPos = this.loadPosition(data);
        let startCanBeMoved = this.canBeMoved;
        this.canBeMoved = this.checkCanMovePawn(data);
        // if pawn data changed remade button
        if (changedPos || startCanBeMoved != this.canBeMoved) {
            this.reMakeButton();
        }
    }

    loadPosition(data) {
        const startPosition = [...this.position];
        if (data.in_home == 1) {
            this.position =
                Constants.PAWNS_POSITIONS.inHome[this.color_idx][data.position];
        } else if (data.out_of_board == 1) {
            this.position =
                Constants.PAWNS_POSITIONS.outOfBoard[this.color_idx][data.position];
        } else {
            this.position = Constants.PAWNS_POSITIONS.normal[data.position];
        }
        // return if position change
        return (
            startPosition[0] == this.position[0] &&
            startPosition[1] == this.position[1]
        );
    }

    render(ctx) {
        this.drawPawn(ctx);
        this.drawPawnHint(ctx);
    }

    drawPawn(ctx) {
        ctx.beginPath();
        if (this.position == undefined) {
            return false;
        }
        const x = Constants.BOARD_MARGIN + this.position[0];
        const y = this.position[1];
        ctx.arc(x, y, Constants.PAWN_SIZE, 0, 2 * Math.PI);
        ctx.strokeStyle = "#000";
        ctx.stroke();
        ctx.fillStyle = this.canBeMoved
            ? this.canMoveColor
            : Constants.PREETY_COLORS[this.color_idx];
        ctx.fill();
    }

    drawPawnHint(ctx) {
        // draw pawn move int if can
        this.moveHint?.drawPawn(ctx);
    }

    // button stuff

    makeButton() {
        // make button
        const block = document.querySelector("#gameBlock");
        this.button = document.createElement("button");
        block.appendChild(this.button);
        // style button
        this.button.classList.add("pawnButton");
        const x =
            Constants.BOARD_MARGIN + this.position[0] - Constants.PAWN_SIZE;
        const y = this.position[1] - Constants.PAWN_SIZE;
        this.button.style.left = x + "px";
        this.button.style.top = y + "px";
        if (this.canBeMoved) {
            this.button.classList.add("activeBtn");
        }
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
        this.button?.remove();
        this.makeButton();
    };

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
        this.hovering = true;
    }

    pawnMouseLeave() {
        this.moveHint = null;
        this.hovering = false;
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
        if (this.moveHint != null) {
            return false;
        }
        // if not can be moved, hint is not necessary
        if (!this.canBeMoved) {
            return false;
        }
        // made hint by data from server
        const res = await fetch(
            `api/public/pawnAfterMove.php?pawn_id=${this.id}`
        );
        if (!res.ok) {
            return false;
        }
        const data = await res.json();
        // if hover end while getting data, not make hint
        if (!this.hovering) {
            return false;
        }
        this.moveHint = new Pawn(data, this.ownerId);
        // force draw hint
        const canvas = document.querySelector("#gameCanvas");
        const ctx = canvas.getContext("2d");
        this.drawPawnHint(ctx);
    };
}
