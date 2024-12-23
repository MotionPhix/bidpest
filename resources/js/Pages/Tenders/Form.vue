<template>
  <TransitionRoot>
    <div class="tender-upload-container">
      <motion.div
        initial={{ opacity: 0, y: 50 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
        class="upload-card"
      >
        <h1 class="text-2xl font-bold mb-6">Create New Tender Document</h1>

        <Form @submit="handleSubmit">
          <FormField name="title">
            <FormLabel>Tender Title</FormLabel>
            <FormControl>
              <Input
                v-model="form.title"
                placeholder="Enter tender title"
                :class="{ 'border-red-500': errors.title }"
              />
            </FormControl>
            <FormMessage v-if="errors.title">
              {{ errors.title }}
            </FormMessage>
          </FormField>

          <FormField name="client_name">
            <FormLabel>Client Name</FormLabel>
            <FormControl>
              <Input
                v-model="form.client_name"
                placeholder="Enter client name"
                :class="{ 'border-red-500': errors.client_name }"
              />
            </FormControl>
            <FormMessage v-if="errors.client_name">
              {{ errors.client_name }}
            </FormMessage>
          </FormField>

          <FormField name="submission_deadline">
            <FormLabel>Submission Deadline</FormLabel>
            <Popover>
              <PopoverTrigger as-child>
                <FormControl>
                  <Button
                    variant="outline"
                    :class="cn(
                      'w-full pl-3 text-left font-normal',
                      !form.submission_deadline && 'text-muted-foreground'
                    )"
                  >
                    {{ form.submission_deadline
                    ? format(form.submission_deadline, 'PPP')
                    : 'Pick a deadline' }}
                    <CalendarIcon class="ml-auto h-4 w-4 opacity-50" />
                  </Button>
                </FormControl>
              </PopoverTrigger>
              <PopoverContent class="w-auto p-0">
                <Calendar
                  :month="month"
                  :year="year"
                  @update:month="month = $event"
                  @update:year="year = $event"
                  @day-click="handleDateSelect"
                />
              </PopoverContent>
            </Popover>
            <FormMessage v-if="errors.submission_deadline">
              {{ errors.submission_deadline }}
            </FormMessage>
          </FormField>

          <FormField name="description">
            <FormLabel>Tender Description</FormLabel>
            <FormControl>
              <Textarea
                v-model="form.description"
                placeholder="Enter detailed tender description"
                :class="{ 'border-red-500': errors.description }"
              />
            </FormControl>
            <FormMessage v-if="errors.description">
              {{ errors.description }}
            </FormMessage>
          </FormField>

          <FormField name="requirements">
            <FormLabel>Tender Requirements</FormLabel>
            <div class="requirements-container">
              <div
                v-for="(req, index) in form.requirements"
                :key="index"
                class="requirement-item"
              >
                <Input
                  v-model="form.requirements[index]"
                  placeholder="Enter requirement"
                />
                <Button
                  variant="destructive"
                  size="icon"
                  @click="removeRequirement(index)"
                >
                  <Trash2Icon class="h-4 w-4" />
                </Button>
              </div>
              <Button
                variant="secondary"
                @click="addRequirement"
                class="mt-2"
              >
                <PlusIcon class="mr-2 h-4 w-4" /> Add Requirement
              </Button>
            </div>
          </FormField>

          <div class="upload-actions">
            <Button
              type="submit"
              :disabled="form.processing"
            >
              {{ form.processing ? 'Creating...' : 'Create Tender Document' }}
            </Button>
          </div>
        </Form>
      </motion.div>

      <!-- AI Assistance Tooltip -->
      <motion.div
        initial={{ opacity: 0, x: 50 }}
        animate={{ opacity: 1, x: 0 }}
        transition={{ duration: 0.5, delay: 0.3 }}
        class="ai-assistance-tooltip"
      >
        <Tooltip>
          <TooltipTrigger>
            <Button variant="outline" size="icon">
              <SparklesIcon class="h-6 w-6 text-primary" />
            </Button>
          </TooltipTrigger>
          <TooltipContent>
            <p>AI can help you draft and analyze your tender document!</p>
          </TooltipContent>
        </Tooltip>
      </motion.div>
    </div>
  </TransitionRoot>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { format } from 'date-fns'
import {
  CalendarIcon,
  PlusIcon,
  Trash2Icon,
  SparklesIcon
} from 'lucide-vue-next'

// Shadcn Components
import {
  Form,
  FormControl,
  FormField,
  FormLabel,
  FormMessage
} from '@/Components/ui/form'
import { Input } from '@/Components/ui/input'
import { Button } from '@/Components/ui/button'
import { Textarea } from '@/Components/ui/textarea'
import {
  Popover,
  PopoverContent,
  PopoverTrigger
} from '@/Components/ui/popover'
import {
  Tooltip,
  TooltipContent,
  TooltipTrigger
} from '@/Components/ui/tooltip'
import { Calendar } from '@/Components/ui/calendar'
import { TransitionRoot } from '@headlessui/vue'
import { motion } from 'framer-motion'

const form = useForm({
  title: '',
  client_name: '',
  submission_deadline: null,
  description: '',
  requirements: ['']
})

const month = ref(new Date().getMonth())
const year = ref(new Date().getFullYear())

function handleDateSelect(day) {
  form.submission_deadline = day
}

function addRequirement() {
  form.requirements.push('')
}

function removeRequirement(index) {
  form.requirements.splice(index, 1)
}

function handleSubmit() {
  form.post(route('tender-documents.store'), {
    onSuccess: () => {
      // Show success notification
      toast.success('Tender Document Created Successfully!')
    },
    onError: (errors) => {
      // Show error notification
      toast.error('Please check the form for errors')
    }
  })
}
</script>

<style scoped>
.tender-upload-container {
  @apply max-w-2xl mx-auto p-6 relative;
}

.upload-card {
  @apply bg-white shadow-lg rounded-lg p-8;
}

.requirements-container .requirement-item {
  @apply flex items-center space-x-2 mb-2;
}

.ai-assistance-tooltip {
  @apply absolute top-4 right-4;
}
</style>
