<template>
     <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2 flex flex-col">
            <!-- <div class="w-full flex flex-row mb-3 font-bold mt-5" style="height: 50px; border-bottom: 1px solid black">
                <div class="w-full flex justify-center items-center h-full cursor-pointer" @click="activeTab = 'ir'" :class="{'bg-blue-300': activeTab == 'ir' }">
                    SERVICES
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
            </div> -->

            <div class="w-full flex flex-row">
                <div class="w-full">
                    <label class="font-bold text-2xl">
                        Time Frame
                    </label><br>
                    <input type="date" class="mx-1 mt-5" style="border: 1px solid black;  height: 40px" v-model="date.start">
                    -
                    <input type="date" class="mx-1" style="border: 1px solid black;  height: 40px" v-model="date.end">

                    <button style="background: navy; width: 50px; height: 40px" class="text-center text-xs py-2 text-white"
                        @click="filterRows()"
                    >
                        Filter
                    </button>
                </div>

                <div class="w-full">
                    <div class="float-right">
                        <label class="font-bold text-2xl">
                            Report Category
                        </label><br>
                        <select v-model="activeTab" style="width: 150px; border: 1px solid black; height: 40px" class="mt-5 float-right">
                            <option value="billing">Billing</option>
                            <option value="payment">Payment</option>
                            <option value="reconnection">ACTIVATED CONNECTION</option>
                            <option value="deactivation">DEACTIVATED CONNECTION</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="w-full mt-5">
                <span class="float-right mr-2 cursor-pointer" @click="printReport()"> 
                    <i class="fa-solid fa-print"></i>
                </span>
            </div>

            <div class="w-full mt-2">
                <Table :columns="columns" :rows="rows" :keys="keys" :selected.sync="selected"/>
            </div>
            <div class="w-full" v-if="activeTab == 'billing' || activeTab == 'payment'">
                <span class="float-right text-2xl font-bold py-2">
                        Total: ₱ {{ parseFloat(options.total).toFixed(2) }}
                </span>
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
                        <div class="w-full flex justify-center items-center">
                            <img src="/images/logo.png" style="width: 100px; height: 100px"/>
                        </div>

                        <div class="w-full text-xl text-center font-bold">
                            Hydrolite Waterworks and Consumers Association
                        </div>

                        <div class="w-full text-lg text-center font-bold">
                            Brgy. Lumbang Calzada Calaca, Batangas
                        </div>

                        <div class="w-full text-lg font-bold text-center mt-6">
                            {{ getReportLabel(activeTab) }}
                        </div>

                        <table class="w-full mt-4">
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
                        <div class="w-full" v-if="activeTab == 'billing' || activeTab == 'payment'">
                            <span class="float-right text-2xl font-bold py-2">
                                    Total: ₱ {{ parseFloat(options.total).toFixed(2) }}
                            </span>
                        </div>
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
            activeTab: 'billing',
            selected: null,
            columns: [],
            rows: [],
            keys: [],
            date: {
                start: null,
                end: null
            }
        }
    },

    watch: {
        activeTab(arg) {
            if(arg == 'ir') {
                this.rows = this.options.ir

                this.columns = [
                    'Name', 'Address', 'Description', 'Worker', 'Status'
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
                        label: 'worker',
                    },
                    {
                        label: 'status',
                    },
                ]
            }

            if(arg == 'billing') {
                this.rows = this.options.clients.filter( x => { return !!x.is_active })

                this.columns = [
                    'Name', 'Account #', 'Amount to Pay', 'Penalty', 'Due Date'
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
                this.rows = this.options.clients.filter( x => { return !!x.is_active })

                this.columns = [
                    'Name', 'Account #', 'Amount to Pay', 'Status', 'Payment Date'
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
                    }
                ]
            }

            if(arg == 'reconnection') {
                this.rows = this.options.clients.filter( x => {return !!x.is_active})
                
                this.columns = [
                    'Name', 'Account #', 'Address', 'Date Created'
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
                    {
                        label: 'display_created_at'
                    },
                ]
            }

            if(arg == 'deactivation') {
                this.rows = this.options.clients.filter( x => { return !x.is_active})
                
                this.columns = [
                    'Name', 'Account #', 'Address', 'Date Created'
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
                    {
                        label: 'display_created_at'
                    },
                ]
            }
        }
    },

    created(){
        if(this.activeTab == 'billing') {
            this.rows = this.options.clients.filter( x => { return !!x.is_active })

            this.columns = [
                'Name', 'Account #', 'Amount to Pay', 'Penalty', 'Due Date'
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
                },

            ]
        }

        var date = new Date();

        var startDate = date.toISOString().slice(0,10);
        var endDate = date.setDate(date.getDate() + 1);
        endDate = date.toISOString().slice(0,10);

        this.date.start = startDate
        this.date.end = endDate

        this.rows = this.rows.filter(x => {
            var createdAt = new Date(x.created_at);
            return createdAt >= new Date(this.date.start) && createdAt <= new Date(this.date.end);
        })
        
    },

    methods: {
        filterRows(){
            var arg = this.activeTab

            if(arg == 'billing') {
                this.rows = this.options.clients.filter( x => { return !!x.is_active })

                this.columns = [
                    'Name', 'Account #', 'Amount to Pay', 'Penalty', 'Due Date'
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
                this.rows = this.options.clients.filter( x => { return !!x.is_active })

                this.columns = [
                    'Name', 'Account #', 'Amount to Pay', 'Status', 'Payment Date'
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
                    }
                ]
            }

            if(arg == 'reconnection') {
                this.rows = this.options.clients.filter( x => {return !!x.is_active})
                
                this.columns = [
                    'Name', 'Account #', 'Address', 'Date Created'
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
                    {
                        label: 'display_created_at'
                    },
                ]
            }

            if(arg == 'deactivation') {
                this.rows = this.options.clients.filter( x => { return !x.is_active})
                
                this.columns = [
                    'Name', 'Account #', 'Address', 'Date Created'
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
                    {
                        label: 'display_created_at'
                    },
                ]
            }

            this.rows = this.rows.filter(x => {
                var createdAt = new Date(x.created_at);
                return createdAt >= new Date(this.date.start) && createdAt <= new Date(this.date.end);
            })

            console.log(this.rows)

        },
        printReport(){
            this.$refs.report.generatePdf()
        },
        
        getReportLabel(arg){
            if(arg == 'ir') {
                return 'INCIDENT REPORT'
            }

            if(arg == 'billing') {
                return 'BILLING REPORT'
            }

            if(arg == 'payment') {
                return 'PAYMENT REPORT'
            }

            if(arg == 'reconnection') {
                return 'ACTIVATED CONNECTION REPORT'
            }

            if(arg == 'deactivation') {
                return 'DEACTIVATED CONNECTION REPORT'
            }
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