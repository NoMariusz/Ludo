import { PAWN_SIZE, COLORS, BOARD_MARGIN } from "../constants.js";
import PAWNS_POSITIONS from "../pawnPositions.js";


export default class Pawn{
    constructor(id, position_idx, color_idx, active, outOfBoard, inHome){
        this.position = [0, 0];
        this.color_idx = color_idx;
        this.loadPosition(position_idx, outOfBoard, inHome);
        this.active = active;
        this.id = id
    }

    loadPosition(position_idx, outOfBoard, inHome){
        if(inHome == 1){
            this.position = PAWNS_POSITIONS.inHome[this.color_idx][position_idx];
        } else if (outOfBoard == 1){
            this.position = PAWNS_POSITIONS.outOfBoard[this.color_idx][position_idx];
        } else {
            this.position = PAWNS_POSITIONS.normal[position_idx];
        }
    }

    render(ctx){
        ctx.beginPath();
        if (this.position == undefined){
            return false;
        }
        console.log(this.position);
        const x = BOARD_MARGIN + this.position[0];
        const y = this.position[1];
        ctx.arc(x, y, PAWN_SIZE, 0, 2*Math.PI);
        ctx.strokeStyle = "#000";
        ctx.stroke();
        ctx.fillStyle = COLORS[this.color_idx];
        ctx.fill();
    }
}