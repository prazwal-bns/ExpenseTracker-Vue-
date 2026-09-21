<script setup>
import { onMounted, ref } from 'vue';

  const expenseCategories = ref([]);
  onMounted(async() =>{
    const response = await fetch('http://localhost:8000/api/categories');
    expenseCategories.value = (await response.json()).data;
  });
</script>

<template>
  <h1 class="text-3xl font-bold underline">Expense Tracker</h1>
  <h2 class="text-2xl font-bold">Expense Categories</h2>
  <ul class="list-disc list-inside">
    <li v-for="category in expenseCategories" :key="category.id">
      {{ category.name }}
    </li>
  </ul>
</template>
