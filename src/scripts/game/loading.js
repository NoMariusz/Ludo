import GameRefresher from "./GameRefresher.js";
import { sleep } from "../utils.js";
import startPawnBlinking from "./pawnBlinker.js";


// loadingGame
let live = true
let refresher = new GameRefresher()

const startLoading = async () => {
    startPawnBlinking(refresher);
    while (live){
        await refresher.refresh();
        await sleep(1500);
    }
}



export default startLoading;
