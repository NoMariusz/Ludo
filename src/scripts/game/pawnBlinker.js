import { PREETY_COLORS, MAIN_COLOR, PAWN_FLASHING_TIME } from "../constants.js";
import { sleep } from "../utils.js";

let live = true;

const startPawnBlinking = async (refresher) => {
    /* start loop changing blink pawns color */
    const canvas = document.querySelector("#gameCanvas");
    const ctx = canvas.getContext("2d");

    while (live) {
        refreshBlinkingPawns(refresher, ctx, true);
        await sleep(PAWN_FLASHING_TIME);
        refreshBlinkingPawns(refresher, ctx, false);
        await sleep(PAWN_FLASHING_TIME);
    }
};

const refreshBlinkingPawns = (refresher, ctx, blinkColor) => {
    refresher.pawns.forEach((pawn) => {
        // change canMoveColor to blink
        pawn.canMoveColor = blinkColor
            ? MAIN_COLOR
            : PREETY_COLORS[pawn.color_idx];
        // draw if can move that pawn
        if (pawn.canBeMoved) {
            pawn.drawPawn(ctx);
        }
    });
};

export default startPawnBlinking;
