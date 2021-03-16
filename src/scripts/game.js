import startLoading from "./game/loading.js";
import initSliderHandler from "./game/readyHandler.js";
import connetctThrowCube from "./game/throwCubeHandler.js";

const main = () => {
    startLoading();
    initSliderHandler();
    connetctThrowCube();
}

main();
