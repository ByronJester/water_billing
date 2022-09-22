<template>
    <table class="w-full">
        <tr class="text-center">
            <th v-for="column in columns" :key="column">
                {{ column }}
            </th>
        </tr>

        <tr class="text-center"
            v-for="(l, index) in rows" :key="l.id"
        >
            <td v-for="(k, i) in keys" :key="i">
                <span>{{ rows[index][k.label] }}</span>
            </td>
        </tr>

    </table>
</template>

<script>
export default {
    props: ['rows', 'columns', 'keys'],
    data() {
        return {

        }
    },
    methods: {

    },

    mounted(){
        
    }
}
</script>

<style scoped>
table {
    border-collapse: collapse;
    border-radius: 5px;
    border-style: hidden;
    box-shadow: 0 0 0 1px #2B4865;
}

td {
    border: 1px solid black;
}

th {
    border: 1px solid black;
    background: #154c79;
    color: white;
}
</style>
