<template>
  <div>
    <input type="text" v-model="searchQuery" @input="fetchCountries(1)" placeholder="Search by country name...">

    <div @click="setSortOrder('asc')">Official Name (Asc)</div>
    <div @click="setSortOrder('desc')">Official Name (Desc)</div>
    <table>
      <thead>
        <tr>
          <th>Flag</th>
          <th>Official Name</th>
          <th>CCA2</th>
          <th>CCA3</th>
          <th>Native Name</th>
          <th>Alt Spellings</th>
          <th>Calling Codes</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="country in countries" :key="country.cca3">
          <td><img :src="country.flag" :alt="country.officialName" class="flag"></td>
          <td>{{ country.officialName }}</td>
          <td>{{ country.cca2 }}</td>
          <td>{{ country.cca3 }}</td>
          <td>{{ country.nativeName }}</td>
          <td>{{ country.altSpellings.join(', ') }}</td>
          <td>{{ country.callingCodes }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination Controls -->
    <div class="pagination">
      <button @click="fetchCountries(currentPage - 1)" :disabled="currentPage <= 1">Previous</button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
      <button @click="fetchCountries(currentPage + 1)" :disabled="currentPage >= totalPages">Next</button>
    </div>
  </div>
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
    };
  },
  methods: {
    async fetchCountries(page = 1) {
      try {
        const response = await axios.get(`/api/countries?page=${page}&search=${this.searchQuery}&sort=${this.sortOrder}`);

        // const response = await axios.get(`/api/countries?page=${page}`);
        this.countries = response.data.data;
        this.currentPage = response.data.current_page;
        this.perPage = response.data.per_page;
        this.total = response.data.total;
        this.totalPages = response.data.last_page; // Note the change here
      } catch (error) {
        console.error(error);
      }
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

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}

th {
  background-color: #f4f4f4;
}
.country {
  margin-bottom: 20px;
}
.country img {
  width: 100px;
  height: auto;
}

.pagination {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.pagination button {
  margin: 0 10px;
}
</style>
