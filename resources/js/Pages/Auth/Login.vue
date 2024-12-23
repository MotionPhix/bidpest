<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, router, useForm} from '@inertiajs/vue3';
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
  emailForm.post(route('auth.log.user'), {
    onSuccess: () => {
      stage.value = 'token'
      email.value = emailForm.email
    }
  })
}

// Social login redirect
function socialLogin(provider) {
  router.visit(route('socialite.redirect', { provider: provider }))
}
</script>

<template>
  <GuestLayout>
    <Head title="Log in"/>

    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit" class="grid gap-4">
      <div class="grid gap-4 grid-cols-2">

        <Button
          type="button"
          class="btn btn-primary"
          @click="socialLogin('google')">
          Continue with Google
        </Button>

        <Button
          type="button"
          class="btn btn-primary"
          @click="socialLogin('facebook')">
          Continue with Facebook
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

        <div class="mt-4 block">
          <label class="flex items-center">
            <Checkbox name="remember" v-model:checked="form.remember"/>

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
