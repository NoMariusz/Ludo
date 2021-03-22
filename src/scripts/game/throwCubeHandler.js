import GameRefresher from "./GameRefresher.js";
import CubeSpeaker from "./CubeSpeaker.js";

const throwCube = async (e) => {
    const res = await fetch("api/public/throwCube.php");
    if (!res.ok) {
        alert("Can not throw cube now!");
        return false;
    } else {
        console.info("throwed cube");
        // hide btn after throw cube
        e.target.classList.add("hidden");
    }
    // to display cube just after throw
    const data = await res.json();
    const gr = new GameRefresher();
    const mock = {
        game: {
            last_throw_points: data.points,
            throwed_cube: 1,
        },
    };
    gr.loadCube(mock);
    // speak cube points
    new CubeSpeaker(data.points);
};

const connetctThrowCube = () => {
    const button = document.querySelector("#throwCube");
    button.onclick = throwCube;
};

export default connetctThrowCube;
