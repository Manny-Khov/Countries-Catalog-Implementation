<template>
    <div>
      <div v-for="country in countries" :key="country.cca3" class="country">
        <img :src="country.flag" :alt="country.officialName">
        <div>{{ country.officialName }}</div>
        <div>{{ country.cca2 }}</div>
        <div>{{ country.cca3 }}</div>
        <div>{{ country.nativeName }}</div>
        <div>{{ country.altSpellings.join(', ') }}</div>
        <div>{{ country.callingCodes }}</div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        countries: []
      };
    },
    async created() {
      try {
        const response = await axios.get('api/countries');
        this.countries = response.data;
      } catch (error) {
        console.error(error);
      }
    }
  };
  </script>
  
  <style scoped>
  .country {
    margin-bottom: 20px;
  }
  .country img {
    width: 100px;
    height: auto;
  }
  </style>
  