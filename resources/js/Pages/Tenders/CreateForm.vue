<template>
  <div class="tender-upload-container">
    <div
      v-motion
      :initial="{
        opacity: 0,
        y: 50
      }"
      :enter="{
        opacity: 1,
        y: 0,
        transition: {
          type: 'spring',
          stiffness: 250,
          damping: 15
        }
      }"
      class="upload-card"
    >
      <h1
        v-motion
        :initial="{ opacity: 0, x: -50 }"
        :enter="{
          opacity: 1,
          x: 0,
          transition: {
            type: 'spring',
            delay: 200
          }
        }"
        class="text-3xl font-bold mb-6"
      >
        Create New Tender Document
      </h1>

      <form @submit.prevent="handleSubmit">
        <div class="space-y-6">
          <!-- Title Input -->
          <div class="form-group">
            <Label>Tender Title</Label>
            <Input
              v-motion
              v-model="form.title"
              :initial="{ opacity: 0, x: -20 }"
              :enter="{
                opacity: 1,
                x: 0,
                transition: {
                  type: 'spring',
                  delay: 300
                }
              }"
              :hover="{
                scale: 1.02,
                transition: { type: 'spring' }
              }"
              :tap="{
                scale: 0.98,
                transition: { type: 'spring' }
              }"
              placeholder="Enter tender title"
            />
          </div>

          <!-- Client Name Input -->
          <div class="form-group">
            <Label>Client Name</Label>
            <Input
              v-motion
              v-model="form.client_name"
              :initial="{ opacity: 0, x: -20 }"
              :enter="{
                opacity: 1,
                x: 0,
                transition: {
                  type: 'spring',
                  delay: 350
                }
              }"
              :hover="{
                scale: 1.02,
                transition: { type: 'spring' }
              }"
              :tap="{
                scale: 0.98,
                transition: { type: 'spring' }
              }"
              placeholder="Enter client name"
            />
          </div>

          <!-- Submission Deadline -->
          <div class="form-group">
            <Label>Submission Deadline</Label>
            <Button
              v-motion
              :initial="{ opacity: 0, x: -20 }"
              :enter="{
                opacity: 1,
                x: 0,
                transition: {
                  type: 'spring',
                  delay: 400
                }
              }"
              :hover="{
                scale: 1.02,
                transition: { type: 'spring' }
              }"
              :tap="{
                scale: 0.98,
                transition: { type: 'spring' }
              }"
              @click="openDatePicker"
            >
              {{ formatDate(form.submission_deadline) }}
            </Button>
          </div>

          <!-- Dynamic Requirements -->
          <div class="form-group">
            <Label>Tender Requirements</Label>
            <div
              v-for="(req, index) in form.requirements"
              :key="index"
              class="flex items-center space-x-2 mb-2"
            >
              <Input
                v-motion
                v-model="form.requirements[index]"
                :initial="{ opacity: 0, x: -20 }"
                :enter="{
                  opacity: 1,
                  x: 0,
                  transition: {
                    type: 'spring',
                    delay: 450 + (index * 50)
                  }
                }"
                :hover="{
                  scale: 1.02,
                  transition: { type: 'spring' }
                }"
                :tap="{
                  scale: 0.98,
                  transition: { type: 'spring' }
                }"
                :placeholder="`Requirement ${index + 1}`"
              />
              <Button
                v-motion
                variant="destructive"
                :hover="{
                  scale: 1.1,
                  transition: { type: 'spring' }
                }"
                :tap="{
                  scale: 0.9,
                  transition: { type: 'spring' }
                }"
                @click="removeRequirement(index)"
              >
                Remove
              </Button>
            </div>

            <Button
              v-motion
              :initial="{ opacity: 0, y: 20 }"
              :enter="{
                opacity: 1,
                y: 0,
                transition: {
                  type: 'spring',
                  delay: 600
                }
              }"
              :hover="{
                scale: 1.05,
                transition: { type: 'spring' }
              }"
              :tap="{
                scale: 0.95,
                transition: { type: 'spring' }
              }"
              @click="addRequirement"
            >
              Add Requirement
            </Button>
          </div>

          <!-- Submit Button -->
          <Button
            v-motion
            :initial="{ opacity: 0, scale: 0.8 }"
            :enter="{
              opacity: 1,
              scale: 1,
              transition: {
                type: 'spring',
                delay: 700
              }
            }"
            :hover="{
              scale: 1.05,
              transition: { type: 'spring' }
            }"
            :tap="{
              scale: 0.95,
              transition: { type: 'spring' }
            }"
            type="submit"
          >
            Create Tender Document
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { format } from 'date-fns'

const form = useForm({
  title: '',
  client_name: '',
  submission_deadline: null,
  requirements: ['']
})

function formatDate(date) {
  return date ? format(date, 'PPP') : 'Select Date'
}

function addRequirement() {
  form.requirements.push('')
}

function removeRequirement(index) {
  form.requirements.splice(index, 1)
}

function handleSubmit() {
  form.post(route('tender-documents.store'))
}

function openDatePicker() {
  // Logic to open the date picker
}
</script>

<style scoped>
.tender-upload-container {
  padding: 2rem;
  background-color: #f9fafb;
  border-radius: 0.5rem;
}
.upload-card {
  background-color: white;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  border-radius: 0.5rem;
  padding: 2rem;
}
</style>
