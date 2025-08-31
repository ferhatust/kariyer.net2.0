<template>
  <q-page class="q-pa-md flex flex-center">
    <q-card flat bordered style="width: 100%; max-width: 520px">
      <q-card-section>
        <div class="text-h6">Giriş Yap</div>
        <div class="text-caption text-grey-7">İşveren veya Aday olarak giriş yapın</div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <q-tabs v-model="tab" class="text-primary" inline-label>
          <q-tab name="employer" icon="business" label="İşveren" />
          <q-tab name="candidate" icon="person" label="Aday" />
        </q-tabs>
        <q-separator />
        <q-tab-panels v-model="tab" animated>
          <q-tab-panel name="employer">
            <q-form @submit.prevent="loginEmployer">
              <q-input v-model="employer.email" type="email" label="E-posta" filled class="q-mb-sm" />
              <q-input v-model="employer.password" type="password" label="Şifre" filled class="q-mb-md" />
              <q-btn :loading="loading" type="submit" color="primary" unelevated label="Giriş Yap" class="full-width" />
            </q-form>
          </q-tab-panel>
          <q-tab-panel name="candidate">
            <q-form @submit.prevent="loginCandidate">
              <q-input v-model="candidate.email" type="email" label="E-posta" filled class="q-mb-sm" />
              <q-input v-model="candidate.password" type="password" label="Şifre" filled class="q-mb-md" />
              <q-btn :loading="loading" type="submit" color="primary" unelevated label="Giriş Yap" class="full-width" />
            </q-form>
          </q-tab-panel>
        </q-tab-panels>
      </q-card-section>
      <q-separator />
      <q-card-section class="text-caption text-center">
        Hesabınız yok mu?
        <q-btn flat color="primary" to="/auth/register" label="Kayıt ol" />
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Notify } from 'quasar'
import { api } from 'boot/axios'

const router = useRouter()
const tab = ref('employer')
const loading = ref(false)

const employer = ref({ email: '', password: '' })
const candidate = ref({ email: '', password: '' })

async function loginEmployer () {
  loading.value = true
  try {
    const { data } = await api.post('/isverenler/login', { e_posta: employer.value.email, sifre: employer.value.password })
    localStorage.setItem('api_token', data.token)
    localStorage.setItem('auth_type', 'employer')
    Notify.create({ type: 'positive', message: 'Giriş başarılı' })
    const redirect = router.currentRoute.value.query?.redirect
    router.push(typeof redirect === 'string' && redirect ? redirect : '/admin')
  } catch (err) {
    console.error('Employer login failed', err?.response?.data || err)
    Notify.create({ type: 'negative', message: 'Giriş başarısız' })
  } finally {
    loading.value = false
  }
}

async function loginCandidate () {
  loading.value = true
  try {
    const { data } = await api.post('/kullanicilar/login', { e_posta: candidate.value.email, sifre: candidate.value.password })
    localStorage.setItem('api_token', data.token)
    localStorage.setItem('auth_type', 'candidate')
    Notify.create({ type: 'positive', message: 'Giriş başarılı' })
    const redirect = router.currentRoute.value.query?.redirect
    router.push(typeof redirect === 'string' && redirect ? redirect : '/jobs')
  } catch (err) {
    console.error('Employer login failed', err?.response?.data || err)
    Notify.create({ type: 'negative', message: 'Giriş başarısız' })
  } finally {
    loading.value = false
  }
}
</script>
