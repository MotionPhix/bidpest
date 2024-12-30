<script setup>
import { computed } from 'vue'
import {Link} from '@inertiajs/vue3'

const props = defineProps({ status: Number })

const title = computed(() => {
  return {
    503: '503: Service Unavailable',
    500: '500: Server Error',
    404: '404: Page Not Found',
    403: '403: Forbidden',
  }[props.status]
})

const description = computed(() => {
  return {
    503: 'Sorry, we are doing some maintenance. Please check back soon.',
    500: 'Whoops, something went wrong on our servers.',
    404: 'Sorry, We could not verify your authenticity.',
    403: 'Sorry, you are forbidden from accessing this page.',
  }[props.status]
})
</script>

<template>
  <div class="flex items-center justify-center h-screen bg-gray-100">
    <div class="text-center p-10 bg-white rounded-lg shadow-lg">
      <h1 class="text-3xl font-bold mb-4 text-red-500">{{ title }}</h1>
      <p class="text-lg text-gray-600 mb-6">{{ description }}</p>
      <Link :href="route('dashboard')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Go back to Dashboard
      </Link>
    </div>
  </div>
</template>
