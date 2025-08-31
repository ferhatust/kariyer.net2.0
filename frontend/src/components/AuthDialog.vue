<template>
  <q-dialog v-model="modelValue" persistent maximized="false">
    <q-card style="min-width: 520px; max-width: 90vw">
      <q-card-section class="row items-center q-pb-xs">
        <div class="text-h6">Giriş / Kayıt</div>
        <q-space />
        <q-btn flat round dense icon="close" @click="close" />
      </q-card-section>
      <q-separator />

      <q-card-section>
        <q-tabs v-model="tab" dense class="text-primary" inline-label>
          <q-tab name="employer_login" icon="business" label="İşveren Giriş" />
          <q-tab name="candidate_login" icon="person" label="Aday Giriş" />
          <q-tab name="employer_register" icon="app_registration" label="İşveren Kayıt" />
          <q-tab name="candidate_register" icon="how_to_reg" label="Aday Kayıt" />
        </q-tabs>
        <q-separator />
        <q-tab-panels v-model="tab" animated>
          <q-tab-panel name="employer_login">
            <q-form @submit.prevent="loginEmployer">
              <q-input v-model="employerLogin.e_posta" type="email" label="E-posta" filled dense class="q-mb-sm" />
              <q-input v-model="employerLogin.sifre" type="password" label="Şifre" filled dense class="q-mb-md" />
              <q-btn :loading="loading" type="submit" color="primary" unelevated label="Giriş" class="full-width" />
            </q-form>
          </q-tab-panel>

          <q-tab-panel name="candidate_login">
            <q-form @submit.prevent="loginCandidate">
              <q-input v-model="candidateLogin.e_posta" type="email" label="E-posta" filled dense class="q-mb-sm" />
              <q-input v-model="candidateLogin.sifre" type="password" label="Şifre" filled dense class="q-mb-md" />
              <q-btn :loading="loading" type="submit" color="primary" unelevated label="Giriş" class="full-width" />
            </q-form>
          </q-tab-panel>

          <q-tab-panel name="employer_register">
            <q-form @submit.prevent="registerEmployer">
              <div class="row q-col-gutter-sm">
                <div class="col-12 col-sm-6">
                  <q-input v-model="employerReg.sirket_adi" label="Şirket Adı" filled dense :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="employerReg.e_posta" type="email" label="E-posta" filled dense :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="employerReg.sifre" type="password" label="Şifre" filled dense :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="employerReg.konum" label="Konum" filled dense />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="employerReg.telefon" label="Telefon" filled dense />
                </div>
                <div class="col-12">
                  <q-input v-model="employerReg.hakkinda" type="textarea" autogrow label="Hakkında" filled dense />
                </div>
              </div>
              <div class="q-mt-md">
                <q-btn :loading="loading" type="submit" color="primary" unelevated label="Kayıt Ol" class="full-width" />
              </div>
            </q-form>
          </q-tab-panel>

          <q-tab-panel name="candidate_register">
            <q-form @submit.prevent="registerCandidate">
              <div class="row q-col-gutter-sm">
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidateReg.ad" label="Ad" filled dense :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidateReg.soyad" label="Soyad" filled dense :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidateReg.e_posta" type="email" label="E-posta" filled dense :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidateReg.sifre" type="password" label="Şifre" filled dense :rules="[v=>!!v||'Zorunlu']" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input v-model="candidateReg.telefon" label="Telefon" filled dense />
                </div>
              </div>
              <div class="q-mt-md">
                <q-btn :loading="loading" type="submit" color="primary" unelevated label="Kayıt Ol" class="full-width" />
              </div>
            </q-form>
          </q-tab-panel>
        </q-tab-panels>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, toRefs } from 'vue'
import { Notify } from 'quasar'
import { api } from 'boot/axios'

const props = defineProps({ modelValue: { type: Boolean, default: false } })
const emit = defineEmits(['update:modelValue', 'authenticated'])
const { modelValue } = toRefs(props)

const tab = ref('employer_login')
const loading = ref(false)

const employerLogin = ref({ e_posta: '', sifre: '' })
const candidateLogin = ref({ e_posta: '', sifre: '' })
const employerReg = ref({ sirket_adi: '', e_posta: '', sifre: '', konum: '', telefon: '', hakkinda: '' })
const candidateReg = ref({ ad: '', soyad: '', e_posta: '', sifre: '', telefon: '' })

function close () { emit('update:modelValue', false) }

async function loginEmployer () {
  loading.value = true
  try {
    const { data } = await api.post('/isverenler/login', employerLogin.value)
    localStorage.setItem('api_token', data.token)
    localStorage.setItem('auth_type', 'employer')
    Notify.create({ type: 'positive', message: 'Giriş başarılı' })
    emit('authenticated', { type: 'employer' })
    close()
  } catch (err) {
    console.error('Employer login failed', err?.response?.data || err)
    Notify.create({ type: 'negative', message: 'Giriş başarısız' })
  } finally { loading.value = false }
}

async function loginCandidate () {
  loading.value = true
  try {
    const { data } = await api.post('/kullanicilar/login', candidateLogin.value)
    localStorage.setItem('api_token', data.token)
    localStorage.setItem('auth_type', 'candidate')
    Notify.create({ type: 'positive', message: 'Giriş başarılı' })
    emit('authenticated', { type: 'candidate' })
    close()
  } catch (err) {
    console.error('Candidate login failed', err?.response?.data || err)
    Notify.create({ type: 'negative', message: 'Giriş başarısız' })
  } finally { loading.value = false }
}

async function registerEmployer () {
  loading.value = true
  try {
    const created = await api.post('/isverenler', employerReg.value)
    const loginResp = await api.post('/isverenler/login', { e_posta: employerReg.value.e_posta, sifre: employerReg.value.sifre })
    localStorage.setItem('api_token', loginResp.data.token)
    localStorage.setItem('auth_type', 'employer')
    try {
      const emp = created?.data
      if (emp?.id) localStorage.setItem('employer_id', String(emp.id))
      if (emp?.sirket_adi) localStorage.setItem('employer_name', emp.sirket_adi)
    } catch (e) { void e }
    Notify.create({ type: 'positive', message: 'Kayıt başarılı' })
    emit('authenticated', { type: 'employer' })
    close()
  } catch (err) {
    const msg = err?.response?.data?.message || 'Kayıt başarısız'
    console.error('Employer register failed', err?.response?.data || err)
    Notify.create({ type: 'negative', message: msg })
  } finally { loading.value = false }
}

async function registerCandidate () {
  loading.value = true
  try {
    await api.post('/kullanicilar', candidateReg.value)
    const loginResp = await api.post('/kullanicilar/login', { e_posta: candidateReg.value.e_posta, sifre: candidateReg.value.sifre })
    localStorage.setItem('api_token', loginResp.data.token)
    localStorage.setItem('auth_type', 'candidate')
    Notify.create({ type: 'positive', message: 'Kayıt başarılı' })
    emit('authenticated', { type: 'candidate' })
    close()
  } catch (err) {
    const msg = err?.response?.data?.message || 'Kayıt başarısız'
    console.error('Candidate register failed', err?.response?.data || err)
    Notify.create({ type: 'negative', message: msg })
  } finally { loading.value = false }
}
</script>
