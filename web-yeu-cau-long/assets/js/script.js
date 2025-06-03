// Mega dropdow
document.addEventListener("DOMContentLoaded", () => {
  const megaDropdown = document.querySelector(".mega-dropdown");
  if (megaDropdown) {
    megaDropdown.addEventListener("mouseenter", () => {
      megaDropdown.classList.add("mega-menu-active");
    });
    megaDropdown.addEventListener("mouseleave", () => {
      megaDropdown.classList.remove("mega-menu-active");
    });
  }
});


