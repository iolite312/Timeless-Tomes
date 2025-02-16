<template>
  <NuxtImg
    ref="image"
    :src="newImage"
    alt="Profile picture"
    loading="eager"
    class="w-24 h-24 rounded-full object-cover"
  />
  <UInput
    id="profile_picture"
    type="file"
    accept="image/*"
    class="hidden"
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

useEventListener(imageRef, 'click', click);
useEventListener(imageRef, 'contextmenu', (e) => {
  //TODO add resizing to image in the future
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
      useRuntimeConfig().public.BASE_URL +
      '/images/' +
      (props.src === 'profile_placeholder.png'
        ? props.src
        : 'uploads/' + props.src);
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

function click() {
  const input = document.getElementById('profile_picture') as HTMLInputElement;
  input.click();
}

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
