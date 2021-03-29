import GameLoader from "./refreshing/GameLoader.js";
import PawnBlinker from "./PawnBlinker.js";
import ReadySliderHandler from "./handlers/ReadySliderHandler.js";
import ThrowCubeHandler from "./handlers/ThrowCubeHandler.js";
import GameRefresher from "./refreshing/GameRefresher.js";

class GamePreparer {
    constructor(){
        this.refresher = new GameRefresher()
    }
    
    main = () => {
        const gameLoader = new GameLoader(this.refresher);
        gameLoader.init();
        const pawnBlinker = new PawnBlinker(this.refresher)
        pawnBlinker.init();
        ReadySliderHandler.init();
        ThrowCubeHandler.init();
    };
}

const gamePreparer = new GamePreparer();
gamePreparer.main();
