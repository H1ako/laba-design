const banner = document.querySelector('.banner')

setInterval(() => {
    const currentScene = Number(banner.dataset.scene)
    const nextScene = currentScene === 3 ? 1 : currentScene + 1
    banner.dataset.scene = nextScene

}, 3500)