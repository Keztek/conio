function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

//setMode('dark');

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function updatePageStyle(mode) {
    let nextMode, modeIcon;
    switch (mode) {
        case "dark":
            document.getElementById("themeStylesheet").href = "http://conio.keztek.net/css/darkmode.css";
            nextMode = "light";
            modeIcon = '<i class="fa-solid fa-moon"></i>';
            break;
        case "light":
            document.getElementById("themeStylesheet").href = "http://conio.keztek.net/css/lightmode.css";
            nextMode = "system";
            modeIcon = '<i class="fa-solid fa-sun"></i>';
            break;
        case "system":
            document.getElementById("themeStylesheet").href = "http://conio.keztek.net/css/systemmode.css";
            nextMode = "dark";
            modeIcon = '<i class="fa-solid fa-gear"></i>';
            break;
        default:
            // Default to system mode if the mode is undefined or not recognized
            document.getElementById("themeStylesheet").href = "http://conio.keztek.net/css/systemmode.css";
            nextMode = "dark";
            modeIcon = '<i class="fa-solid fa-gear"></i>';
            break;
    }

    // Update button onclick attribute and inner HTML
    let modeBtn = document.querySelector(".modeBtn");
    modeBtn.onclick = function () { setMode(nextMode); };
    modeBtn.innerHTML = modeIcon;
}

function setMode(mode) {
    setCookie("mode", mode, 30); // Set the cookie
    updatePageStyle(mode); // Update the page style without reloading
}

// On page load, set the theme according to the cookie
window.onload = function () {
    let currentMode = getCookie("mode");
    if (currentMode) {
        updatePageStyle(currentMode);
    } else {
        // If no mode is set in the cookie, you can choose to set a default or try to respect the system settings
        // For example, default to 'system'
        setMode("system");
    }
};