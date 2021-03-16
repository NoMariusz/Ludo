import GameRefresher from "./GameRefresher.js";


const throwCube = async () => {
    const res = await fetch("api/board/throwCube.php");
    if(!res.ok){
        alert("Can not throw cube now!");
        return false;
    } else {
        console.info("throwed cube");
    }
    // to display cube just after throw
    const data = await res.json()
    const gr = new GameRefresher();
    const mock = {
        game: {
            last_throw_points: data.points,
            throwed_cube: 1
        }
    }
    gr.loadCube(mock);
}

const connetctThrowCube = () => {
    const button = document.querySelector("#throwCube");
    button.onclick = throwCube;
}

export default connetctThrowCube;
