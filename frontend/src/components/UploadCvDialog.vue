<template>
  <q-dialog v-model="model">
    <q-card style="min-width: 520px; max-width: 92vw">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">CV Yükle</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>
      <q-separator />

      <q-card-section>
        <div class="text-body2 q-mb-sm">PDF, DOC veya DOCX formatında maksimum 5 MB.</div>
        <q-file
          v-model="file"
          label="Dosya seç"
          accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
          filled
          clearable
          :counter="true"
          :max-files="1"
          :filter="filterSize"
          @rejected="onRejected"
        >
          <template #prepend>
            <q-icon name="upload_file" />
          </template>
        </q-file>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat label="İptal" v-close-popup />
        <q-btn color="primary" :disable="!file || uploading" :loading="uploading" label="Yükle" @click="doUpload" />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Notify } from 'quasar'

const props = defineProps({
  modelValue: { type: Boolean, default: false }
})
const emit = defineEmits(['update:modelValue', 'uploaded'])

const model = ref(props.modelValue)
watch(() => props.modelValue, v => { model.value = v })
watch(model, v => emit('update:modelValue', v))

const file = ref(null)
const uploading = ref(false)

function filterSize (files) {
  const limit = 5 * 1024 * 1024 // 5MB
  return files.filter(f => f.size <= limit)
}

function onRejected (details) {
  if (details?.failedPropValidation === 'filter') {
    Notify.create({ type: 'warning', message: 'Dosya 5 MB sınırını aşıyor.' })
  } else {
    Notify.create({ type: 'warning', message: 'Dosya kabul edilmedi.' })
  }
}

async function doUpload () {
  if (!file.value) return
  try {
    uploading.value = true
    // TODO: backend hazırsa burada gerçek yükleme yapılır (FormData + api.post)
    await new Promise(r => setTimeout(r, 900))
    Notify.create({ type: 'positive', message: 'CV başarıyla yüklendi.' })
    emit('uploaded', { name: file.value.name, size: file.value.size })
    model.value = false
    file.value = null
  } catch (e) {
    void e
    Notify.create({ type: 'negative', message: 'Yükleme başarısız.' })
  } finally {
    uploading.value = false
  }
}
</script>
