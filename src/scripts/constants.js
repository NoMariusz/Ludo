export default class Constants{
    static READY_TEXT = "I want to play!";
    static WAITING_TEXT = "Waiting for other players";

    static TURN_TIME = 50;
    static COLORS = ["yellow", "red", "green", "blue"];
    static PREETY_COLORS = ["#aeb100", "#ac1a00", "#00a344", "#004992"];
    static MAIN_COLOR = "#ff8f26";

    static PAWN_FLASHING_TIME = 500;

    static WIDTH = 1000;
    static HEIGHT = 500;
    static BOARD_MARGIN = 250;
    static BOARD_SIZE = 500;

    static PAWN_SIZE = 10;
    static PAWNS_COUNT = 40;
    static DICE_SIZE = 100;

    static DEFAULT_SPEAK_LANGUAGE = "es-ES";

    static PAWNS_POSITIONS = {
        outOfBoard: [
            // yellow
            [
                [30, 425],
                [30, 470],
                [75, 470],
                [75, 425],
            ],
            //red
            [
                [30, 30],
                [30, 75],
                [75, 75],
                [75, 30],
            ],
            // green
            [
                [425, 425],
                [425, 470],
                [470, 470],
                [470, 425],
            ],
            // blue
            [
                [425, 30],
                [425, 75],
                [470, 75],
                [470, 30],
            ],
        ],
        inHome: [
            // yellow
            [
                [250, 425],
                [250, 380],
                [250, 335],
                [250, 295],
            ],
            //red
            [
                [75, 250],
                [120, 250],
                [160, 250],
                [205, 250],
            ],
            // green
            [
                [425, 250],
                [380, 250],
                [335, 250],
                [295, 250],
            ],
            // blue
            [
                [250, 75],
                [250, 120],
                [250, 160],
                [250, 205],
            ],
        ],
        normal: [
            [205, 470],
            [205, 425],
            [205, 380],
            [205, 335],
            [205, 295],
            [160, 295],
            [120, 295],
            [75, 295],
            [30, 295],
            [30, 250],
            [30, 205],
            [75, 205],
            [120, 205],
            [160, 205],
            [205, 205],
            [205, 160],
            [205, 120],
            [205, 75],
            [205, 30],
            [250, 30],
            [295, 30],
            [295, 75],
            [295, 120],
            [295, 160],
            [295, 205],
            [335, 205],
            [380, 205],
            [425, 205],
            [470, 205],
            [470, 250],
            [470, 295],
            [425, 295],
            [380, 295],
            [335, 295],
            [295, 295],
            [295, 335],
            [295, 380],
            [295, 425],
            [295, 470],
            [250, 470],
        ],
    };
}
