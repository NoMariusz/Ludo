const initSliderHandler = () => {
    const isReadySwitch = document.getElementById('isReadySwitch');
    isReadySwitch.onchange = handleReadyChange;
}

const handleReadyChange = async (e) => {
    // send to socket player ready
    const res = await fetch("api/game/setPlayerReady.php");
    if(!res.ok){
        console.error("Can not set player ready!");
    }
}

export default initSliderHandler;
