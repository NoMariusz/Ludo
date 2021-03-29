export default class ReadySliderHandler{
    static init = () => {
        const isReadySwitch = document.getElementById('isReadySwitch');
        isReadySwitch.onchange = this.handleReadyChange;
    }
    
    static handleReadyChange = async (e) => {
        // send to socket player ready
        const res = await fetch("api/public/setPlayerReady.php");
        if(!res.ok){
            console.error("Can not set player ready!");
        }
    }
}
