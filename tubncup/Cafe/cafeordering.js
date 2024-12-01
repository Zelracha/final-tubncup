let opencartBtn = document.querySelector('.opencartBtn');
let closeCart = document.querySelector('.close');
let body = document.querySelector('body');
let listProductHTML = document.querySelector('.listProduct');
let listCartHTML = document.querySelector('.cartList');

let listProducts = [];
let carts = [];

opencartBtn.addEventListener('click', () => {
    body.classList.toggle('showCart');
});
closeCart.addEventListener('click', () => {
    body.classList.toggle('showCart');
});

const addDatatoHTML = () => {
    listProductHTML.innerHTML = '';
    if (listProducts.length > 0) {
        listProducts.forEach(product => {
            let newProduct = document.createElement('div');
            newProduct.classList.add('productItem');
            newProduct.dataset.id = product.id;
            newProduct.innerHTML = `
            <img id="menupic" src=${product.image}>
            <p><b>${product.name}</b></p>
            <p id="price"><i>₱${product.price}</i></p>
            <button type="" id="b2" class="cartbutton">
                <img id="cart" src="menu/shopping-cart.png">Add to Cart</button>
            `;
            listProductHTML.appendChild(newProduct);
        });
    }
};

listProductHTML.addEventListener('click', (event) => {
    let positionClick = event.target;
    if (positionClick.classList.contains('cartbutton')) {
        let product_id = positionClick.parentElement.dataset.id;
        addToCart(product_id);
    }
});

const addToCart = (product_id) => {
    // Find the product from listProducts using the product_id
    let product = listProducts.find(p => p.id == product_id);

    if (product) {
        // Check if the product is already in the cart
        let positionThisProductInCart = carts.findIndex((item) => item.product_id == product_id);

        if (positionThisProductInCart < 0) {
            // Add product to cart if not already there
            carts.push({
                product_id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: 1
            });
        } else {
            // Update quantity if product is already in the cart
            carts[positionThisProductInCart].quantity += 1;
        }
    }

    addCartToHTML();  // Update cart display
    addCartoToMemory();
};

const addCartoToMemory = () => {
    localStorage.setItem('cart', JSON.stringify(carts));
};

const addCartToHTML = () => {
    listCartHTML.innerHTML = '';  // Clear cart
    if (carts.length > 0) {
        carts.forEach(cartItem => {
            let newCartItem = document.createElement('div');
            newCartItem.classList.add('item');
            newCartItem.dataset.id = cartItem.product_id;  // Add the data-id for each cart item
            newCartItem.innerHTML = `
                <div class="image">
                    <img src="${cartItem.image}" alt="${cartItem.name}">
                </div>
                <div class="name">${cartItem.name}</div>
                <div class="totalPrice">₱${cartItem.price * cartItem.quantity}</div>
                <div class="quantity">
                    <span class="minus">-</span>
                    <span>${cartItem.quantity}</span>
                    <span class="plus">+</span>
                </div>
            `;
            listCartHTML.appendChild(newCartItem);
        });
    } else {
        // Display empty cart message when the cart is empty
        listCartHTML.innerHTML = `
            <div class="no-item-msg">
                <img class="empty-cart-image" src="icons/empty-cart.png" alt="Cart is empty"> 
                <br><p>Your cart is empty.</p>
            </div>
        `;
        // Optionally add a class for empty cart message styling
        listCartHTML.classList.add("empty-cart-message");
    }
};

listCartHTML.addEventListener('click', (event) => {
    let positionClick = event.target;
    if (positionClick.classList.contains('minus') || positionClick.classList.contains('plus')) {
        let product_id = positionClick.parentElement.parentElement.dataset.id;
        let type = 'minus';
        if (positionClick.classList.contains('plus')) {
            type = 'plus';
        }
        changeQuantityCart(product_id, type);
    }
});

const changeQuantityCart = (product_id, type) => {
    let positionItemInCart = carts.findIndex((value) => value.product_id == product_id);
    if (positionItemInCart >= 0) {

        switch (type) {
            case 'plus':
                carts[positionItemInCart].quantity = carts[positionItemInCart].quantity + 1;
                break;

            default:
                let valueChange = carts[positionItemInCart].quantity - 1;
                if (valueChange > 0) {
                    carts[positionItemInCart].quantity = valueChange;
                } else {
                    carts.splice(positionItemInCart, 1);
                }
                break;
        }
    }
    addCartToHTML();
    addCartoToMemory();
};

const initApp = () => {
    // Get product data
    fetch('products.json')
        .then(response => response.json())
        .then(data => {
            listProducts = data;
            addDatatoHTML();

            // Retrieve the cart from localStorage (if any)
            if (localStorage.getItem('cart')) {
                carts = JSON.parse(localStorage.getItem('cart'));
            } else {
                // If no cart data in localStorage, ensure the empty cart is displayed
                carts = [];
            }

            addCartToHTML();  // Display cart (empty or not)
        });
};

initApp();
