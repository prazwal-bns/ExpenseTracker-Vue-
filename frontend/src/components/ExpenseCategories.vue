<script setup>
import { onMounted, ref } from 'vue';

  const expenseCategories = ref([]);
  onMounted(async() =>{
    const response = await fetch('http://localhost:8000/api/categories');
    expenseCategories.value = (await response.json()).data;
  });
</script>

<template>
  <h1>Expense Tracker</h1>
  <h2>Expense Categories</h2>
  <ul>
    <li v-for="category in expenseCategories" :key="category.id">
      {{ category.name }}
    </li>
  </ul>
</template>
