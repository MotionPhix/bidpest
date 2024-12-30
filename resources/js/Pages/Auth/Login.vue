<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, useForm} from '@inertiajs/vue3';
import {Separator} from "@/Components/ui/separator";
import {Button} from "@/Components/ui/button";

defineProps({
  status: {
    type: String,
  },
});

const form = useForm({
  email: '',
  remember: false,
});

// Initiate login by sending token
function submit() {
  form.post(route('auth.log.user'), {
    onSuccess: () => {
      console.log('done')
    }
  })
}

// Social login redirect
function socialLogin(provider) {
  window.location.href = route('socialite.redirect', { provider: provider })
}
</script>

<template>
  <GuestLayout>
    <Head title="Log in"/>

    <form @submit.prevent="submit" class="grid gap-4">
      <div class="grid gap-4 grid-cols-2">

        <Button
          size="xl"
          type="button"
          @click="socialLogin('google')">
          <svg class="w-4 h-auto" width="46" height="47" viewBox="0 0 46 47" fill="none">
            <path d="M46 24.0287C46 22.09 45.8533 20.68 45.5013 19.2112H23.4694V27.9356H36.4069C36.1429 30.1094 34.7347 33.37 31.5957 35.5731L31.5663 35.8669L38.5191 41.2719L38.9885 41.3306C43.4477 37.2181 46 31.1669 46 24.0287Z" fill="#4285F4"/>
            <path d="M23.4694 47C29.8061 47 35.1161 44.9144 39.0179 41.3012L31.625 35.5437C29.6301 36.9244 26.9898 37.8937 23.4987 37.8937C17.2793 37.8937 12.0281 33.7812 10.1505 28.1412L9.88649 28.1706L2.61097 33.7812L2.52296 34.0456C6.36608 41.7125 14.287 47 23.4694 47Z" fill="#34A853"/>
            <path d="M10.1212 28.1413C9.62245 26.6725 9.32908 25.1156 9.32908 23.5C9.32908 21.8844 9.62245 20.3275 10.0918 18.8588V18.5356L2.75765 12.8369L2.52296 12.9544C0.909439 16.1269 0 19.7106 0 23.5C0 27.2894 0.909439 30.8731 2.49362 34.0456L10.1212 28.1413Z" fill="#FBBC05"/>
            <path d="M23.4694 9.07688C27.8699 9.07688 30.8622 10.9863 32.5344 12.5725L39.1645 6.11C35.0867 2.32063 29.8061 0 23.4694 0C14.287 0 6.36607 5.2875 2.49362 12.9544L10.0918 18.8588C11.9987 13.1894 17.25 9.07688 23.4694 9.07688Z" fill="#EB4335"/>
          </svg>
          Login with Google
        </Button>

        <Button
          size="xl"
          type="button"
          class="btn btn-primary"
          @click="socialLogin('facebook')">
          Login with Facebook
        </Button>

      </div>

      <Separator label="OR" class="my-4" />

      <div>
        <InputLabel for="email" value="Email"/>

        <TextInput
          id="email"
          type="email"
          class="mt-1 block w-full"
          v-model="form.email"
          placeholder="Enter your email address"
          autofocus
          autocomplete="username"
        />

        <InputError class="mt-2" :message="form.errors.email"/>
        <InputError class="mt-2" :message="form.errors.error" />

        <div class="mt-4 block">
          <label class="flex items-center">
            <Checkbox
              class="w-5 h-5"
              name="remember"
              v-model:checked="form.remember"/>

            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">
              Remember me
            </span>

          </label>
        </div>
      </div>

      <div class="mt-4 flex items-center justify-end">
        <Button
          class="btn"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing || !form.email.length">
          Log in
        </Button>
      </div>
    </form>
  </GuestLayout>
</template>
