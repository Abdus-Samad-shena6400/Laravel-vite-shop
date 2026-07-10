import { createStore } from 'vuex'
import axiosClient from '../axios'

const normalizeProduct = (product = {}) => {
  if (!product || typeof product !== 'object') return {}

  const category_name =
    product.category_name ??
    (typeof product.category === 'string' ? product.category : product.category?.name) ??
    product.category_name

  return {
    ...product,
    id: product.id,
    name: product.name,
    slug: product.slug,
    // price normalization
    price:
      product.price ??
      product.sale_price ??
      product.regular_price ??
      0,
    // image normalization
    image_url:
      product.image_url ??
      product.image ??
      product.featured_image ??
      null,
    // category normalization
    category_name: category_name ?? null,
    // stock normalization (some code uses quantity/stock)
    stock: product.stock ?? product.quantity ?? 0,
    quantity: product.quantity ?? product.stock ?? 0,
  }
}

const normalizeCartItem = (item = {}) => {
  if (!item || typeof item !== 'object') return {}

  return {
    ...item,
    product: normalizeProduct(item.product),
    quantity: Number(item.quantity || 1),
  }
}

const store = createStore({
  state: {
    user: {
      token: localStorage.getItem('TOKEN'),
      data: JSON.parse(localStorage.getItem('USER')) || {}
    },
    // Central products list
    products: [],
    categories: [],
    brands: [],

    // Cart and wishlist will be loaded from database via API
    cart: [],
    wishlist: [],
    // Local wishlist for non-authenticated users
    localWishlist: JSON.parse(localStorage.getItem('LOCAL_WISHLIST') || '[]'),
    // Track products currently being added to cart to prevent duplicates
    addingToCart: new Set()
  },

  getters: {
    userToken: state => state.user.token,
    userData: state => state.user.data,
    allProducts: state => state.products,
    featuredProducts: state => state.products.slice(0, 8),
    latestProducts: state => [...state.products].reverse().slice(0, 8),
    allCategories: state => state.categories,
    allBrands: state => state.brands,
    topBrands: state => state.brands.slice(0, 8),
    cartItems: state => state.cart,
    cartCount: state => state.cart.reduce((total, item) => total + item.quantity, 0),
    cartTotal: state => state.cart.reduce((total, item) => {
      const price = item.product.deal_price || item.product.price || item.product.sale_price || item.product.regular_price || 0;
      return total + (price * item.quantity);
    }, 0),
    wishlistItems: state => state.user.token ? state.wishlist : state.localWishlist,
    wishlistCount: state => state.user.token ? state.wishlist.length : state.localWishlist.length,
    isInWishlist: (state) => (productId) => {
      if (state.user.token) {
        return state.wishlist.some(item => item.product?.id === productId)
      }
      return state.localWishlist.some(item => item.id === productId)
    },
    isInCart: (state) => (productId) => {
      return state.cart.some(item => item.product?.id === productId)
    }
  },

  actions: {
    async register({ commit }, user) {
      const response = await axiosClient.post('/register', user)
      commit('setUser', response.data.user)
      commit('setToken', response.data.access_token)
      return response.data
    },




    async login({ commit, dispatch, state }, credentials) {
      const response = await axiosClient.post('/login', credentials)
      commit('setUser', response.data.user)
      commit('setToken', response.data.access_token)
      // Load cart and wishlist from database after login
      dispatch('loadCart')
      dispatch('loadWishlist')
      // Sync local wishlist to server if there are items
      if (state.localWishlist.length > 0) {
        for (const item of state.localWishlist) {
          try {
            await dispatch('toggleWishlist', item.id)
          } catch (error) {
            console.error('Failed to sync wishlist item:', error)
          }
        }
        commit('CLEAR_LOCAL_WISHLIST')
      }
      return response.data
    },

    async logout({ commit }) {
      try {
        await axiosClient.post('/logout')
      } catch (error) {
        console.error('Error logging out from server:', error)
      } finally {
        commit('setUser', {})
        commit('setToken', null)
        commit('CLEAR_CART')
        commit('SET_WISHLIST', [])
      }
    },

    async updateProfile({ commit }, form) {
      const response = await axiosClient.put('/profile', form)
      commit('setUser', response.data.user)
      return response.data
    },

    async loadCart({ commit }) {

    const { data } = await axiosClient.get('/cart')

    commit('SET_CART', data)

},

async addToCart({ dispatch, state }, { productId, quantity = 1 }) {
    // Check if user is authenticated
    if (!state.user.token) {
        throw new Error('Please sign in to add items to your cart.')
    }
    
    // Prevent duplicate calls for the same product
    if (state.addingToCart.has(productId)) {
        return { message: 'Product is already being added to cart.' }
    }
    
    state.addingToCart.add(productId)
    
    try {
        // Check if item is in wishlist and remove it automatically
        const wishlistItem = state.wishlist.find(item => item.product?.id === productId)
        if (wishlistItem) {
            try {
                await axiosClient.delete(`/wishlist/${wishlistItem.id}`)
                dispatch('loadWishlist')
            } catch (error) {
                console.error('Failed to remove from wishlist:', error)
            }
        }
        
        const response = await axiosClient.post('/cart', {
            product_id: productId,
            quantity
        })
        dispatch('loadCart')
        return response.data
    } catch (error) {
        // Re-throw with more detailed error message
        if (error.response?.status === 422) {
            const message = error.response.data?.message || 'Validation failed'
            throw new Error(message)
        }
        throw error
    } finally {
        state.addingToCart.delete(productId)
    }
},

async updateCartQuantity({ dispatch }, { cartItemId, quantity }) {
    const response = await axiosClient.put(`/cart/${cartItemId}`, {
        quantity
    })
    dispatch('loadCart')
    return response.data
},

async removeFromCart({ dispatch }, cartItemId) {
    const response = await axiosClient.delete(`/cart/${cartItemId}`)
    dispatch('loadCart')
    return response.data
},

async clearCart({ dispatch }) {
    const response = await axiosClient.delete('/cart')
    dispatch('loadCart')
    return response.data
},
    async fetchProducts({ commit }) {

      const { data } = await axiosClient.get('/store/products')

      commit('SET_PRODUCTS', data.data ?? data)

    },

    async fetchCategories({ commit }) {

      const { data } = await axiosClient.get('/store/categories')

      commit('SET_CATEGORIES', data.data ?? data)

    },

    async fetchBrands({ commit }) {

      const { data } = await axiosClient.get('/store/brands')

      commit('SET_BRANDS', data.data ?? data)

    },

    async loadWishlist({ commit }) {

    const { data } = await axiosClient.get('/wishlist')

    commit('SET_WISHLIST', data)

},

async addToWishlist({ dispatch, state }, productId) {
    // Check if item is in cart and remove it automatically
    const cartItem = state.cart.find(item => item.product?.id === productId)
    if (cartItem) {
        try {
            await axiosClient.delete(`/cart/${cartItem.id}`)
            dispatch('loadCart')
        } catch (error) {
            console.error('Failed to remove from cart:', error)
        }
    }
    
    const response = await axiosClient.post('/wishlist', {
        product_id: productId
    })
    dispatch('loadWishlist')
    return response.data
},

async removeFromWishlist({ dispatch }, wishlistId) {
    const response = await axiosClient.delete(`/wishlist/${wishlistId}`)
    dispatch('loadWishlist')
    return response.data
},

async toggleWishlist({ dispatch, commit, state }, productId) {
    // If user is not authenticated, use localStorage
    if (!state.user.token) {
        const item = state.localWishlist.find(w => w.id === productId)
        if (item) {
            commit('REMOVE_FROM_LOCAL_WISHLIST', productId)
            return { message: 'Item removed from wishlist.' }
        } else {
            // Check if item is in cart and remove it automatically before adding to wishlist
            const cartItem = state.cart.find(item => item.product?.id === productId)
            if (cartItem) {
                try {
                    await axiosClient.delete(`/cart/${cartItem.id}`)
                    dispatch('loadCart')
                } catch (error) {
                    console.error('Failed to remove from cart:', error)
                }
            }
            commit('ADD_TO_LOCAL_WISHLIST', productId)
            return { message: 'Item added to wishlist.' }
        }
    }
    
    // If user is authenticated, use API
    const item = state.wishlist.find(w => w.product?.id === productId)
    let response
    if (item) {
        response = await axiosClient.delete(`/wishlist/${item.id}`)
    } else {
        // Check if item is in cart and remove it automatically before adding to wishlist
        const cartItem = state.cart.find(item => item.product?.id === productId)
        if (cartItem) {
            try {
                await axiosClient.delete(`/cart/${cartItem.id}`)
                dispatch('loadCart')
            } catch (error) {
                console.error('Failed to remove from cart:', error)
            }
        }
        response = await axiosClient.post('/wishlist', {
            product_id: productId
        })
    }
    dispatch('loadWishlist')
    return response.data
},

async moveToCartFromWishlist({ dispatch }, { productId, wishlistId, quantity = 1 }) {
    try {
        // Add to cart without checking wishlist
        const cartResponse = await axiosClient.post('/cart', {
            product_id: productId,
            quantity
        })
        dispatch('loadCart')
        
        // Remove from wishlist
        await axiosClient.delete(`/wishlist/${wishlistId}`)
        dispatch('loadWishlist')
        
        return cartResponse.data
    } catch (error) {
        if (error.response?.status === 422) {
            const message = error.response.data?.message || 'Validation failed'
            throw new Error(message)
        }
        throw error
    }
},

async moveToWishlistFromCart({ dispatch }, { productId, cartItemId }) {
    try {
        // Add to wishlist without checking cart
        const wishlistResponse = await axiosClient.post('/wishlist', {
            product_id: productId
        })
        dispatch('loadWishlist')
        
        // Remove from cart
        await axiosClient.delete(`/cart/${cartItemId}`)
        dispatch('loadCart')
        
        return wishlistResponse.data
    } catch (error) {
        throw error
    }
}

   
  },

  mutations: {
    setUser(state, user) {
      state.user.data = user
      if (Object.keys(user).length > 0) {
        localStorage.setItem('USER', JSON.stringify(user))
      } else {
        localStorage.removeItem('USER')
      }
    },

    setToken(state, token) {
      state.user.token = token
      if (token) {
        localStorage.setItem('TOKEN', token)
      } else {
        localStorage.removeItem('TOKEN')
      }
    },

    SET_CART(state, cart) {
      state.cart = (cart || []).map(normalizeCartItem)
    },

    ADD_TO_CART(state, product) {
      const normalizedProduct = normalizeProduct(product)
      const cartItem = state.cart.find((item) => item.product?.id === normalizedProduct.id)
      if (cartItem) {
        cartItem.quantity++
      } else {
        state.cart.push({ product: normalizedProduct, quantity: 1 })
      }
    },

    REMOVE_FROM_CART(state, productId) {
      state.cart = state.cart.filter(item => item.product?.id !== productId)
    },

    UPDATE_CART_QUANTITY(state, { productId, quantity }) {
      const cartItem = state.cart.find(item => item.product?.id === productId)
      if (cartItem) {
        cartItem.quantity = Math.max(1, quantity)
      }
    },

    CLEAR_CART(state) {
      state.cart = []
    },

    SET_WISHLIST(state, wishlist) {

    state.wishlist = wishlist

},

    ADD_TO_WISHLIST(state, product) {
      state.wishlist.push(normalizeProduct(product))
    },

    REMOVE_FROM_WISHLIST(state, productId) {
      state.wishlist = state.wishlist.filter(p => p.product?.id !== productId)
    },

    ADD_TO_LOCAL_WISHLIST(state, productId) {
      if (!state.localWishlist.find(w => w.id === productId)) {
        state.localWishlist.push({ id: productId })
        localStorage.setItem('LOCAL_WISHLIST', JSON.stringify(state.localWishlist))
      }
    },

    REMOVE_FROM_LOCAL_WISHLIST(state, productId) {
      state.localWishlist = state.localWishlist.filter(w => w.id !== productId)
      localStorage.setItem('LOCAL_WISHLIST', JSON.stringify(state.localWishlist))
    },

    CLEAR_LOCAL_WISHLIST(state) {
      state.localWishlist = []
      localStorage.removeItem('LOCAL_WISHLIST')
    },

    SET_PRODUCTS(state, products) {

      state.products = products

    },

    SET_CATEGORIES(state, categories) {

      state.categories = categories

    },

    SET_BRANDS(state, brands) {

      state.brands = brands

    },
  }
})

export default store