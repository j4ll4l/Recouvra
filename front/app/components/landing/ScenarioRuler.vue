<script setup lang="ts">
// Le mètre de chantier : chaque graduation jaune est une relance. On choisit un jour, l'email s'affiche.
const stops = [
  { day: 3, tone: 'Courtois', date: '20 oct.', why: 'Un oubli arrive à tout le monde : on rappelle, simplement.', subject: 'Facture F-2026-121 : petit rappel', body: 'Bonjour Monsieur Dupont,\nSauf erreur de notre part, la facture F-2026-121 de 5 480,00 € arrivait à échéance le 17 octobre. Si le règlement est en cours, ne tenez pas compte de ce message.\nBien cordialement, Julie Martin' },
  { day: 15, tone: 'Ferme', date: '1 nov.', why: 'Deux semaines sans nouvelles : on demande une date précise.', subject: 'Facture F-2026-121 toujours impayée', body: 'Bonjour Monsieur Dupont,\nNotre facture F-2026-121 de 5 480,00 € reste impayée malgré notre premier rappel. Merci de nous indiquer la date de règlement d’ici le 8 novembre.\nCordialement, Julie Martin' },
  { day: 30, tone: 'Formel', date: '16 nov.', why: 'Un mois : on rappelle les pénalités prévues par la loi.', subject: 'Dernier rappel avant mise en demeure', body: 'Monsieur,\nLa facture F-2026-121 est échue depuis le 17 octobre. Les pénalités de retard et l’indemnité forfaitaire de 40 € sont exigibles. Sans règlement sous 8 jours, nous engagerons une mise en demeure.\nJulie Martin' },
  { day: 45, tone: 'Mise en demeure', date: '1 déc.', why: 'Le courrier officiel, prêt à signer et à envoyer en recommandé.', subject: 'Mise en demeure de payer — F-2026-121', body: 'Monsieur,\nNous vous mettons en demeure de régler sous 8 jours la somme de 5 480,00 € TTC, majorée des pénalités de retard (art. L441-10 du Code de commerce) et de l’indemnité forfaitaire de 40 € (art. D441-5).\nJulie Martin, gérante' },
]
const MAX_DAY = 45

const selectedDay = ref(15)
const current = computed(() => stops.find(s => s.day === selectedDay.value)!)
const ticks = Array.from({ length: MAX_DAY + 1 }, (_, day) => ({
  day,
  kind: stops.some(s => s.day === day) ? 'stop' : day % 5 === 0 ? 'five' : 'one',
}))
</script>

<template>
  <section
    id="relances"
    class="section section--yellow"
    aria-labelledby="relances-titre"
  >
    <div class="container">
      <LandingSectionHead
        eyebrow="Relances automatiques"
        title="Le scénario de relance, au jour près."
        title-id="relances-titre"
      >
        Touchez une graduation : voici l’email que votre client reçoit ce jour-là.
      </LandingSectionHead>

      <div class="ruler">
        <div
          class="ruler__ticks"
          aria-hidden="true"
        >
          <span
            v-for="tick in ticks"
            :key="tick.day"
            :class="['ruler__tick', `ruler__tick--${tick.kind}`]"
          />
        </div>
        <div
          class="ruler__stops"
          role="group"
          aria-label="Jour de la relance"
        >
          <button
            v-for="stop in stops"
            :key="stop.day"
            type="button"
            class="ruler__stop"
            :style="{ left: `${(stop.day / MAX_DAY) * 100}%` }"
            :aria-pressed="stop.day === selectedDay"
            @click="selectedDay = stop.day"
          >
            J+{{ stop.day }}
          </button>
        </div>
      </div>

      <div
        class="detail"
        aria-live="polite"
      >
        <div class="detail__why">
          <p class="detail__day">
            J+{{ current.day }}
          </p>
          <p class="detail__tone">
            {{ current.tone }}
          </p>
          <p>{{ current.why }}</p>
        </div>
        <div class="email theme-dark">
          <div class="email__head">
            <span>De : Martin BTP via Recouvra · Répondre à : julie.martin@exemple.fr</span>
            <span>À : Dupont Rénovation · envoi le {{ current.date }}</span>
          </div>
          <div class="email__body">
            <p class="email__subject">
              {{ current.subject }}
            </p>
            <p class="email__text">
              {{ current.body }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.ruler {
  position: relative;
  height: 170px;
  overflow: hidden;
}

.ruler__ticks {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  height: 100px;
}

.ruler__tick {
  width: 2px;
  height: 28px;
  background: var(--color-ink);
}

.ruler__tick--five {
  height: 56px;
}

.ruler__tick--stop {
  width: 6px;
  height: 100px;
}

.ruler__stops {
  position: absolute;
  inset: 106px 0 0;
}

.ruler__stop {
  position: absolute;
  top: 0;
  height: var(--tap-min);
  min-width: 64px;
  padding: 0 var(--space-3);
  border: 0;
  border-radius: var(--radius-sm);
  background: var(--color-ink);
  color: var(--color-white);
  font-size: var(--text-lg);
  font-weight: 700;
  transform: translateX(-50%);
}

.ruler__stop:first-child {
  transform: translateX(-20%);
}

.ruler__stop:last-child {
  transform: translateX(-100%);
}

.ruler__stop[aria-pressed='true'] {
  background: var(--color-white);
  color: var(--color-ink);
  box-shadow: 0 0 0 2px var(--color-ink);
}

.detail {
  display: grid;
  gap: var(--space-6);
  margin-top: var(--space-6);
}

.detail__why {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.detail__day {
  font-size: 4rem;
  font-weight: 800;
  line-height: 1;
  letter-spacing: -0.05em;
}

.detail__tone {
  font-size: var(--text-xl);
  font-weight: 700;
}

.email {
  border-radius: var(--radius-md);
  overflow: hidden;
}

.email__head {
  display: flex;
  flex-direction: column;
  gap: 2px;
  padding: var(--space-3) var(--space-4);
  border-bottom: 1px solid var(--color-ink-line);
  font-size: var(--text-sm);
  color: var(--color-muted-on-dark);
}

.email__body {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
  padding: var(--space-5) var(--space-4);
}

.email__subject {
  font-size: var(--text-lg);
  font-weight: 700;
}

.email__text {
  white-space: pre-line;
  line-height: 1.6;
  color: var(--color-text-on-dark);
}

@media (min-width: 1024px) {
  .detail {
    grid-template-columns: 1fr 2fr;
    gap: var(--space-12);
  }

  .detail__day {
    font-size: 6rem;
  }
}
</style>
