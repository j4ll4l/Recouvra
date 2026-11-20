<script setup lang="ts">
import type { InvoiceStatus } from '#shared/types'
import { INVOICE_STATUS_LABELS } from '#shared/utils/invoice-status'

defineProps<{ status: InvoiceStatus }>()
</script>

<template>
  <span :data-status="status">{{ INVOICE_STATUS_LABELS[status] }}</span>
</template>
