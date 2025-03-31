<template>
  <NuxtImg
    ref="image"
    :src="newImage"
    alt="Book picture"
    loading="eager"
    class="max-w-36 max-h-48 object-cover mb-4"
  />
  <UInput
    id="book_picture"
    type="file"
    accept="image/*"
    @change="changePicture($event)"
  />
</template>

<script lang="ts" setup>
const props = defineProps({
  src: {
    type: String,
    required: true,
  },
});

const imageRef = useTemplateRef<HTMLImageElement>('image');

useEventListener(imageRef, 'contextmenu', (e) => {
  e.preventDefault();
  console.log('clicked');
});

const base64ImageRegex =
  /^data:image\/(png|jpeg|jpg|gif|webp|bmp);base64,[A-Za-z0-9+/]+={0,2}$/;

const newImage = ref<string>('');
const oldImage = ref<string>('');

function updateImage() {
  if (!base64ImageRegex.test(props.src)) {
    newImage.value =
      useRuntimeConfig().public.BASE_URL + '/images/uploads/books/' + props.src;
    oldImage.value = newImage.value;
  } else {
    newImage.value = props.src;
  }
}

onMounted(() => {
  console.log(newImage.value);
  updateImage();
});

watch(
  () => props.src,
  () => {
    updateImage();
  }
);

const emit = defineEmits(['changePicture']);

function changePicture(event: Event) {
  const input = event.target as HTMLInputElement;
  const file = input.files?.[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = () => {
      const base64 = reader.result as string;
      emit('changePicture', base64);
    };
    reader.readAsDataURL(file);
  }
}
</script>

<style></style>
