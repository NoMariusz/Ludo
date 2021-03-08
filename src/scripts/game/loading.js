import GameRefresher from "./GameRefresher.js";


// loadingGame
let live = true
let refresher = new GameRefresher()

const startLoading = async () => {
    while (live){
        await refresher.refresh();
        await sleep(500);
    }
}

const sleep = (ms) => {
    return new Promise(resolve => setTimeout(resolve, ms));
}



export default startLoading;
