<template>
  <div>
    <form id="payment-form" ref="paymentForms">
      <div id="payment-element">
        <!--Stripe.js injects the Payment Element-->
      </div>
      <UButton id="submit" type="submit">
        <div id="spinner" class="spinner hidden" />
        <span id="button-text">Pay now</span>
      </UButton>
      <div id="payment-message" class="hidden" />
    </form>
  </div>
</template>

<script lang="ts" setup>
import {
  loadStripe,
  type Stripe,
  type StripeElements,
  type StripePaymentElementOptions,
} from '@stripe/stripe-js';

let stripeClient: Stripe;
onBeforeMount(() => {
  loadStripe(useRuntimeConfig().public.STRIPE_PUBLIC_KEY).then((stripe) => {
    if (stripe) {
      stripeClient = stripe;
      initialize();
    }
  });
});
const paymentForm = useTemplateRef<HTMLFormElement>('paymentForms');
onMounted(() => {
  paymentForm.value?.addEventListener('submit', handleSubmit);
});

let elements: StripeElements;

// Fetches a payment intent and captures the client secret
function initialize() {
  const clientSecret = useOrderStore().clientSecret;
  useAccountStore().cart = [];

  elements = stripeClient.elements({ clientSecret: clientSecret });

  const paymentElementOptions: StripePaymentElementOptions = {
    layout: 'accordion',
  };

  const paymentElement = elements.create('payment', paymentElementOptions);
  paymentElement.mount('#payment-element');
}

async function handleSubmit(e: Event) {
  e.preventDefault();

  const { error } = await stripeClient.confirmPayment({
    elements,
    confirmParams: {
      // Make sure to change this to your payment completion page
      return_url: `${window.location.origin}/checkout/complete`,
    },
  });

  if (error.type === 'card_error' || error.type === 'validation_error') {
    // showMessage(error.message);
  } else {
    // showMessage('An unexpected error occurred.');
  }
}
</script>

<style scoped></style>
