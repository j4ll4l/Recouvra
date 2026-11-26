<script setup lang="ts">
// Vitrine des composants UI, disponible seulement en développement (npm run dev)
if (!import.meta.dev) {
  throw createError({ statusCode: 404, statusMessage: 'Page introuvable' })
}

useSeoMeta({ title: 'Composants UI | Recouvra', robots: 'noindex' })

const email = ref('')
const emailError = computed(() => (email.value && !email.value.includes('@') ? 'Entrez une adresse email valide.' : ''))
const modalOpen = ref(false)
const tab = ref('all')
const tabs = [
  { value: 'all', label: 'Toutes', count: 14 },
  { value: 'overdue', label: 'Échues', count: 5 },
  { value: 'paid', label: 'Payées', count: 4 },
]
</script>

<template>
  <div class="container demo">
    <h1>Composants UI</h1>

    <UiCard>
      <h2>Boutons</h2>
      <div class="row">
        <UiButton>Primaire</UiButton>
        <UiButton variant="dark">
          Sombre
        </UiButton>
        <UiButton variant="outline">
          Contour
        </UiButton>
        <UiButton variant="danger">
          Supprimer
        </UiButton>
        <UiButton loading>
          Envoi…
        </UiButton>
        <UiButton
          to="/"
          size="lg"
        >
          Lien vers l’accueil
        </UiButton>
      </div>
    </UiCard>

    <UiCard>
      <h2>Champ</h2>
      <UiField
        v-slot="{ id, describedby, invalid }"
        label="Email du client"
        hint="Les relances partent à cette adresse."
        :error="emailError"
        required
      >
        <input
          :id="id"
          v-model="email"
          type="email"
          :aria-describedby="describedby"
          :aria-invalid="invalid"
        >
      </UiField>
    </UiCard>

    <UiCard>
      <h2>Onglets et statuts</h2>
      <UiTabs
        v-model="tab"
        :tabs="tabs"
        label="Filtrer les factures"
      />
      <p>Onglet choisi : {{ tab }}</p>
      <div class="row">
        <InvoiceStatusBadge status="draft" />
        <InvoiceStatusBadge status="issued" />
        <InvoiceStatusBadge status="overdue" />
        <InvoiceStatusBadge status="reminded" />
        <InvoiceStatusBadge status="paid" />
        <InvoiceStatusBadge status="disputed" />
        <InvoiceStatusBadge status="written_off" />
      </div>
    </UiCard>

    <UiAlert tone="success">
      Paiement enregistré.
    </UiAlert>
    <UiAlert tone="error">
      Le montant dépasse le reste dû.
    </UiAlert>

    <UiEmptyState
      title="Aucune facture pour l’instant"
      text="Ajoutez votre première facture : Recouvra s’occupe des relances."
    >
      <UiButton>Ajouter une facture</UiButton>
    </UiEmptyState>

    <UiButton
      variant="outline"
      @click="modalOpen = true"
    >
      Ouvrir la modale
    </UiButton>
    <UiModal
      v-model:open="modalOpen"
      title="Enregistrer un paiement"
    >
      <p>Contenu de la modale. Échap ou clic sur le fond pour fermer.</p>
      <UiButton @click="modalOpen = false">
        Valider
      </UiButton>
    </UiModal>

    <AppPagination
      :page="2"
      :total-pages="5"
    />

    <h2>Calcul des pénalités</h2>
    <PenaltyCalculator />
  </div>
</template>

<style scoped>
.demo {
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
  padding-block: var(--space-10);
}

.row {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-3);
  margin-top: var(--space-4);
}
</style>
