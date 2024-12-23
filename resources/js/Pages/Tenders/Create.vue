<template>
  <div
    v-motion-container
    class="tender-upload-container"
    :initial="{ opacity: 0 }"
    :enter="{
      opacity: 1,
      transition: {
        staggerChildren: 0.1
      }
    }"
  >
    <div
      v-motion-item
      class="upload-card bg-white shadow-lg rounded-lg p-8 max-w-2xl mx-auto"
    >
      <h1
        v-motion-item
        class="text-3xl font-bold mb-6 text-center text-gray-800"
      >
        Create New Tender Document
      </h1>

      <form @submit.prevent="handleSubmit">
        <div class="space-y-6">
          <!-- Title Input -->
          <div
            v-motion-item
            class="form-group"
          >
            <Label>Tender Title</Label>
            <Input
              v-model="form.title"
              placeholder="Enter tender title"
              :class="{ 'border-red-500': errors.title }"
              v-motion
              :hover="{
                scale: 1.02,
                transition: { type: 'spring' }
              }"
            />
            <span
              v-if="errors.title"
              class="text-red-500 text-sm mt-1"
            >
              {{ errors.title }}
            </span>
          </div>

          <!-- Client Name Input -->
          <div
            v-motion-item
            class="form-group"
          >
            <Label>Client Name</Label>
            <Input
              v-model="form.client_name"
              placeholder="Enter client name"
              :class="{ 'border-red-500': errors.client_name }"
              v-motion
              :hover="{
                scale: 1.02,
                transition: { type: 'spring' }
              }"
            />
            <span
              v-if="errors.client_name"
              class="text-red-500 text-sm mt-1"
            >
              {{ errors.client_name }}
            </span>
          </div>

          <!-- Submission Deadline -->
          <div
            v-motion-item
            class="form-group"
          >
            <Label>Submission Deadline</Label>
            <Popover>
              <PopoverTrigger as-child>
                <Button
                  variant="outline"
                  :class="cn(
                    'w-full justify-start text-left font-normal',
                    !form.submission_deadline && 'text-muted-foreground'
                  )"
                  v-motion
                  :hover="{
                    scale: 1.02,
                    transition: { type: 'spring' }
                  }"
                >
                  {{
                    form.submission_deadline
                      ? format(form.submission_deadline, 'PPP')
                      : 'Pick a date'
                  }}
                  <CalendarIcon class="ml-auto h-4 w-4 opacity-50"/>
                </Button>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <Calendar
                  @update:modelValue="handleDateSelect"
                />
              </PopoverContent>
            </Popover>
          </div>

          <!-- Description Textarea -->
          <div
            v-motion-item
            class="form-group"
          >
            <Label>Tender Description</Label>
            <Textarea
              v-model="form.description"
              placeholder="Enter detailed tender description"
              :class="{ 'border-red-500': errors.description }"
              v-motion
              :hover="{
                scale: 1.01,
                transition: { type: 'spring' }
              }"
            />
            <span
              v-if="errors.description"
              class="text-red-500 text-sm mt-1"
            >
              {{ errors.description }}
            </span>
          </div>

          <!-- Dynamic Requirements -->
          <div
            v-motion-item
            class="form-group"
          >
            <Label>Tender Requirements</Label>
            <div
              v-for="(req, index) in form.requirements"
              :key="index"
              class="flex items-center space-x-2 mb-2"
              v-motion-item
            >
              <Input
                v-model="form.requirements[index]"
                :placeholder="`Requirement ${index + 1}`"
                v-motion
                :hover="{
                  scale: 1.02,
                  transition: { type: 'spring' }
                }"
              />
              <Button
                variant="destructive"
                size="icon"
                @click="removeRequirement(index)"
                v-motion
                :hover="{
                  scale: 1.1,
                  transition: { type: 'spring' }
                }"
              >
                <Trash2Icon class="h-4 w-4"/>
              </Button>
            </div>

            <Button
              variant="secondary"
              @click="addRequirement"
              class="mt-2"
              v-motion
              :hover="{
                scale: 1.05,
                transition: { type: 'spring' }
              }"
            >
              <PlusIcon class="mr-2 h-4 w-4"/>
              Add Requirement
            </Button>
          </div>

          <!-- Submit Button -->
          <div
            v-motion-item
            class="flex justify-center mt-6"
          >
            <Button
              type="submit"
              :disabled="form.processing"
              v-motion
              :initial="{ opacity: 0, scale: 0.8 }"
              :enter="{
                opacity: 1,
                scale: 1,
                transition: { type: 'spring' }
              }"
              :hover="{
                scale: 1.05,
                transition: { type: 'spring' }
              }"
              :press="{
                scale: 0.95,
                transition: { type: 'spring' }
              }"
            >
              {{ form.processing ? 'Creating...' : 'Create Tender Document' }}
            </Button>
          </div>
        </div>
      </form>
    </div>

    <!-- AI Assistance Floating Button -->
    <div
      v-motion-item
      class="fixed bottom-6 right-6"
    >
      <Tooltip>
        <TooltipTrigger>
          <Button
            variant="outline"
            size="icon"
            v-motion
            :hover="{
              rotate: 360,
              scale: 1.1,
              transition: {
                type: 'spring',
                duration: 0.5
              }
            }"
          >
            <SparklesIcon class="h-6 w-6 text-primary"/>
          </Button>
        </TooltipTrigger>
        <TooltipContent>
          AI can help you draft and analyze your tender document!
        </TooltipContent>
      </Tooltip>
    </div>
  </div>
</template>

<script setup>
import {ref} from 'vue'
import {useForm} from '@inertiajs/vue3'
import { useMotions } from '@vueuse/motion'
import {format} from 'date-fns'
import {
  CalendarIcon,
  PlusIcon,
  Trash2Icon,
  SparklesIcon
} from 'lucide-vue-next'

// Shadcn Components
import {Button} from '@/Components/ui/button'
import {Input} from '@/Components/ui/input'

```vue
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Popover, PopoverTrigger, PopoverContent } from '@/Components/ui/popover'
import { Calendar } from '@/Components/ui/calendar'
import { Tooltip, TooltipTrigger, TooltipContent } from '@/Components/ui/tooltip'

const form = useForm({
  title: '',
  client_name: '',
  submission_deadline: null,
  description: '',
  requirements: [''],
  processing: false,
})

const errors = ref({})

function handleSubmit() {
  form.processing = true
  // Simulate form submission
  setTimeout(() => {
    form.processing = false
    // Handle successful submission logic here
  }, 2000)
}

function handleDateSelect(date) {
  form.submission_deadline = date
}

function addRequirement() {
  form.requirements.push('')
}

function removeRequirement(index) {
  form.requirements.splice(index, 1)
}
</script>

<style scoped>
.tender-upload-container {
  padding: 2rem;
  background-color: #f9fafb;
  border-radius: 0.5rem;
}
</style>
