<template>
  <AuthenticatedLayout>
    <div>
      <h1>Tender Documents</h1>
      <PrimaryButton @click="createTenderDocument">Create Tender Document</PrimaryButton>
      <table>
        <thead>
        <tr>
          <th>Title</th>
          <th>Client Name</th>
          <th>Submission Deadline</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="document in tenderDocuments" :key="document.id">
          <td>{{ document.title }}</td>
          <td>{{ document.client_name }}</td>
          <td>{{ document.submission_deadline }}</td>
          <td>
            <PrimaryButton @click="generateBid(document)">Generate Bid</PrimaryButton>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const { props } = usePage()
const tenderDocuments = props.tenderDocuments

function createTenderDocument() {
  router.visit(route('bid-documents.create'))
}

function generateBid(document) {
  router.post(route('bids.generate', document.uuid))
}
</script>
