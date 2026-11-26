<script setup lang="ts">
import type { InvoiceStatus } from '#shared/types'
import { INVOICE_STATUS_LABELS } from '#shared/utils/invoice-status'

defineProps<{ status: InvoiceStatus }>()
</script>

<template>
  <span
    class="badge"
    :data-status="status"
  >{{ INVOICE_STATUS_LABELS[status] }}</span>
</template>

<style scoped>
.badge {
  display: inline-block;
  padding: 3px 9px;
  border-radius: 3px;
  font-size: var(--text-xs);
  font-weight: 600;
  white-space: nowrap;
}

.badge[data-status='draft'] { background: var(--status-draft-bg); color: var(--status-draft); }
.badge[data-status='issued'] { background: var(--status-issued-bg); color: var(--status-issued); }
.badge[data-status='overdue'] { background: var(--status-overdue-bg); color: var(--status-overdue); }
.badge[data-status='reminded'] { background: var(--status-reminded-bg); color: var(--status-reminded); }
.badge[data-status='paid'] { background: var(--status-paid-bg); color: var(--status-paid); }
.badge[data-status='disputed'] { background: var(--status-disputed-bg); color: var(--status-disputed); }
.badge[data-status='written_off'] { background: var(--status-written-off-bg); color: var(--status-written-off); }
</style>
