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
            <td v-for="(k, i) in keys" :key="i" class="cursor-pointer" 
                :class="{'--active__color': !!selected && selected.id == l.id }"
                @click="selectItem(l)"
            >
                <span>{{ rows[index][k.label] }}</span>
            </td>
        </tr>

    </table>
</template>

<script>
export default {
    props: ['rows', 'columns', 'keys', 'selected'],
    data() {
        return {
        }
    },
    methods: {
        selectItem(arg) {
            this.$emit('update:selected', arg)
        }
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
    box-shadow: 0 0 0 1px black;
}

td {
    border: 1px solid black;
}

th {
    border: 1px solid black;
    background: #0097A7;
    color: black;
}

.--active__color {
    background: #B0BEC5;
}
</style>
