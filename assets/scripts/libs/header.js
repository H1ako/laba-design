document.addEventListener("DOMContentLoaded", function () {
  initMenuToggle();
  initScrollBehavior();
});

function initMenuToggle() {
  // Get menu elements
  const menuButtons = document.querySelectorAll(
    "#menu-button, #fixed-menu-button"
  );
  const fullscreenMenu = document.getElementById("fullscreen-menu");
  const closeButton = fullscreenMenu?.querySelector(".fullscreen-menu__close");
  const menuOverlay = fullscreenMenu?.querySelector(
    ".fullscreen-menu__overlay"
  );

  // Exit if elements don't exist
  if (!fullscreenMenu || menuButtons.length === 0) return;

  // Toggle menu function
  const toggleMenu = () => {
    const isActive = fullscreenMenu.classList.contains("active");

    if (isActive) {
      fullscreenMenu.classList.remove("active");
      document.body.style.overflow = "";
    } else {
      fullscreenMenu.classList.add("active");
      document.body.style.overflow = "hidden";
    }
  };

  // Add click event listeners
  menuButtons.forEach((button) => {
    button.addEventListener("click", toggleMenu);
  });

  if (closeButton) {
    closeButton.addEventListener("click", toggleMenu);
  }

  if (menuOverlay) {
    menuOverlay.addEventListener("click", toggleMenu);
  }

  // Close menu on escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && fullscreenMenu.classList.contains("active")) {
      toggleMenu();
    }
  });
}

function initScrollBehavior() {
  // Get fixed elements
  const fixedHeaderElements = document.querySelectorAll(
    ".main-header__fixed, .hero-header__fixed"
  );

  // Exit if no fixed elements
  if (fixedHeaderElements.length === 0) return;

  // Variables for scroll behavior
  let lastScrollTop = 0;
  const showOffset = 200; // Show fixed elements after scrolling this much

  // Scroll event handler
  const handleScroll = () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    // Show/hide fixed elements based on scroll position
    if (scrollTop > showOffset) {
      fixedHeaderElements.forEach((el) => {
        el.classList.add("visible");
      });
    } else {
      fixedHeaderElements.forEach((el) => {
        el.classList.remove("visible");
      });
    }

    lastScrollTop = scrollTop;
  };

  // Add scroll event listener with throttling for better performance
  let ticking = false;
  window.addEventListener("scroll", function () {
    if (!ticking) {
      window.requestAnimationFrame(function () {
        handleScroll();
        ticking = false;
      });
      ticking = true;
    }
  });

  // Initial check
  handleScroll();
}

// Add this to your main JavaScript file or create a new one
document.addEventListener("DOMContentLoaded", () => {
  const heroHeader = document.querySelector(".hero-header");

  if (heroHeader) {
    // Check for reduced motion preference
    const prefersReducedMotion = window.matchMedia(
      "(prefers-reduced-motion: reduce)"
    );

    if (!prefersReducedMotion.matches) {
      let ticking = false;

      window.addEventListener("scroll", () => {
        if (!ticking) {
          window.requestAnimationFrame(() => {
            const scrollPosition = window.scrollY;
            if (scrollPosition > 0) {
              heroHeader.classList.add("scrolling");
              heroHeader.style.setProperty("--scroll", scrollPosition);
            } else {
              heroHeader.classList.remove("scrolling");
            }
            ticking = false;
          });

          ticking = true;
        }
      });
    }
  }
});
