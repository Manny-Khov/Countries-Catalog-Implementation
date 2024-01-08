<template>
    <div id="app">
      <country-list></country-list>
    </div>
  </template>
  
  <script>
  import CountryList from './components/CountryList.vue';
  
  export default {
    name: 'App',
    components: {
      CountryList 
    }
  }
  </script>
  
  <style>
  /* Your styles here */
  </style>
  