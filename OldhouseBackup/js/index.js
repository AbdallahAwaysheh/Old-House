document.getElementById('toggleNavButton').addEventListener('click', function () {
  var navLinks = document.getElementById('navLinks');
  navLinks.classList.toggle('show');
});
document.addEventListener('DOMContentLoaded', (event) => {
  let slideIndex = 1;
  showSlides(slideIndex);

  function plusSlides(n) {
      showSlides(slideIndex += n);
  }

  function currentSlide(n) {
      showSlides(slideIndex = n);
  }

  function showSlides(n) {
      let i;
      let slides = document.getElementsByClassName("mySlides");
      let dots = document.getElementsByClassName("dot");
      if (n > slides.length) { slideIndex = 1 }
      if (n < 1) { slideIndex = slides.length }
      for (i = 0; i < slides.length; i++) {
          slides[i].style.display = "none";
      }
      for (i = 0; i < dots.length; i++) {
          dots[i].className = dots[i].className.replace(" active", "");
      }
      slides[slideIndex - 1].style.display = "flex";
      dots[slideIndex - 1].className += " active";
  }

  document.querySelector('.prev').addEventListener('click', () => plusSlides(-1));
  document.querySelector('.next').addEventListener('click', () => plusSlides(1));
});


