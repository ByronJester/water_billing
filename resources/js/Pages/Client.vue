<template>
    <Navigation :auth="auth">
        <div class="w-full h-full px-2 py-2">
            <div class="w-full text-2xl pt-5 px-4 cursor-pointer">
                <span @click="back()">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Back
                </span>

                <span class="float-right">
                    {{ client.name }} - {{ client.reference }}
                </span>
            </div>
            
        </div>
    </Navigation>
    
</template>

<script>
import Navigation from '../Layouts/Navigation.vue'
import { Inertia } from '@inertiajs/inertia';

export default {
    props: ['auth', 'options'],
    components: {
        Navigation,
    },
    data() {
        return {
            client: null
        }
    },
    mounted() {
        console.log(this.options)
        this.client = this.options.client
    },

    methods: {
        back(){
            Inertia.get(
                this.$root.route + '/clients' ,
                {
                    onSuccess: () => { },
                },
            );
        }
    }
}

</script>