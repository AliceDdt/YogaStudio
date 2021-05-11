import BookingCart from './modules/BookingCart.js'

/******** MAIN CODING */
document.addEventListener('DOMContentLoaded', function() {

    let myBooking = new BookingCart();
    myBooking.restore();
    myBooking.setTotalCart();

    const btnNav = document.querySelector('.navbar__btn');
    const navBar = document.querySelector('.navbar');
    const main = document.querySelector('.main');
    
    btnNav.addEventListener('click', function () {
        
        navBar.classList.toggle('open');
    });
    
   main.addEventListener('click', function(e){
            navBar.classList.remove('open');
        
    });


});