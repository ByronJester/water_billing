<template>
     <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
            <div class="w-full flex flex-row mb-3 font-bold mt-5" style="height: 50px; border-bottom: 1px solid black">
                <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'ir'" :class="{'bg-blue-300': activeTab == 'ir' }">
                    INCIDENT REPORT
                </div>

                <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'billing'" :class="{'bg-blue-300': activeTab == 'billing' }">
                    BILLING
                </div>

                <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'payment'" :class="{'bg-blue-300': activeTab == 'payment' }">
                    PAYMENT
                </div>

                <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'reconnection'" :class="{'bg-blue-300': activeTab == 'reconnection' }">
                    ACTIVATED CONNECTION
                </div>

                <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'deactivation'" :class="{'bg-blue-300': activeTab == 'deactivation' }">
                    DEACTIVATED CONNECTION
                </div>
            </div>

            <div class="w-full">
                <span class="float-right mr-2 cursor-pointer" @click="printReport()">
                    <i class="fa-solid fa-print"></i>
                </span>
            </div>

            <div class="w-full mt-2">
                <Table :columns="columns" :rows="rows" :keys="keys" :selected.sync="selected"/>
            </div>

            <VueHtml2pdf
                :show-layout="false"
                :float-layout="true"
                :enable-download="true"
                :preview-modal="true"
                :paginate-elements-by-height="1000"
                :filename="Math.random().toString(36).slice(2)"
                :pdf-quality="2"
                :manual-pagination="false"
                pdf-format="a4"
                pdf-orientation="landscape"
                pdf-content-width="100%"
                ref="report"
            >
                <section slot="pdf-content">
                    <div class="w-full p-5">
                        <table class="w-full">
                            <tr class="text-center">
                                <th v-for="column in columns" :key="column">
                                    {{ column }}
                                </th>
                            </tr>

                            <tr class="text-center"
                                v-for="(l, index) in rows" :key="l.id"
                            >
                                <td v-for="(k, i) in keys" :key="i" class="cursor-pointer">
                                    <span>{{ rows[index][k.label] }}</span>
                                </td>
                            </tr>

                        </table>
                    </div>
                </section>
            </VueHtml2pdf>
        </div>
    </Navigation>
</template>

<script>
import Navigation from '../Layouts/Navigation.vue'
import Table from "../Components/Table";
import Toggle from '../Components/Toggle.vue';
import axios from "axios";
import VueHtml2pdf from 'vue-html2pdf'

export default {
    props: ['auth', 'options'],
    components: {
        Navigation,
        Table,
        Toggle,
        VueHtml2pdf
    },
    data(){
        return {
           activeTab: 'ir',
           selected: null,
           columns: [],
           rows: [],
           keys: []
        }
    },

    watch: {
        activeTab(arg) {
            if(arg == 'ir') {
                this.rows = this.options.ir

                this.columns = [
                    'Name', 'Address', 'Description', 'Status'
                ]

                this.keys = [
                    {
                        label: 'client_name',
                    },
                    {
                        label: 'client_address'
                    },
                    {
                        label: 'description',
                    },
                    {
                        label: 'status',
                    },
                ]
            }

            if(arg == 'billing') {
                this.rows = this.options.clients

                this.columns = [
                    'Name', 'Line #', 'Amount to Pay', 'Penalty', 'Due Date'
                ]

                this.keys = [
                    {
                        label: 'name',
                    },
                    {
                        label: 'reference',
                    },
                    {
                        label: 'amount_to_pay',
                    },
                    {
                        label: 'penalty',
                    },
                    {
                        label: 'due_date'
                    }
                ]
            }

            if(arg == 'payment') {
                this.rows = this.options.clients

                this.columns = [
                    'Name', 'Line #', 'Amount to Pay', 'Status', 'Payment Date'
                ]

                this.keys = [
                    {
                        label: 'name',
                    },
                    {
                        label: 'reference',
                    },
                    {
                        label: 'amount_to_pay',
                    },
                    {
                        label: 'status',
                    },
                    {
                        label: 'payment_date',
                    },
                ]
            }

            if(arg == 'reconnection') {
                this.rows = this.options.clients.filter( x => {return !!x.is_active})
                
                this.columns = [
                    'Name', 'Line #', 'Address'
                ]

                this.keys = [
                    {
                        label: 'name',
                    },
                    {
                        label: 'reference',
                    },
                    {
                        label: 'address',
                    },
                ]
            }

            if(arg == 'deactivation') {
                this.rows = this.options.clients.filter( x => { return!x.is_active})
                
                this.columns = [
                    'Name', 'Line #', 'Address'
                ]

                this.keys = [
                    {
                        label: 'name',
                    },
                    {
                        label: 'reference',
                    },
                    {
                        label: 'address',
                    },
                ]
            }
        }
    },

    created(){
        if(this.activeTab == 'ir') {
            this.rows = this.options.ir

            this.columns = [
                'Name', 'Address', 'Description', 'Status'
            ]

            this.keys = [
                {
                    label: 'client_name',
                },
                {
                    label: 'client_address'
                },
                {
                    label: 'description',
                },
                {
                    label: 'status',
                },
            ]
        }
    },

    methods: {
        printReport(){
            this.$refs.report.generatePdf()
        }
    }
}
</script>

<style scoped>
.--input {
    width: 100%;
    height: 30px;
    border: 1px solid black;
    border-radius: 5px;
    text-align: center;
}

.--btn {
    background: #2B4865;
    border-radius: 10px;
    width: 100%;
    text-align: center;
    color: white;
}

table {
    border-collapse: collapse;
    border-radius: 5px;
    border-style: hidden;
    box-shadow: 0 0 0 1px black;
}

td {
    border: 1px solid black;
    padding-top: 20px;
    padding-bottom: 20px;
}

th {
    border: 1px solid black;
    background: 	navy;
    color: #ffffff;
    padding-top: 20px;
    padding-bottom: 20px;
}

.--active__color {
    background: #B0BEC5;
}
</style>