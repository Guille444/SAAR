// Cuando se hace clic en el botón, se expande o contrae una barra lateral en la página web. 
const hamBurger = document.querySelector(".toggle-btn");

hamBurger.addEventListener("click", function () {
    document.querySelector("#sidebar").classList.toggle("expand");
});