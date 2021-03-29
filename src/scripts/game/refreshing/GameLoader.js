import Utilities from "../../Utitlities.js";


export default class GameLoader{
    constructor(refresher){
        this.live = true
        this.refresher = refresher
    }

    init = async () => {
        while (this.live){
            await this.refresher.refresh();
            await Utilities.sleep(1500);
        }
    }
}
