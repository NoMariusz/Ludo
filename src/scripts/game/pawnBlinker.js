import Constants from "../Constants.js";
import Utilities from "../Utitlities.js";

export default class PawnBlinker {
    constructor(refresher) {
        this.live = true;
        this.refresher = refresher;
    }

    init = async () => {
        /* start loop changing blink pawns color */
        const canvas = document.querySelector("#gameCanvas");
        const ctx = canvas.getContext("2d");

        while (this.live) {
            this.refreshBlinkingPawns(ctx, true);
            await Utilities.sleep(Constants.PAWN_FLASHING_TIME);
            this.refreshBlinkingPawns(ctx, false);
            await Utilities.sleep(Constants.PAWN_FLASHING_TIME);
        }
    };

    refreshBlinkingPawns = (ctx, blinkColor) => {
        this.refresher.pawns.forEach((pawn) => {
            // change canMoveColor to blink
            pawn.canMoveColor = blinkColor
                ? Constants.MAIN_COLOR
                : Constants.PREETY_COLORS[pawn.color_idx];
            // draw if can move that pawn
            if (pawn.canBeMoved) {
                pawn.drawPawn(ctx);
            }
        });
    };
}
