import marquee from "/assets/scripts/libs/vanilla-marquee.min.js";

const marqueeEl = document.querySelector(".marquee");

const newMarquee = new marquee(marqueeEl, {
    duplicated: true,
    direction: "left",
    gap: 0,
    speed: 20,
    delayBeforeStart: 0,
    startVisible: true,
})