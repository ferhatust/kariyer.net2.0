import { defineRouter } from '#q-app/wrappers'
import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from 'vue-router'
import routes from './routes'

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

export default defineRouter(function (/* { store, ssrContext } */) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === 'history'
      ? createWebHistory
      : createWebHashHistory

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,

    // Leave this as is and make changes in quasar.conf.js instead!
    // quasar.conf.js -> build -> vueRouterMode
    // quasar.conf.js -> build -> publicPath
    history: createHistory(process.env.VUE_ROUTER_BASE),
  })

  // Navigation guards
  Router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('api_token')
    const type = localStorage.getItem('auth_type') // 'employer' | 'candidate'

    // Do NOT block site globally; show auth dialog from layout instead

    // Protect admin routes for employers only
    if (to.path.startsWith('/admin')) {
      if (!token || type !== 'employer') {
        return next({ path: '/auth/login', query: { redirect: to.fullPath } })
      }
    }

    // If logged in, avoid going back to auth pages
    if ((to.path.startsWith('/auth/login') || to.path.startsWith('/auth/register')) && token) {
      return next(type === 'employer' ? '/admin' : '/jobs')
    }

    next()
  })

  return Router
})
