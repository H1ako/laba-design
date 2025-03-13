<div class="loading-screen" id="loading-screen">
    <div class="loading-screen__dots-wrapper">
        <div class="dots-wrapper__dot dot_one"></div>
        <div class="dots-wrapper__dot dot_two"></div>
        <div class="dots-wrapper__dot dot_three"></div>
    </div>

    <svg version="1.1" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <filter id="goo">
                <feGaussianBlur
                    result="blur"
                    stdDeviation="10"
                    in="SourceGraphic"></feGaussianBlur>
                <feColorMatrix
                    values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 21 -7"
                    mode="matrix"
                    in="blur"></feColorMatrix>
            </filter>
        </defs>
    </svg>

</div>