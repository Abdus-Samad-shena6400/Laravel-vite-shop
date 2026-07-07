import { createRouter, createWebHistory } from 'vue-router'
import AppLayout from '../components/AppLayout.vue'
import Dashboard from '../views/admin/Dashboard.vue'
import Profile from '../views/store/Profile.vue'
import Home from '../views/store/Home.vue'
import Shop from '../views/store/Shop.vue'
import Cart from '../views/store/Cart.vue'
import Wishlist from '../views/store/Wishlist.vue'
import UserDashboard from '../views/store/UserDashboard.vue'
import Login from '../views/store/Login.vue'
import Register from '../views/store/Register.vue'
import RequestPassword from '../views/store/RequestPassword.vue'
import ResetPassword from '../views/store/ResetPassword.vue'
import Categories from '../views/admin/Categories.vue'
import CategoryCreate from '../views/admin/CategoryCreate.vue'
import CategoryEdit from '../views/admin/CategoryEdit.vue'
import Brand from '../views/admin/Brand.vue'
import BrandCreate from '../views/admin/CreateBrand.vue'
import BrandEdit from '../views/admin/EditBrand.vue'
import Product from '../views/admin/Product.vue'
import ProductCreate from '../views/admin/ProductCreate.vue'
import ProductEdit from '../views/admin/ProductEdit.vue'
import Order from '../views/admin/Order.vue'
import OrderShow from '../views/admin/OrderShow.vue'
import ProductShow from '../views/store/ProductShow.vue'
import Coupon from '../views/admin/Coupon.vue'
import CouponCreate from '../views/admin/CouponCreate.vue'
import CouponEdit from '../views/admin/CouponEdit.vue'
import store from '../store'


const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/shop',
        name: 'shop',
        component: Shop
    },
    {
        path: '/products/:id',
        name: 'product.show',
        component: ProductShow
    },
    {
        path: '/cart',
        name: 'cart',
        component: Cart
    },
    {
        path: '/checkout',
        name: 'checkout',
        component: () => import('../views/store/Checkout.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/contact',
        name: 'contact',
        component: () => import('../views/store/Contact.vue')
    },

    {
        path: '/about',
        name: 'about',
        component: () => import('../views/store/About.vue')
    },


    {
        path: '/my-orders/:id',
        name: 'myOrderDetails',
        component: () => import('../views/store/MyOrderDetails.vue')
    },
    {
        path: '/wishlist',
        name: 'wishlist',
        component: Wishlist
    },
    {
        path: '/user/dashboard',
        name: 'user-dashboard',
        component: UserDashboard, // <--- Register the UserDashboard
        meta: { requiresAuth: true } // <--- Requires login to access


    },
    {
        path: '/',
        component: AppLayout,
        meta: { requiresAuth: true, requiresAdmin: true },
        children: [
            {
                path: 'dashboard',
                name: 'dashboard',
                component: Dashboard
            },
            {
                path: 'profile',
                name: 'profile',
                component: Profile
            },
            {
                path: 'categories',
                name: 'categories',
                component: Categories
            },
            {
                path: 'categories/create',
                name: 'category.create',
                component: CategoryCreate
            },
            {
                path: 'categories/:id/edit',
                name: 'category.edit',
                component: CategoryEdit
            },
            {
                path: 'brands',
                name: 'brands',
                component: Brand
            },
            {
                path: 'brands/create',
                name: 'brand.create',
                component: BrandCreate
            },
            {
                path: 'brands/:id/edit',
                name: 'brand.edit',
                component: BrandEdit
            },
            {
                path: 'products',
                name: 'products',
                component: Product
            },
            {
                path: 'products/create',
                name: 'product.create',
                component: ProductCreate
            },
            {
                path: 'products/:id/edit',
                name: 'product.edit',
                component: ProductEdit
            },
            {
                path: 'orders',
                name: 'orders',
                component: Order
            },
            {
                path: 'orders/:id',
                name: 'orders.show',
                component: OrderShow
            },

            {
                path: 'customers',
                name: 'customers',
                component: () => import('../views/admin/Customers.vue')
            },
            {
                path: 'customers/:id',
                name: 'customers.show',
                component: () => import('../views/admin/CustomerShow.vue'),
                props: true
            },
            {
                path: '/admin/coupons',
                name: 'coupons',
                component: Coupon
            },
            {
                path: '/admin/coupons/create',
                name: 'couponCreate',
                component: CouponCreate
            },
            {
                path: '/admin/coupons/:id/edit',
                name: 'couponEdit',
                component: CouponEdit
            },

            {
                path: 'reports',
                name: 'reports',
                component: () => import('../views/admin/Reports.vue')
            },

            {
                path: 'contacts',
                name: 'contacts',
                component: () => import('../views/admin/Contacts.vue')
            },
            {
                path: 'contacts/:id',
                name: 'contacts.show',
                component: () => import('../views/admin/ContactShow.vue')
            },
            {
                path: 'reviews',
                name: 'reviews',
                component: () => import('../views/admin/Reviews.vue')
            },
        ]
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { requiresGuest: true }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { requiresGuest: true }
    },
    {
        path: '/request-password',
        name: 'requestPassword',
        component: RequestPassword,
        meta: { requiresGuest: true }
    },
    {
        path: '/reset-password/:token',
        name: 'resetPassword',
        component: ResetPassword,
        meta: { requiresGuest: true }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Global Navigation Guard
router.beforeEach((to, from, next) => {
    const isAuthenticated = !!store.state.user.token
    const user = store.state.user.data
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
    const requiresAdmin = to.matched.some(record => record.meta.requiresAdmin)
    const requiresGuest = to.matched.some(record => record.meta.requiresGuest)

    if (requiresAuth && !isAuthenticated) {
        return next({ name: 'login' })
    }

    if (requiresAdmin && !user?.is_admin) {
        return next({ name: 'user-dashboard' })
    }

    if (requiresGuest && isAuthenticated) {
        if (user?.is_admin) {
            return next({ name: 'dashboard' })
        }

        return next({ name: 'user-dashboard' })
    }

    next()
})

export default router