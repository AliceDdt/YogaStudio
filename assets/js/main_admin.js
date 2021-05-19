
document.addEventListener('DOMContentLoaded', function() {

    const btnNav = document.querySelector('.navbar__btn');
    const navBar = document.querySelector('.navbar');
    const main = document.querySelector('.main__admin');
    
    btnNav.addEventListener('click', function () {
        
        navBar.classList.toggle('open');
    });
    
   main.addEventListener('click', function(e){
            navBar.classList.remove('open');
        
    });


});