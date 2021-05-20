import BookingCart from './modules/BookingCart.js'
import Page from './modules/Page.js'

document.addEventListener('DOMContentLoaded', function() {

    let myBooking = new BookingCart();
    myBooking.restore();
    myBooking.setTotalCart();

    let myPage = new Page('.navbar__btn','.navbar','.main');
    myPage.animateSideNav();
    myPage.displayAlert('.alert');

});