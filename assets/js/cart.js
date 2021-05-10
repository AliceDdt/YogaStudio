import BookingCart from './modules/BookingCart.js'

document.addEventListener('DOMContentLoaded', () => {
    let myBooking = new BookingCart();
    myBooking.restore();
    myBooking.showCart();
    myBooking.removeFromCart();
   
});