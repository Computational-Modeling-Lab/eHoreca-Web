<template>
  <table class="data_table">
    <tr>
      <th>Id</th>
      <td>{{ heatmap.id }}</td>
    </tr>
    <tr>
      <th>Title</th>
      <td>
        <input type="text" name="title" id="title" v-model="heatmap.title" />
      </td>
    </tr>
    <tr>
      <th>Description</th>
      <td>
        <input
          type="text"
          name="description"
          id="description"
          v-model="heatmap.description"
        />
      </td>
    </tr>
    <tr>
      <th>Valid from</th>
      <td>
        <vue-datetime v-model="heatmap['valid from']"></vue-datetime>
      </td>
    </tr>
    <tr>
      <th>Valid to</th>
      <td>
        <vue-datetime v-model="heatmap['valid to']"></vue-datetime>
      </td>
    </tr>
    <tr>
      <th>Created By</th>
      <td>
        {{ heatmap["created by"] }}
      </td>
    </tr>
    <tr>
      <th>Created at</th>
      <td>
        {{ heatmap["created at"] }}
      </td>
    </tr>
    <tr>
      <th>Updated at</th>
      <td>
        {{ heatmap["updated at"] }}
      </td>
    </tr>
  </table>
</template>

<script>
export default {
  data() {
    return {};
  },
  props: {
    heatmap: {
      type: Object | Array,
      required: true,
    },
  },
};
</script>
