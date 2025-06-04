let slideIndex = 0;
const slides = document.getElementsByClassName("slide");

if (slides.length > 0) {
  function showSlides() {
    for (let i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
    }
    slideIndex++;
    if (slideIndex > slides.length) slideIndex = 1;
    slides[slideIndex - 1].style.display = "block";
    setTimeout(showSlides, 4000); // Chuyển mỗi 4 giây
  }

  showSlides();
}


// Lấy tất cả các item có submenu
const submenuParents = document.querySelectorAll('.main-menu li.has-submenu > a');

submenuParents.forEach(menuLink => {
  menuLink.addEventListener('click', function(e) {
    e.preventDefault();
    const parentLi = this.parentElement;

    const isActive = parentLi.classList.contains('active');

    // Đóng tất cả menu đang mở
    document.querySelectorAll('.main-menu li.has-submenu.active').forEach(item => {
      item.classList.remove('active');
    });

    // Toggle trạng thái hiện tại
    if (!isActive) {
      parentLi.classList.add('active');
    }
  });
});

// Click ngoài menu để đóng submenu
document.addEventListener('click', function(e) {
  if (!e.target.closest('.main-menu')) {
    document.querySelectorAll('.main-menu li.has-submenu.active').forEach(item => {
      item.classList.remove('active');
    });
  }
});
