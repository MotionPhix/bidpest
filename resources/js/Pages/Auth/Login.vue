<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {Head, Link, router, useForm, usePage} from '@inertiajs/vue3';
import {computed, ref} from "vue";
import {Separator} from "@/Components/ui/separator";

defineProps({
  status: {
    type: String,
  },
});

const form = useForm({
  email: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};

// Page properties
const page = usePage()

// State management
const stage = ref('email') // 'email' or 'token'
const email = ref('')
const token = ref('')

// Computed properties for messages
const status = computed(() => page.props.status)
const error = computed(() => page.props.error)
const suggestSocialLogin = computed(() => page.props.suggestSocialLogin)

// Form for email initiation
const emailForm = useForm({
  email: ''
})

// Form for token verification
const tokenForm = useForm({
  email: '',
  token: ''
})

// Initiate login by sending token
function initiateLogin() {
  emailForm.post(route('auth.log.user'), {
    onSuccess: () => {
      stage.value = 'token'
      email.value = emailForm.email
    }
  })
}

// Verify token
function verifyToken() {
  tokenForm.email = email.value
  tokenForm.token = token.value

  tokenForm.post(route('auth.verify.token'), {
    onError: () => {
      token.value = '' // Clear token on error
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

        <PrimaryButton
          type="button"
          @click="socialLogin('google')">
          Continue with Google
        </PrimaryButton>

        <PrimaryButton
          type="button"
          @click="socialLogin('facebook')">
          Continue with Facebook
        </PrimaryButton>

      </div>

      <Separator label="Or" class="my-4" />

      <div>
        <InputLabel for="email" value="Email"/>

        <TextInput
          id="email"
          type="email"
          class="mt-1 block w-full"
          v-model="form.email"
          required
          autofocus
          autocomplete="username"
        />

        <InputError class="mt-2" :message="form.errors.email"/>

        <div class="mt-4 block">
          <label class="flex items-center">
            <Checkbox name="remember" v-model:checked="form.remember"/>
            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400"
            >Remember me</span
            >
          </label>
        </div>
      </div>

      <div class="mt-4 flex items-center justify-end">
        <PrimaryButton
          class="ms-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing">
          Log in
        </PrimaryButton>
      </div>
    </form>

    <div class="passwordless-login">

      <!-- Email Input Stage -->
      <div class="login-container">
        <h2>Passwordless Login</h2>

        <!-- Error Message -->
        <div v-if="error" class="error-message">
          {{ error }}

          <!-- Suggest Social Login if email not found -->
          <div v-if="suggestSocialLogin" class="social-login-suggestion">
            <p>Would you like to login with:</p>
          </div>
        </div>

        <!-- Email Input -->
        <form v-if="stage === 'email'" @submit.prevent="initiateLogin">
          <InputLabel for="email" value="Email Address"/>
          <TextInput
            id="email"
            type="email"
            v-model="emailForm.email"
            required
            autofocus
            placeholder="Enter your email"
          />
          <PrimaryButton class="mt-4">Send Login Token</PrimaryButton>
        </form>

        <!-- Token Verification -->
        <form v-else-if="stage === 'token'" @submit.prevent="verifyToken">
          <InputLabel for="token" value="Verification Token"/>
          <TextInput
            id="token"
            type="text"
            v-model="token"
            required
            autofocus
            placeholder="Enter token sent to your email"
          />
          <PrimaryButton class="mt-4">Verify Token</PrimaryButton>
        </form>
      </div>
    </div>
  </GuestLayout>
</template>

<style scoped>
.passwordless-login {
  max-width: 400px;
  margin: auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #f9f9f9;
}

.success-message {
  color: green;
  margin-bottom: 10px;
}

.error-message {
  color: red;
  margin-bottom: 10px;
}

.social-login-suggestion {
  margin-top: 10px;
}
</style>
