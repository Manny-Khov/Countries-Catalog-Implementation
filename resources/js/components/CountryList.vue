<template>
  <v-container fluid>
    <div class="text-center mt-8 mb-12">
      <h1 class="text-5xl font-bold text-gray-800">Countries Catalog Implementation</h1>
    </div>

    <!-- Search Field -->
    <v-text-field 
      v-model="searchQuery" 
      @input="fetchCountries(1)" 
      label="Search by Country name"
      solo
      clearable
      append-icon="mdi-magnify"
      class="my-4"
    >
    </v-text-field>

    <!-- Sorting Buttons -->
    <v-row>
      <v-col cols="12" class="d-flex justify-start">
        <v-btn color="primary" class="mr-2" @click="setSortOrder('asc')">Sort Asc</v-btn>
        <v-btn color="primary" @click="setSortOrder('desc')">Sort Desc</v-btn>
      </v-col>
    </v-row>

    <!-- Country Flags Grid -->
    <v-row>
      <v-col 
        v-for="country in countries" 
        :key="country.cca3"
        cols="12" sm="6" md="4" lg="3" xl="2"
      >
        <v-card class="d-flex flex-column align-center mb-4">
          <v-img :src="country.flag" height="100px" width="100%"></v-img>
          <v-btn text block color="blue" class="mt-3" @click="showCountryDetails(country)">
            {{ country.officialName }}
          </v-btn>
        </v-card>
      </v-col>
    </v-row>

    <!-- Modal Dialog -->
    <v-dialog
      transition="dialog-bottom-transition"
      v-model="showModal"
      max-width="600px"
    >
      <v-card>
        <v-toolbar color="primary">
          <span class="pl-3 font-bold" v-text="selectedCountry?.officialName"></span>
          <v-spacer></v-spacer>
          <v-btn  @click="showModal = false" class="jutify-end">
            Close
          </v-btn>
        </v-toolbar>
        <v-card-text>
          <div><strong>CCA2:</strong> {{ selectedCountry?.cca2 }}</div>
          <div><strong>CCA3:</strong> {{ selectedCountry?.cca3 }}</div>
          <div><strong>Native Name:</strong> {{ selectedCountry?.nativeName }}</div>
          <div><strong>Alt Spellings:</strong> {{ selectedCountry?.altSpellings.join(', ') }}</div>
          <div><strong>Calling Codes:</strong> {{ selectedCountry?.callingCodes }}</div>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Pagination Controls -->
    <div class="flex justify-center my-5">
      <v-btn @click="fetchCountries(currentPage - 1)" :disabled="currentPage <= 1" color="primary" text>Previous</v-btn>
      <span class="mx-3 my-auto">{{ currentPage }} of {{ totalPages }}</span>
      <v-btn @click="fetchCountries(currentPage + 1)" :disabled="currentPage >= totalPages" color="primary" text>Next</v-btn>
    </div>
  </v-container>
</template>



<script>
import axios from 'axios';

export default {
  data() {
    return {
      countries: [],
      currentPage: 1,
      perPage: 25,
      total: 0,
      totalPages: 0,
      searchQuery: '',
      sortOrder: '',
      showModal: false,
      selectedCountry: null,
    };
  },
  methods: {
    async fetchCountries(page = 1) {
      try {
        const response = await axios.get(`/api/countries?page=${page}&search=${this.searchQuery}&sort=${this.sortOrder}`);
        this.countries = response.data.data;
        this.currentPage = response.data.current_page;
        this.perPage = response.data.per_page;
        this.total = response.data.total;
        this.totalPages = response.data.last_page;
      } catch (error) {
        console.error(error);
      }
    },
    showCountryDetails(country) {
      this.selectedCountry = country;
      this.showModal = true;
    },
    setSortOrder(order) {
      this.sortOrder = order;
      this.fetchCountries(this.currentPage);
    }
  },
  created() {
    this.fetchCountries(this.currentPage);
  }
}
</script>

<style scoped>
.flag {
  width: 50px;
  height: auto;
}
</style>
