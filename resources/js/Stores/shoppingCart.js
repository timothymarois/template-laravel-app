import { defineStore } from 'pinia';

export const shoppingCart = defineStore('shopping-cart-store', {
    state() {
        return {
            products: []
        };
    },
    getters: {
        total: (state) => state.products.reduce((total, product) => total + (product.qty * product.sale_price), 0),
    },
    actions: {
        add(product) {
            const existingProduct = this.products.find(p => p.id === product.id);
            if (existingProduct) {
                existingProduct.qty += 1;
            } else {
                product.qty = 1
                this.products.push(product);
            }
        },
        remove(productId) {
            const productIndex = this.products.findIndex(p => p.id === productId);
            if (productIndex !== -1) {
                this.products.splice(productIndex, 1);
            }
        },
        clear() {
            this.products = [];
        }
    },
    // persist: false,
    persist: {
        key: 'cart',
    }
});
