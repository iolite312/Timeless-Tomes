<!-- eslint-disable vue/html-self-closing -->
<template>
  <div class="w-full">
    <ais-instant-search :search-client="client" index-name="books">
      <ais-search-box class="mb-4">
        <template #default="{ currentRefinement, isSearchStalled, refine }">
          <input
            type="search"
            :value="currentRefinement"
            class="w-full rounded-[calc(var(--ui-radius)*1.5)] border-0 placeholder:text-(--ui-text-dimmed) focus:outline-none disabled:cursor-not-allowed disabled:opacity-75 px-3 py-2 text-base gap-2 text-(--ui-text-highlighted) bg-(--ui-bg) ring ring-inset ring-(--ui-border-accented) focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-(--ui-primary)"
            placeholder="Search..."
            @input="refine($event.currentTarget.value)"
          />
          <span :hidden="!isSearchStalled">Loading...</span>
        </template>
      </ais-search-box>

      <div class="flex flex-row max-height">
        <div class="w-54 border-r-1 border-t-1 p-4 border-[#475569]">
          <ais-refinement-list attribute="genre" />
        </div>
        <div class="flex flex-col flex-1 pt-12 border-t-1 p-4 border-[#475569]">
          <ais-hits class="flex flex-row flex-wrap gap-4">
            <template #default="{ items }">
              <SearchCard v-for="book in items" :key="book.id" :book="book" />
            </template>
          </ais-hits>
        </div>
      </div>
      <div class="flex items-center justify-center">
        <ais-pagination>
          <template #default="{ currentRefinement, nbPages, refine }">
            <p>Page {{ currentRefinement + 1 }} of {{ nbPages }}</p>
            <UPagination
              :total="nbPages * hitsPerPage"
              :items-per-page="hitsPerPage"
              :page="currentRefinement + 1"
              class="min-w-64"
              @update:page="(page) => refine(page - 1)"
            />
          </template>
        </ais-pagination>
        <ais-hits-per-page
          :items="hitsPerPageOptions"
          @change="(event) => (hitsPerPage = event.target.value)"
        />
      </div>
    </ais-instant-search>
    <Placeholder class="h-48 m-4" />
  </div>
</template>

<script setup lang="js">
import {
  AisInstantSearch,
  AisHits,
  AisSearchBox,
  AisRefinementList,
  AisPagination,
  AisHitsPerPage,
} from 'vue-instantsearch/vue3/es';
import { instantMeiliSearch } from "@meilisearch/instant-meilisearch";
import axiosClient from '~/axios';

const { data } = await axiosClient.get('/search');

const client = instantMeiliSearch(
  useRuntimeConfig().public.MEILI_HOST_URL,
  data.key
).searchClient;

const hitsPerPage = ref(8);

const hitsPerPageOptions = [
  { label: '8 hits per page', value: 8, default: true },
  { label: '16 hits per page', value: 16 },
  { label: '32 hits per page', value: 32 },
];
</script>

<style scoped></style>
