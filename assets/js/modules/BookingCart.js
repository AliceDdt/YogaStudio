class BookingCart {
    
    //cart
    bookingCart = [];

    //localStorage name
    storageName = "yogaBooking";

    elementData = {};

    selectorDivCart = '#cart-display';
    selector ='.btn__resa';
    selectorBadge = '#cart-total';
    selectorInput = '#bookingCartJson';
    selectorForm = '#booking-form';

    input = document.querySelector(this.selectorInput);
    elements = document.querySelectorAll(this.selector);
    badge = document.querySelector(this.selectorBadge);
    cart = document.querySelector(this.selectorDivCart);
    form = document.querySelector(this.selectorForm);


//
    getBookingData(element){
        this.elementData = {
            id: element.dataset.id,
            name: element.dataset.name,
            date: element.dataset.date,
            time: element.dataset.time,
            price: element.dataset.price       
        };

        return this.elementData;
    }
    
    addBooking(){
        this.restore();
        this.setTotalCart();
        
        this.elements.forEach(element => {
           
            let item = this.getBookingData(element);
            
            for (const i in this.bookingCart) {
                //test si produit dejà ajouté
                if (this.bookingCart[i].id == item.id) {
                    this.disabledBookingElement(element);
                    return;
                }
            }


            element.addEventListener('click', e => {
                e.preventDefault();
                
                this.getBookingData(element);
                
                this.bookingCart.push({
                                id: this.elementData.id,
                                name: this.elementData.name,
                                date: this.elementData.date,
                                time: this.elementData.time,
                                price: this.elementData.price
                });

                this.save();
                this.setTotalCart();
                this.disabledBookingElement(element);
            });
        });
    
    }
 
    setTotalCart(){      
        this.badge.innerText = this.bookingCart.length;
    }

    save(){
        localStorage.setItem(this.storageName,JSON.stringify(this.bookingCart));
    }

    restore(){
            let data = localStorage.getItem(this.storageName);
            if (data) this.bookingCart = JSON.parse(data);  
    }
      
    remove(idElement) {
        this.bookingCart.splice(idElement, 1);
         
        //enregistrer this.listTasks dans localstorage
        this.save();
              
         //rafraichir l'html
        this.showCart();
    }
 
    removeFromCart(){
        this.cart.addEventListener('click', e => {
            e.preventDefault();
            //on veut declencher le traitement du delete uniquement sur les balises a.delete
            if (e.target.classList.contains('remove')) {
            this.remove(parseInt(e.target.dataset.id));   
            }
        });
    }

    showCart(){
        this.setTotalCart();
        let content = '';

        if(this.bookingCart.length == 0){
            content+='<p>Vous n\'avez aucune réservation en cours !</p>';
            this.form.classList.add("hidden");
        }

        else{
            
            this.form.classList.remove("hidden");
            this.bookingCart.forEach(function(booking, i){
                
                content+=
                    `<div class="cart__booking">
                        <div>
                            <p>${booking.name}</p>
                            <p>${booking.date}</p>
                            <p>${booking.time}</p>
                            <p>${booking.price} €</p>
                        </div>
                    <button type="button" class="btn--remove remove" data-id=${i}>supprimer</button>
                    </div>`;
            })

            this.input.setAttribute('value', JSON.stringify(this.bookingCart));
        }
        
        this.cart.innerHTML = content;

    }

    disabledBookingElement(element){
        element.disabled = true;
        element.classList.add('disabled');
        element.innerText = 'Ajouté';
    }
 
}

export default BookingCart;