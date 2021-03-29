import GameRefresher from "./GameRefresher.js";
import Utilities from "../Utitlities.js";
import PawnBlinker from "./PawnBlinker.js";

export default class GameLoader{
    constructor(){
        this.live = true
        this.refresher = new GameRefresher()
    }

    init = async () => {
        const pawnBlinker = new PawnBlinker(this.refresher)
        pawnBlinker.init();
        while (this.live){
            await this.refresher.refresh();
            await Utilities.sleep(1500);
        }
    }
}
