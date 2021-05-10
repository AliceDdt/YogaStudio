import BookingCart from './modules/BookingCart.js'

/******** MAIN CODING */
document.addEventListener('DOMContentLoaded', function() {

    let myBooking = new BookingCart();
    myBooking.restore();
    myBooking.setTotalCart();

    const btnNav = document.querySelector('.btn-navbar');
    const navBar = document.querySelector('.navbar');
    const main = document.querySelector('.main-content');
    
    btnNav.addEventListener('click', function () {
        
        navBar.classList.toggle('open');
    });
    
   main.addEventListener('click', function(e){
            navBar.classList.remove('open');
        
    });


});