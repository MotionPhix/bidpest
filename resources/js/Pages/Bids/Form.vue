<template>
  <AuthenticatedLayout>
    <div>
      <h1>Create Tender Document</h1>
      <form @submit.prevent="submit">
        <InputLabel for="title" value="Title" />
        <TextInput v-model="form.title" required />

        <InputLabel for="client_name" value="Client Name" />
        <TextInput v-model="form.client_name" required />

        <InputLabel for="submission_deadline" value="Submission Deadline" />
        <TextInput type="date" v-model="form.submission_deadline" required />

        <InputLabel for="description" value="Description" />
        <TextInput v-model="form.description" />

        <InputLabel for="requirements" value="Requirements " />
        <TextInput v-model="form.requirements" placeholder="Enter requirements as JSON" />

        <PrimaryButton class="mt-4">Create Tender Document</PrimaryButton>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const form = useForm({
  title: '',
  client_name: '',
  submission_deadline: '',
  description: '',
  requirements: ''
})

function submit() {
  form.post(route('bid-documents.store'))
}
</script>
