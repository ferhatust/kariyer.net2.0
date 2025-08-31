<template>
  <q-dialog v-model="modelValue" persistent>
    <q-card style="min-width: 520px; max-width: 90vw">
      <q-card-section class="row items-center no-wrap">
        <div class="text-h6">Hızlı Aksiyonlar</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>
      <q-separator />
      <q-card-section>
        <div class="row q-col-gutter-md">
          <div class="col-12 col-sm-6">
            <q-btn class="action-btn" color="primary" unelevated icon="add_business" label="Yeni İlan Oluştur" @click="goCreateJob" />
          </div>
          <div class="col-12 col-sm-6">
            <q-btn class="action-btn" color="secondary" outline icon="work" label="İlanları Keşfet" @click="goJobs" />
          </div>
          <div class="col-12 col-sm-6">
            <q-btn class="action-btn" color="primary" outline icon="dashboard" label="Admin Paneli" @click="goAdmin" />
          </div>
          <div class="col-12 col-sm-6">
            <q-btn class="action-btn" color="secondary" outline icon="person" label="Profilim" @click="goProfile" />
          </div>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue', 'create:job'])
const router = useRouter()

const modelValue = computed({
  get: () => props.modelValue,
  set: (v) => emit('update:modelValue', v),
})

function goCreateJob() {
  emit('create:job')
  emit('update:modelValue', false)
}
function goJobs() {
  router.push('/jobs')
  emit('update:modelValue', false)
}
function goAdmin() {
  router.push('/admin')
  emit('update:modelValue', false)
}
function goProfile() {
  router.push('/profile')
  emit('update:modelValue', false)
}
</script>

<style scoped>
.action-btn {
  width: 100%;
  height: 56px;
  border-radius: 10px;
}
</style>
