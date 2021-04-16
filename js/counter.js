function display(val) {

    hours = parseInt(val / 3600);
    minutes = parseInt((val % 3600) / 60);
    seconds = parseInt((val % 3600) % 60);

    hours = (hours < 10) ? "0" + hours : hours;
    minutes = (minutes < 10) ? "0" + minutes : minutes;
    seconds = (seconds < 10) ? "0" + seconds : seconds;

    resp = hours + ":" + minutes + ":" + seconds;

    document.getElementById("counter").innerHTML = resp;
    document.getElementById("counter").style.visibility = "visible";
    window.document.title = resp + " Claim Free UMKoins";

}

function counter() {
    if (TIME_OUT > 0) {
        display(--TIME_OUT);
    } else {
        window.location.href = SITE_URL;
    }
}
