<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, useForm} from '@inertiajs/vue3';

const form = useForm({
  token: '',
  email: '',
});

// Verify token
function verifyToken() {
  form.email = email.value
  form.token = token.value

  form.post(route('auth.verify.token'), {
    onError: () => {
      form.reset('token')
    }
  })
}
</script>

<template>
  <GuestLayout>
    <Head title="Register"/>

    <form @submit.prevent="verifyToken">
      <div class="mt-4">
        <InputLabel for="email" value="Email"/>

        <TextInput
          id="email"
          type="email"
          class="mt-1 block w-full"
          v-model="form.email"
        />

        <InputError class="mt-2" :message="form.errors.email"/>
      </div>

      <div class="mt-4">
        <InputLabel for="token" value="Verification Token"/>

        <TextInput
          id="token"
          type="text"
          class="mt-1 block w-full"
          v-model="form.token"
        />

        <InputError class="mt-2" :message="form.errors.token"/>
      </div>

      <div class="mt-4 flex items-center justify-end">
        <PrimaryButton
          class="ms-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing">
          Verify
        </PrimaryButton>
      </div>
    </form>
  </GuestLayout>
</template>
