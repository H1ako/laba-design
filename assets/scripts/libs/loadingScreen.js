const loadingScreen = document.getElementById("loading-screen");

function enableLoadingScreen() {
    loadingScreen?.classList?.remove("hidden");
}

function disableLoadingScreen() {
    loadingScreen?.classList?.add("hidden");
}

document.addEventListener("DOMContentLoaded", () => {
//   setTimeout(disableLoadingScreen, 2000);
  disableLoadingScreen()
});

window.addEventListener('beforeunload', (event) => {
    enableLoadingScreen()
});