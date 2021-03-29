import GameLoader from "./game/GameLoader.js";
import ReadySliderHandler from "./game/ReadySliderHandler.js";
import ThrowCubeHandler from "./game/ThrowCubeHandler.js";

class GamePreparer{
    static main = () => {
        const gameLoader = new GameLoader();
        gameLoader.init();
        ReadySliderHandler.init();
        ThrowCubeHandler.init();
    }
}

GamePreparer.main();
