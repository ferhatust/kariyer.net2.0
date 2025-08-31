const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', redirect: '/jobs' },
      { path: 'jobs', component: () => import('pages/JobsPage.vue') },
      { path: 'jobs/:id', component: () => import('pages/JobDetailPage.vue') },
      { path: 'applications', component: () => import('pages/ApplicationsPage.vue') },
      { path: 'profile', component: () => import('pages/ProfilePage.vue') },
    ],
  },

  // Auth
  { path: '/auth/login', component: () => import('pages/auth/LoginPage.vue') },
  { path: '/auth/register', component: () => import('pages/auth/RegisterPage.vue') },

  // Admin area (Employer panel)
  {
    path: '/admin',
    component: () => import('layouts/AdminLayout.vue'),
    children: [
      { path: '', component: () => import('pages/admin/DashboardPage.vue') },
      { path: 'records', component: () => import('pages/admin/RecordsPage.vue') },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes
