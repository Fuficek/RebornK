// mobnile nav menu overlay toggle active
document.addEventListener("DOMContentLoaded", () => {
    const menuOpen = document.querySelector(".menu");
    const menuClose = document.querySelector(".close");
    const overlay = document.querySelector(".overlay");
  
    if (menuOpen && menuClose && overlay) {
      menuOpen.addEventListener("click", () => {
        overlay.classList.add("overlay--active");
      });
  
      menuClose.addEventListener("click", () => {
        overlay.classList.remove("overlay--active");
      });
    }
  });
  