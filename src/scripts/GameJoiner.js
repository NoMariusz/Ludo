class GameJoiner {
    static joinToGame = async () => {
        const playerNick = document.getElementById("playerNick");
        let path = `api/public/login.php?nick=${playerNick.value}`;

        let res = await fetch(path);

        if (!res.ok) {
            console.error("Can not login to game");
        }
        let resData = await res.json();
        console.log(resData);
        if (resData.result) {
            console.log("redirecting ...");
            // redirect to game
            window.location.href += "game.php";
        }
    };
}
