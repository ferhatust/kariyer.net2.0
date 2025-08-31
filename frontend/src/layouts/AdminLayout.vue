<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-primary text-white">
      <q-toolbar>
        <q-btn flat dense round icon="menu" @click="toggleLeft" />
        <q-toolbar-title>İşveren Paneli</q-toolbar-title>
        <q-space />
        <q-btn flat dense round icon="logout" @click="logout" />
      </q-toolbar>
    </q-header>

    <q-drawer v-model="left" show-if-above bordered :mini="mini" @mouseover="mini=false" @mouseout="mini=true">
      <q-list padding>
        <q-item clickable v-ripple to="/admin" exact active-class="active-link">
          <q-item-section avatar><q-icon name="dashboard" /></q-item-section>
          <q-item-section>
            <q-item-label>Dashboard</q-item-label>
            <q-item-label caption>Genel bakış</q-item-label>
          </q-item-section>
        </q-item>
        <q-item clickable v-ripple to="/jobs" active-class="active-link">
          <q-item-section avatar><q-icon name="work" /></q-item-section>
          <q-item-section>
            <q-item-label>İlanlar</q-item-label>
            <q-item-label caption>İş ilanları listesi</q-item-label>
          </q-item-section>
        </q-item>

        <q-item clickable v-ripple to="/admin/records" active-class="active-link">
          <q-item-section avatar><q-icon name="people" /></q-item-section>
          <q-item-section>
            <q-item-label>Kayıtlar</q-item-label>
            <q-item-label caption>İşverenler ve adaylar</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container class="bg-grey-2">
      <div class="q-pa-md container">
        <router-view />
      </div>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const left = ref(false)
const mini = ref(true)
const router = useRouter()

function toggleLeft () { left.value = !left.value }

function logout () {
  localStorage.removeItem('api_token')
  localStorage.removeItem('auth_type')
  router.push('/auth/login')
}
</script>

<style scoped>
.container { max-width: 1200px; margin: 0 auto; }
.active-link { color: var(--q-primary); background: rgba(33,150,243,.08); border-left: 3px solid var(--q-primary); }
</style>
